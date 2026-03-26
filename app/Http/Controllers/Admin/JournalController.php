<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\Authorization;
use App\Models\Journal as model;
use App\Models\Journal;
use App\Models\JournalDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class JournalController extends Controller
{
    use ActivityStatusLogHelper;

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
    protected string $view_folder = 'journal';

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
        $journals = DB::table('journals')->whereNull('journals.deleted_at')->get();
        $journalTypes = $journals->unique('journal_type')->pluck('journal_type')->toArray();

        if ($request->ajax()) {
            $data = model::orderByDesc('created_at')->select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('date', fn($row) => localDate($row->date))
                ->editColumn('reference', function ($row) {
                    return $row->document_reference['code'] ?? '';
                })
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . journal_status()[$row->status]['color'] . '">
                                ' . journal_status()[$row->status]['label'] . ' - ' . journal_status()[$row->status]['text'] . '
                            </div>';

                    return $badge;
                })
                ->editColumn('credit_total', fn($row) => formatNumber($row->credit_total))
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
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.' . $this->view_folder . '.index', compact('journalTypes'));
    }

    /**
     * data
     *
     * @param \Illuminate\Http\Request  $request
     * @param boolean|null $is_generated
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        // return $request->is_generated;
        if ($request->ajax()) {
            $data = model::where('is_generated', $request->is_generated);

            if ($request->type) {
                $data->where('journal_type', $request->type);
            }

            if ($request->from_date) {
                $data->whereDate('date', '>=', Carbon::parse($request->from_date));
            }
            if ($request->to_date) {
                $data->whereDate('date', '<=', Carbon::parse($request->to_date));
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('date', fn($row) => localDate($row->date))
                ->editColumn('reference', function ($row) {
                    if (!$row->is_generated) {
                        return $row->reference ?? '';
                    }
                    return $row->document_reference['code'] ?? '';
                })
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . journal_status()[$row->status]['color'] . '">
                                ' . journal_status()[$row->status]['label'] . ' - ' . journal_status()[$row->status]['text'] . '
                            </div>';

                    return $badge;
                })
                ->editColumn('credit_total', fn($row) => formatNumber($row->credit_total))
                ->addColumn('action', function ($row) use ($request) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'btn_config' => [
                            'detail' => [
                                'display' => false,
                            ],
                            'edit' => [
                                'display' => !$request->is_generated && in_array($row->status, ['revert', 'pending']),
                            ],
                            'delete' => [
                                'display' => !$request->is_generated && in_array($row->status, ['revert', 'pending']),
                            ],
                        ],
                    ]);
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $model = [];
        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(model::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

        return view("admin.$this->view_folder.create", compact('model'));
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

        // * validate
        if ($request->ajax()) {
            $this->validate_api($request->all(), array_merge(model::rules(), [
                'account_id.*' => 'required|exists:coas,id',
                'debit.*' => 'nullable',
                'credit.*' => 'nullable',
                'remark.*' => 'nullable',
            ]));
        } else {
            $this->validate($request, array_merge(model::rules(), [
                'account_id.*' => 'required|exists:coas,id',
                'debit.*' => 'nullable',
                'credit.*' => 'nullable',
                'remark.*' => 'nullable',
            ]));
        }

        $last_data = model::whereMonth('date', Carbon::parse($request->date))
            ->whereYear('date', Carbon::parse($request->date))
            ->orderBy('id', 'desc')
            ->withTrashed()
            ->first();

        $new_code = generate_code_transaction_with_out_branch("JOUR", $last_data->code ?? "0000-0000-0000-0000", date: $request->date);

        // while umtil get unique code
        $continue = true;
        do {
            $last_data = model::withTrashed()
                ->where('code', $new_code)->first();
            if ($last_data) {
                $new_code = generate_code_transaction_with_out_branch("JOUR", $last_data->code ?? "0000-0000-0000-0000", date: $request->date);
            } else {
                $continue = false;
            }
        } while ($continue);

        // * create data
        $model = new model();
        $model->loadModel([
            'code' => $new_code,
            'date' => Carbon::parse($request->date),
            'document_reference' => [],
            'remark' => $request->remark,
            'exchange_rate' => thousand_to_float($request->exchange_rate ?? 0),
            'currency_id' => $request->currency_id,
            'reference' => $request->reference,
        ]);


        // * saving and make reponse
        try {
            $model->save();
            $document_reference = [
                'id' => $model->id,
                'model' => Journal::class,
                'code' => $model->code,
                'link' => route('admin.journal.show', ['journal' => $model->id]),
            ];

            $model->update(
                [
                    'document_reference' => $document_reference,
                ]
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        // * make journal details
        $debit = 0;
        $credit = 0;
        foreach ($request->account_id as $key => $value) {
            $credit += thousand_to_float($request->credit[$key] ?? 0);
            $debit += thousand_to_float($request->debit[$key] ?? 0);

            $model->journal_details()->create([
                'journal_id' => $model->id,
                'coa_id' => $value,
                'debit' => thousand_to_float($request->debit[$key] ?? 0),
                'credit' => thousand_to_float($request->credit[$key] ?? 0),
                'remark' => $request->remark_detail[$key],
            ]);
        }

        // * update credit & debit
        $model->credit_total = $credit;
        $model->debit_total = $debit;

        // *saving
        try {
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->credit_total ?? 0,
                title: "Jurnal",
                subtitle: Auth::user()->name . " mengajukan Jurnal " . $model->code,
                link: route('admin.journal.show', ['journal' => $model->id]),
                update_status_link: route('admin.journal.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

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

        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: model::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );

        $authorization_logs['can_revert'] = !$model->is_generated && $model->status == 'approve' && checkAvailableDate($model->date);
        $authorization_logs['can_void'] = !$model->is_generated && $model->status == 'approve' && checkAvailableDate($model->date);
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] =  !$model->is_generated && $model->status == 'approve' && checkAvailableDate($model->date);
        $authorization_logs['can_void_request'] =  !$model->is_generated && $model->status == 'approve' && checkAvailableDate($model->date);
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'auth_revert_void_button'));
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
        $model = model::findOrFail($id);
        DB::beginTransaction();
        // * validate
        if ($request->ajax()) {
            $this->validate($request, array_merge(model::rules(), [
                'account_id.*' => 'required|exists:coas,id',
                'debit.*' => 'nullable',
                'credit.*' => 'nullable',
                'remark.*' => 'nullable',
            ]));
        } else {
            $this->validate_api($request->all(), array_merge(model::rules(), [
                'account_id.*' => 'required|exists:coas,id',
                'debit.*' => 'nullable',
                'credit.*' => 'nullable',
                'remark.*' => 'nullable',
            ]));
        }

        if (Carbon::parse($request->date) != $model->date) {
            $last_data = model::whereMonth('date', Carbon::parse($request->date))
                ->whereYear('date', Carbon::parse($request->date))
                ->orderBy('id', 'desc')
                ->withTrashed()
                ->first();

            $new_code = generate_code_transaction_with_out_branch("JOUR", $last_data->code ?? "0000-0000-0000-0000", date: $request->date);

            // while umtil get unique code
            $continue = true;
            do {
                $last_data = model::withTrashed()
                    ->where('code', $new_code)->first();
                if ($last_data) {
                    $new_code = generate_code_transaction_with_out_branch("JOUR", $last_data->code ?? "0000-0000-0000-0000", date: $request->date);
                } else {
                    $continue = false;
                }
            } while ($continue);

            $model->code = $new_code;
        }

        // * update data
        $model->loadModel([
            'date' => Carbon::parse($request->date),
            'reference' => $request->reference,
            'remark' => $request->remark,
            'exchange_rate' => thousand_to_float($request->exchange_rate ?? 0),
            'journal_type' => $request->journal_type,
            'currency_id' => $request->currency_id,
        ]);

        // * saving and make reponse
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        // * updating journal details
        $model->journal_details()->delete();
        $debit = 0;
        $credit = 0;
        foreach ($request->account_id as $key => $value) {
            $credit += thousand_to_float($request->credit[$key] ?? 0);
            $debit += thousand_to_float($request->debit[$key] ?? 0);

            $model->journal_details()->create([
                'journal_id' => $model->id,
                'coa_id' => $value,
                'debit' => thousand_to_float($request->debit[$key] ?? 0),
                'credit' => thousand_to_float($request->credit[$key] ?? 0),
                'remark' => $request->remark_detail[$key],
            ]);
        }

        // * update credit & debit
        $model->credit_total = $credit;
        $model->debit_total = $debit;

        // *saving
        try {
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->credit_total ?? 0,
                title: "Jurnal",
                subtitle: Auth::user()->name . " mengajukan Jurnal " . $model->code,
                link: route('admin.journal.show', ['journal' => $model->id]),
                update_status_link: route('admin.journal.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
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
        DB::beginTransaction();
        try {
            JournalDetail::where('journal_id', $model->id)
                ->get()
                ->each(function ($item) {
                    $item->delete();
                });

            $model->delete();

            Authorization::where('model', model::class)->where('model_id', $model->id)->delete();
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
        // if ($request->search) {
        //     $model = model::where('nama', 'like', "%$request->search%")->orderByDesc('created_at')->limit(10)->get();
        // } else {
        //     $model = model::orderByDesc('created_at')->limit(10)->get();
        // }

        // return $this->ResponseJsonData($model);
    }

    /**
     * update status
     *
     * @param Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, $id)
    {
        $model = model::findOrfail($id);

        // * if status is approved and credit total and debit total is not same
        if ($request->status == 'approved') {
            if ($model->credit_total != $model->debit_total) {
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'update', null, 'Total Debit dan Total Kredit tidak sama', 422);
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Total Debit dan Total Kredit tidak sama'));
            }
        }

        if (in_array($request->status, ['approve', 'revert', 'void']) && !checkAvailableDate($model->date)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Periode tanggal telah ditutup'));
        }

        // * saving and make reponse
        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);
                // * update status
                $model->status = $request->status;
                $model->save();
            } else {
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', null, $request->status);
            }

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );
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

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    public function update_ordering()
    {
        DB::beginTransaction();
        try {
            DB::table('journal_details')->update(['ordering' => null]);

            $journal_details = DB::table('journal_details')
                ->join('journals', 'journals.id', '=', 'journal_details.journal_id')
                ->orderBy('journals.date', 'asc')
                ->orderBy('journal_details.id', 'asc')
                ->select(
                    'journal_details.*',
                    'journals.date',
                )
                ->get();

            foreach ($journal_details as $key => $journal_detail) {
                DB::table('journal_details')->where('id', $journal_detail->id)->update(['ordering' => generate_journal_order($journal_detail->date)]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function update_stock_mutation_order()
    {
        DB::beginTransaction();
        try {
            DB::table('stock_mutations')->update(['ordering' => null]);

            $stock_mutations = DB::table('stock_mutations')
                ->orderBy('date', 'asc')
                ->orderBy('id', 'asc')
                ->select(
                    'stock_mutations.*',
                )
                ->get();

            foreach ($stock_mutations as $key => $stock_mutation) {
                DB::table('stock_mutations')->where('id', $stock_mutation->id)->update(['ordering' => generate_stock_mutation_order($stock_mutation->date)]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function get_data(Request $request)
    {
        $journal = Journal::with('journal_details.coa')->where('reference_model', $request->model)
            ->where('reference_id', $request->id)
            ->first();

        return $this->ResponseJson($journal);
    }

    public function regenerate_code()
    {
        $journals = DB::table('journals')->update(['code' => NULL]);

        $journals = DB::table('journals')->get();
        foreach ($journals as $key => $journal) {
            $last_data = model::whereMonth('date', Carbon::parse($journal->date))
                ->whereYear('date', Carbon::parse($journal->date))
                ->orderBy('id', 'desc')
                ->withTrashed()
                ->whereNotNull('code')
                ->first();

            $new_code = generate_code_transaction_with_out_branch("JOUR", ($last_data->code ?? "0000-0000-0000-0000"), date: $journal->date);

            $journal = model::withTrashed()->find($journal->id);
            $journal->code = $new_code;
            $journal->save();
        }
    }
}
