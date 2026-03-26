<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\NotificationHelper;
use App\Models\Authorization;
use App\Models\Branch;
use App\Models\CashBond;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CashBondController extends Controller
{
    use \App\Http\Helpers\ActivityStatusLogHelper;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder", ['only' => ['index', 'show'], 'export']);
        $this->middleware("permission:create $this->view_folder", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit $this->view_folder", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete $this->view_folder", ['only' => ['destroy']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'cash-bond';

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                'cash_bonds.id',
                'code',
                'date',
                'employee_id',
                'cash_bond_details.credit',
                'description',
                'status',
                'created_at',
                'cash_bonds.id',
            ];

            // * get data with date
            $search = $request->input('search.value');
            $query = \App\Models\CashBond::with(['branch', 'employee'])
                ->join('employees', 'employees.id', '=', 'cash_bonds.employee_id')
                ->join('cash_bond_details', function ($join) {
                    $join->on('cash_bond_details.cash_bond_id', '=', 'cash_bonds.id')
                        ->where('cash_bond_details.type', '=', 'cash_bank')
                        ->whereNull('cash_bond_details.deleted_at');
                })
                ->leftJoin('bank_code_mutations', function ($q) {
                    $q->on('cash_bonds.id', 'bank_code_mutations.ref_id')
                        ->where('bank_code_mutations.ref_model', 'App\Models\CashBond');
                })
                ->when($request->employee_id, fn($q) => $q->where('cash_bonds.employee_id', $request->employee_id))
                ->when(get_current_branch()->is_primary && $request->branch_id, fn($q) => $q->where('cash_bonds.branch_id', $request->branch_id))
                ->when(!get_current_branch()->is_primary, fn($q) => $q->where('cash_bonds.branch_id', get_current_branch()->id))
                ->when($request->from_date, fn($q) => $q->whereDate('cash_bonds.created_at', '>=', Carbon::parse($request->from_date)))
                ->when($request->to_date, fn($q) => $q->whereDate('cash_bonds.created_at', '<=', Carbon::parse($request->to_date)))
                ->when($request->search, function ($q) use ($search) {
                    $q->where(function ($q) use ($search) {
                        $q->where('cash_bonds.date', 'like', "%{$search}%")
                            ->orWhere('cash_bonds.code', 'like', "%{$search}%")
                            ->orWhere('cash_bonds.status', 'like', "%{$search}%")
                            ->orWhere('employees.name', 'like', "%{$search}%")
                            ->orWhere('employees.NIK', 'like', "%{$search}%")
                            ->orWhere('bank_code_mutations.code', 'like', "%{$search}%");
                    });
                })
                ->when($request->status, fn($q) => $q->where('cash_bonds.status', $request->status));

            // * calculate some data
            $totalData = $query->count();
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $totalFiltered = $query->count();

            $query->select('cash_bonds.*', 'cash_bond_details.credit')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir);

            $data = $query->get();

            // * make data table
            $results = array();
            if (!empty($data)) {
                foreach ($data as $key => $cash_bond) {
                    $badge = '<div class="badge badge-lg badge-' . cash_bond_status()[$cash_bond->status]['color'] . '">
                                            ' . cash_bond_status()[$cash_bond->status]['label'] . ' - ' . cash_bond_status()[$cash_bond->status]['text'] . '
                                        </div>';

                    $btn = $cash_bond->check_available_date;

                    $nestedData['DT_RowIndex'] = $key + 1;
                    $nestedData['date'] = localDate($cash_bond->date);
                    $nestedData['code'] = '<a href="' . route("admin.$this->view_folder.index") . '/' . $cash_bond->id . '" class="text-primary">' . ($cash_bond->bank_code_mutation ?? $cash_bond->code) . '</a>' . '<br><a target="_blank" href="' . route('admin.cash-bond.export', ['id' => md5($cash_bond->id)]) . '" class="btn btn-sm btn-soft btn-info"><i class="fa fa-file-pdf"></i></a>';
                    $nestedData['employee_id'] = $cash_bond->employee?->name;
                    $nestedData['status'] = $badge;
                    $nestedData['credit'] = formatNumber($cash_bond->credit);
                    $nestedData['created_at'] = toDayDateTimeString($cash_bond->created_at);
                    $nestedData['description'] = $cash_bond->description;
                    $nestedData['action'] = Blade::render('components.datatable.button-datatable', [
                        'row' => $cash_bond,
                        'main' => $this->view_folder,
                        'btn_config' => [
                            'detail' => [
                                'display' => true,
                            ],
                            'edit' => [
                                'display' => in_array($cash_bond->status, ['pending', 'revert']) && $btn,
                            ],
                            'delete' => [
                                'display' => in_array($cash_bond->status, ['pending', 'revert']) && $btn,
                            ],
                        ],
                    ]);
                    $results[] = $nestedData;
                }
            }

            return $this->ResponseJson([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered ?? $totalData),
                "data" => $results,
            ]);
        }

        return view("admin.$this->view_folder.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(CashBond::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

        return view("admin.$this->view_folder.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'branch_id' => 'nullable|exists:branches,id',
            'project_id' => 'nullable|exists:projects,id',
            'employee_id' => 'required|exists:employees,id',
            'currency_id' => 'required|exists:currencies,id',
            'date' => 'required|date',
            'reference' => 'nullable|string',
            'exchange_rate' => 'nullable',
            'description' => 'nullable|string',

            'type' => 'array',
            'coa_id' => 'array',
            'credit' => 'array',
            'debit' => 'array',
            'note' => 'array',

            'type.*' => 'required|in:cash_bank,cash_advance,other',
            'coa_id.*' => 'required|exists:coas,id',
            'credit.*' => 'nullable',
            'debit.*' => 'nullable',
            'note.*' => 'nullable|string',
        ]);

        DB::beginTransaction();

        $branch_id = $request->branch_id ?? Auth::user()->branch_id;
        $branch = Branch::find($branch_id);

        // * create cash bond
        $model = new \App\Models\CashBond();

        $model->fill([
            'branch_id' => $branch_id,
            'project_id' => $request->project_id,
            'employee_id' => $request->employee_id,
            'currency_id' => $request->currency_id,
            'code' => generate_code(\App\Models\CashBond::class, 'code', 'date', "CB", date: $request->date, branch_sort: $branch->sort ?? null),
            'date' => Carbon::parse($request->date) ?? Carbon::now()->format('Y-m-d'),
            'description' => $request->description,
            'exchange_rate' => thousand_to_float($request->exchange_rate ?? 1),
            'description' => $request->description,
        ]);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        }

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", exception_message: $th->getMessage()));
        }

        // * create cash bond detail
        $detail_data = [];
        if (is_array($request->coa_id)) {
            foreach ($request->coa_id as $key => $value) {
                $detail_data[] = [
                    'cash_bond_id' => $model->id,
                    'type' => $request->type[$key] ?? null,
                    'coa_id' => $value,
                    'credit' => thousand_to_float($request->credit[$key] ?? 0),
                    'debit' => thousand_to_float($request->debit[$key] ?? 0),
                    'note' => $request->note[$key] ?? $request->note[1] ?? null,
                ];
            }
        }

        // * validate credit debit must be balance or same
        $total_credit = array_sum(array_column($detail_data, 'credit'));
        $total_debit = array_sum(array_column($detail_data, 'debit'));

        if ($total_credit != $total_debit) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", exception_message: "Total Credit and Debit must be balance"));
        }

        // * create cash bond detail
        try {
            $model->cashBondDetails()->createMany($detail_data);

            try {
                $code = generate_bank_code(
                    ref_model: CashBond::class,
                    ref_id: $model->id,
                    coa_id: $model->cashBondDetails->where('type', 'cash_bank')->first()->coa_id,
                    type: 'out',
                    date: Carbon::parse($request->date),
                    is_save: true,
                    code: $request->sequence_code,
                );

                if (!$code) {
                    DB::rollBack();
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'generate kode error, periksa bank internal coa kas/bank yang dipilih'));
                }
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", exception_message: $th->getMessage()));
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: CashBond::class,
                model_id: $model->id,
                amount: $total_credit ?? 0,
                title: "Kas Bond",
                subtitle: auth()->user()->name . " mengajukan kas bond " . $code,
                link: route('admin.cash-bond.show', $model->id),
                update_status_link: route('admin.cash-bond.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );

            $notification = new NotificationHelper();
            $notification->send_notification(
                branch_id: $model->branch_id,
                user_id: null,
                roles: [],
                permissions: ['approve ' . $this->view_folder],
                title: "KASBON BARU",
                body: $model->code . ' - ' . $model->employee->name,
                reference_model: get_class($model),
                reference_id: $model->id,
                link: route("admin.{$this->view_folder}.show", $model),
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", exception_message: $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, "create"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = \App\Models\CashBond::findOrFail($id);

        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: CashBond::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = $model->status == "approve" && $model->check_available_date;
        $authorization_logs['can_void_request'] = $model->status == "approve" && $model->check_available_date;
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'auth_revert_void_button'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $model = \App\Models\CashBond::with([
            'branch',
            'project',
            'employee',
            'currency',
            'user',
            'cashBondDetails.coa',
        ])->findOrFail($id);

        if (!$model->check_available_date) {
            return abort(403);
        }

        if (!in_array($model->status, ['pending', 'revert']) || !$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "edit", exception_message: "Data tidak dapat diedit karena status masih {$model->status}"));
        }

        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        $parentCurrency = $model->currency;
        $cashBankCoa = $model->cashBondDetails->where('type', 'cash_bank')->first()->coa;
        $cashBankCurrency = $cashBankCoa->currency_id ? $cashBankCoa->currency : get_local_currency();

        return view("admin.$this->view_folder.edit", compact('model', 'parentCurrency', 'cashBankCurrency'));
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
        $this->validate($request, [
            'employee_id' => 'required|exists:employees,id',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable',
            'description' => 'nullable|string',

            'type' => 'array',
            'coa_id' => 'array',
            'credit' => 'array',
            'debit' => 'array',
            'note' => 'array',

            'type.*' => 'required|in:cash_bank,cash_advance,other',
            'coa_id.*' => 'required|exists:coas,id',
            'credit.*' => 'nullable',
            'debit.*' => 'nullable',
            'note.*' => 'nullable|string',
        ]);

        DB::beginTransaction();

        $model = \App\Models\CashBond::findOrFail($id);

        if (!in_array($model->status, ['pending', 'revert']) || !$model->check_available_date) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "update", exception_message: "Data tidak dapat diedit karena status masih {$model->status}"));
        }

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal sudah closing'));
        }

        // * update cash bond
        $model->fill([
            'employee_id' => $request->employee_id,
            'currency_id' => $request->currency_id,
            'exchange_rate' => thousand_to_float($request->exchange_rate),
            'description' => $request->description,
        ]);

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "update", exception_message: $th->getMessage()));
        }

        // * update cash bond details
        $detail_data = [];
        if (is_array($request->coa_id)) {
            foreach ($request->coa_id as $key => $value) {
                $detail_data[] = [
                    'type' => $request->type[$key] ?? null,
                    'coa_id' => $value,
                    'credit' => thousand_to_float($request->credit[$key] ?? 0),
                    'debit' => thousand_to_float($request->debit[$key] ?? 0),
                    'note' => $request->note[$key] ?? $request->note[1] ?? null,
                ];
            }
        }

        // * validate credit debit must be balance or same
        $total_credit = array_sum(array_column($detail_data, 'credit'));
        $total_debit = array_sum(array_column($detail_data, 'debit'));

        if ($total_credit != $total_debit) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "update", exception_message: "Total Debit dan Total Kredit tidak sama"));
        }

        // * delete all cash bond details and create new cash bond details
        try {
            $model->cashBondDetails()->delete();
            $model->cashBondDetails()->createMany($detail_data);

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: CashBond::class,
                model_id: $model->id,
                amount: $total_credit ?? 0,
                title: "Kas Bond",
                subtitle: auth()->user()->name . " mengajukan kas bond " . ($model->bank_code_mutation ?? $model->code),
                link: route('admin.cash-bond.show', $model->id),
                update_status_link: route('admin.cash-bond.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "update", exception_message: $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, "update"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = \App\Models\CashBond::findOrFail($id);

        if (!in_array($model->status, ['pending', 'revert'])) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "delete", exception_message: "Data tidak dapat dihapus karena status masih {$model->status}"));
        }

        DB::beginTransaction();

        try {
            $model->delete();

            Authorization::where('model', CashBond::class)->where('model_id', $model->id)->delete();
        } catch (\Throwable $th) {

            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "delete", exception_message: $th->getMessage()));
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, "delete"));
    }

    /**
     * update_status
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function update_status(Request $request, $id)
    {
        $model = \App\Models\CashBond::findOrFail($id);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, 'Tanggal sudah closing'));
        }

        validate_branch($model->branch_id);

        DB::beginTransaction();

        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(\App\Models\CashBond::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);
                $model->fill([
                    'status' => $request->status,
                    'reject_reason' => $request->message,
                ]);
                $model->save();
            } else {
                $this->create_activity_status_log(\App\Models\CashBond::class, $id, $request->note ?? $request->message ?? 'message not available', null, $request->status);
            }

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );
        } catch (\Throwable $th) {

            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "edit", "update status", null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, "edit", "update status"));
    }

    public function export($id)
    {
        $model = CashBond::whereRaw("MD5(id) = ?", [$id])
            ->with(['cashBondReturnDetails' => function ($query) {
                $query->whereHas('cash_bond_return', function ($cbr) {
                    $cbr->where('status', 'approve');
                });
            }])
            ->firstOrFail();
        $title = "Kasbon " . $model->code;
        $fileName = $title . '.pdf';
        $pdf = Pdf::loadview("admin.cash-bond.export", compact('model',  'title'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream($fileName);
    }
}
