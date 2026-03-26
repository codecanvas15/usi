<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankInternal as model;
use App\Models\BankInternalDetail;
use App\Models\Coa;
use App\Models\ProfitLossDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use View;

class BankInternalController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder", ['only' => ['index', 'show']]);
        $this->middleware("permission:create $this->view_folder", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit $this->view_folder", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete $this->view_folder", ['only' => ['destroy']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'bank-internal';

    /**
     * search  value
     *
     * @var string|null
     */
    protected string|null $search = null;

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = model::with(['coa'])
                ->where('branch_id', Auth::user()->branch_id)
                ->orderByDesc('bank_internals.created_at')
                ->select('bank_internals.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('nama_bank', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->nama_bank,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('coa.account_code', fn($row) => $row->coa?->account_code ?? "Undefined")
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'btn_config' => [
                            'detail' => [
                                'display' => false,
                            ],
                            'edit' => [
                                'display' => true,
                            ],
                            'delete' => [
                                'display' => true,
                            ],
                        ],
                    ]);
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.' . $this->view_folder . '.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $coa_tree = app('App\Http\Controllers\Admin\CoaController')->tree_view_api();

        $model = [];
        $coa = Coa::find($request->coa_id);

        return view("admin.$this->view_folder.create", compact('coa_tree', 'model', 'coa'));
    }

    public function coaBankInternal(Request $request)
    {
        if ($request->ajax()) {
            $profit_loss_details_coa = ProfitLossDetail::pluck('coa_id');

            $coas = DB::table('coas')
                ->whereNull('coas.deleted_at')
                ->where('is_parent', 0)
                ->whereNotIn('coas.id', $profit_loss_details_coa->toArray())
                ->orderBy('account_code', 'asc')
                ->leftJoin('journal_details', function ($query) {
                    $query->on('journal_details.coa_id', '=', 'coas.id')
                        ->join('journals', 'journals.id', '=', 'journal_details.journal_id')
                        ->where('journals.status', 'approve')
                        ->whereNull('journals.deleted_at');
                })
                ->whereNotNull('journal_details.id');

            if ($request->filled('coa_id')) {
                $coas->where('coas.id', $request->coa_id);
            }

            $coas = $coas->groupBy('coas.id')->selectRaw('coas.*')->get();

            // Get amount before exchanged for each COA
            $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : Carbon::today();

            $amount_before_exchanged = DB::table('journal_details')
                ->whereNotIn('journal_details.coa_id', $profit_loss_details_coa->toArray())
                ->whereIn('journal_details.coa_id', $coas->pluck('id')->toArray())
                ->join('coas', 'coas.id', '=', 'journal_details.coa_id')
                ->join('journals', 'journals.id', '=', 'journal_details.journal_id')
                ->whereNull('journals.deleted_at')
                ->where('journals.status', 'approve')
                ->whereDate('journals.date', '<', $fromDate)
                ->groupBy('coas.id')
                ->selectRaw('
            coas.id,
            COALESCE(SUM(journal_details.debit_exchanged), 0) - COALESCE(SUM(journal_details.credit_exchanged), 0) AS amount_before_exchanged
        ')
                ->get();

            // Fetch bank internals with optional filtering by `nama_bank`
            $bankInternalsQuery = DB::table('bank_internals')
                ->whereIn('coa_id', $coas->pluck('id')->toArray())
                ->whereNull('deleted_at');

            if ($request->filled('bank_name')) {
                $bankInternalsQuery->where('nama_bank', 'LIKE', '%' . $request->bank_name . '%');
            }

            $bankInternals = $bankInternalsQuery->select(
                'id',
                'coa_id',
                'code',
                'type',
                'nama_bank',
                'no_rekening',
                'on_behalf_of',
                'branch_id',
                'created_at',
                'updated_at'
            )->get();

            // Filter COAs that have matching bank internals
            $filteredCoas = $coas->filter(function ($coa) use ($bankInternals) {
                return $bankInternals->where('coa_id', $coa->id)->isNotEmpty();
            });

            // Merge amount_before_exchanged with bank internals & COA
            $result = $filteredCoas->map(function ($coa) use ($amount_before_exchanged, $bankInternals) {
                $amountData = $amount_before_exchanged->where('id', $coa->id)->first();
                $bankInternal = $bankInternals->where('coa_id', $coa->id)->first();

                return [
                    'amount_before_exchanged' => $amountData->amount_before_exchanged ?? 0,
                    'coa' => $coa,
                    'bank_internal' => $bankInternal ? [
                        'id' => $bankInternal->id,
                        'code' => $bankInternal->code,
                        'type' => $bankInternal->type,
                        'nama_bank' => $bankInternal->nama_bank,
                        'no_rekening' => $bankInternal->no_rekening,
                        'on_behalf_of' => $bankInternal->on_behalf_of,
                        'branch_id' => $bankInternal->branch_id,
                        'created_at' => $bankInternal->created_at,
                        'updated_at' => $bankInternal->updated_at
                    ] : null,
                ];
            });

            return response()->json(['data' => $result]);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        // * validate data
        $this->validate($request, [
            'code' => 'required',
            'type' => 'required',
            'nama_bank' => 'required',
            'branch_name' => 'required',
            'no_rekening' => 'required|max:50|string|unique:bank_internals,no_rekening',
            'on_behalf_of' => 'required|max:50|string',
            'coa_id' => 'nullable|exists:coas,id|unique:bank_internals,coa_id',

            'detail_name.*' => 'required',
            'detail_description.*' => 'required',
            'detail_credit_limit.*' => 'required',
            'detail_start_date.*' => 'required|date',
            'detail_end_date.*' => 'required|date',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

        ]);

        // * other validation
        $data = \App\Models\BankInternal::where('no_rekening', $request->no_rekening)
            ->when($request->coa_id, fn($q) => $q->orWhere('coa_id', $request->coa_id))
            ->orWhere('code', $request->code)
            ->first();

        if (!is_null($data)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", 'No Rekening / Nomor Account / Kode Sudah Ada!'));
        }

        // * creating parent data
        $model = new \App\Models\BankInternal();
        $model->fill([
            'code' => $request->code,
            'type' => $request->type,
            'nama_bank' => $request->nama_bank,
            'no_rekening' => $request->no_rekening,
            'on_behalf_of' => $request->on_behalf_of,
            'branch_id' => $request->branch_id,
            'coa_id' => $request->coa_id
        ]);

        try {
            if ($request->hasFile('logo')) {
                $model->logo = $this->upload_file($request->file('logo'), 'bank-internal');
            }

            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", $th->getMessage()));
        }

        // * creating child data
        $data_details = [];
        if (is_array($request->detail_name)) {
            foreach ($request->detail_name as $key => $value) {
                $data_details[] = [
                    'bank_internal_id' => $model->id,
                    'name' => $value ?? null,
                    'description' => $request->detail_description[$key],
                    'credit_limit' => thousand_to_float($request->detail_credit_limit[$key] ?? '0'),
                    'start_date' => $request->detail_start_date[$key] ? \Carbon\Carbon::parse($request->detail_start_date[$key])->format('Y-m-d') : null,
                    'end_date' => $request->detail_end_date[$key] ? \Carbon\Carbon::parse($request->detail_end_date[$key])->format('Y-m-d') : null,
                ];
            }
        }

        try {
            $model->bank_internal_details()->createMany($data_details);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD());
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $int
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $model = model::findOrFail($id);
        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        if ($model->branch_id != Auth::user()->branch_id) {
            return abort(403);
        }

        return view("admin.$this->view_folder.show", compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $model = model::findOrFail($id);
        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        if ($model->branch_id != Auth::user()->branch_id) {
            return abort(403);
        }

        return view("admin.$this->view_folder.edit", compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // * other validation
        $data = \App\Models\BankInternal::where('id', '!=', $id)
            ->where(function ($q) use ($request) {
                $q->where('no_rekening', $request->no_rekening)
                    ->when($request->coa_id, fn($query) => $query->orWhere('coa_id', $request->coa_id))
                    ->orWhere('code', $request->code);
            })
            ->first();

        if (!is_null($data)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "edit", 'No Rekening / Nomor Account / Kode Sudah Ada!'));
        }

        $model = model::findOrFail($id);

        if ($model->branch_id != Auth::user()->branch_id) {
            return abort(403);
        }

        DB::beginTransaction();
        // * validate
        if ($request->ajax()) {
            $this->validate($request, model::rules());
        } else {
            $this->validate_api($request->all(), model::rules());
        }
        // * update data
        $model->loadModel($request->all());

        // * saving and make reponse
        try {
            if ($request->hasFile('logo')) {
                Storage::delete($model->logo);
                $model->logo = $this->upload_file($request->file('logo'), 'bank-internal');
            }
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        BankInternalDetail::where('bank_internal_id', $model->id)->delete();
        if ($request->detail_name) {
            foreach ($request->detail_name as $key => $value) {
                $this->validate_api(
                    [
                        $value,
                        $request->detail_description[$key],
                        $request->detail_credit_limit[$key],
                        $request->detail_start_date[$key],
                        $request->detail_end_date[$key],
                    ],
                    BankInternalDetail::rules()
                );
                BankInternalDetail::create([
                    'bank_internal_id' => $model->id,
                    'name' => $value ?? '',
                    'description' => $request->detail_description[$key] ?? '',
                    'credit_limit' => thousand_to_float($request->detail_credit_limit[$key]) ?? '',
                    'start_date' => $request->detail_start_date[$key] ?? '',
                    'end_date' => $request->detail_end_date[$key] ?? '',
                ]);
            }
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $model = model::findOrFail($id);
        if ($model->branch_id != Auth::user()->branch_id) {
            return abort(403);
        }
        DB::beginTransaction();
        try {
            $model->delete();

            Storage::delete($model->logo);
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'delete', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
        }
        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD(false, 'delete');
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request)
    {
        if ($request->search) {
            $model = model::where('nama_bank', 'like', "%$request->search%")->orderByDesc('created_at')->limit(10)->get();
        } else {
            $model = model::orderByDesc('created_at')->limit(10)->get();
        }

        return $this->ResponseJsonData($model);
    }

    public function detail($id)
    {
        $model = BankInternalDetail::where('bank_internal_id', $id)->get();
        return $this->ResponseJsonData($model);
    }

    public function isNoRekExists(Request $request)
    {
        $model = model::where('no_rekening', $request->no_rekening)->first();

        if ($model) {
            $data['is_exists'] = true;
        } else {
            $data['is_exists'] = false;
        }

        return response()->json($data);
    }
}
