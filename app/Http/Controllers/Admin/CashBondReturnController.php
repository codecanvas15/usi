<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Authorization;
use App\Models\Branch;
use App\Models\CashBondReturn;
use App\Models\CashBondReturnDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;

class CashBondReturnController extends Controller
{
    use \App\Http\Helpers\ActivityStatusLogHelper;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder", ['only' => ['index', 'show']]);
        $this->middleware("permission:create $this->view_folder", ['only' => ['create', 'store']]);
        // $this->middleware("permission:edit $this->view_folder", ['only' => ['edit', 'update']]);
        // $this->middleware("permission:delete $this->view_folder", ['only' => ['destroy']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'cash-bond-return';

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                'id',
                'date',
                'code',
                'employess.name',
                'status',
                'created_at'
            ];

            // * get data with date
            $search = $request->input('search.value');
            $query = \App\Models\CashBondReturn::leftJoin('employees', 'employees.id', 'cash_bond_returns.employee_id')
                ->orderByDesc('cash_bond_returns.date')
                ->when(get_current_branch()->is_primary && $request->branch_id, fn($q) => $q->where('cash_bond_returns.branch_id', $request->branch_id))
                ->when(!get_current_branch()->is_primary, fn($q) => $q->where('cash_bond_returns.branch_id', get_current_branch()->id))
                ->when($request->from_date, fn($q) => $q->whereDate('cash_bond_returns.date', '>=', Carbon::parse($request->from_date)))
                ->when($request->to_date, fn($q) => $q->whereDate('cash_bond_returns.date', '<=', Carbon::parse($request->to_date)))
                ->when($request->status, fn($q) => $q->where('cash_bond_returns.status', $request->status))
                ->when($request->employee_id, fn($q) => $q->where('cash_bond_returns.employee_id', $request->employee_id))
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('cash_bond_returns.date', 'like', "%{$search}%")
                            ->orWhere('cash_bond_returns.code', 'like', "%{$search}%")
                            ->orWhere('cash_bond_returns.status', 'like', "%{$search}%")
                            ->orWhere('employees.name', 'like', "%{$search}%")
                            ->orWhere('employees.NIK', 'like', "%{$search}%");
                    });
                });

            // * calculate some data
            $totalData = $query->count();
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $totalFiltered = $query->count();

            $query->select('cash_bond_returns.*',)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir);

            $data = $query->get();

            // * make data table
            $results = array();
            if (!empty($data)) {
                foreach ($data as $key => $cash_bond_return) {
                    $badge = '<div class="badge badge-lg badge-' . cash_bond_return_status()[$cash_bond_return->status]['color'] . '">
                                                ' . cash_bond_return_status()[$cash_bond_return->status]['label'] . ' - ' . cash_bond_return_status()[$cash_bond_return->status]['text'] . '
                                            </div>';

                    $btn = $cash_bond_return->check_available_date;

                    $nestedData['DT_RowIndex'] = $key + 1;
                    $nestedData['id'] = $cash_bond_return->id;
                    $nestedData['date'] = localDate($cash_bond_return->date);
                    $nestedData['code'] = '<a href="' . route("admin.$this->view_folder.index") . '/' . $cash_bond_return->id . '" class="text-primary">' . ($cash_bond_return->bank_code_mutation ?? $cash_bond_return->code) . '</a>' . '<br><a target="_blank" href="' . route('admin.cash-bond-return.export', ['id' => md5($cash_bond_return->id)]) . '" class="btn btn-sm btn-soft btn-info"><i class="fa fa-file-pdf"></i></a>';
                    $nestedData['employee_id'] = "{$cash_bond_return->employee?->name} - {{$cash_bond_return->employee?->NIK}}" ?? "Undefined";
                    $nestedData['status'] = $badge;
                    $nestedData['created_at'] = toDayDateTimeString($cash_bond_return->created_at);
                    $nestedData['action'] =
                        Blade::render('components.datatable.button-datatable', [
                            'row' => $cash_bond_return,
                            'main' => $this->view_folder,
                            'btn_config' => [
                                'detail' => [
                                    'display' => true,
                                ],
                                'edit' => [
                                    'display' => in_array($cash_bond_return->status, ['pending', 'revert']) && $btn,
                                ],
                                'delete' => [
                                    'display' => in_array($cash_bond_return->status, ['pending', 'revert']) && $btn,
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
        if (!$authorization->is_authoirization_exist(CashBondReturn::class)) {
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
            'coa_id' => 'required|exists:coas,id',
            'currency_id' => 'required|exists:currencies,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'exchange_rate' => 'required',

            'cash_bond_ids' => 'required|array',
            'cash_bond_return_amounts' => 'required|array',

            'cash_bond_ids.*' => 'required|exists:cash_bonds,id',
            'cash_bond_return_amounts.*' => 'required|numeric',

            'other_coa_ids' => 'nullable|array',
            'other_amounts' => 'nullable|array',
            'other_debits' => 'nullable|array',
            'other_descriptions' => 'nullable|array',

            'other_coa_ids.*' => 'nullable|exists:coas,id',
            'other_amounts.*' => 'nullable',
            'other_descriptions.*' => 'nullable|string',
        ]);

        // * VARIABLES
        $CREDIT_TOTAL = 0;
        $DEBIT_TOTAL = 0;

        DB::beginTransaction();

        $branch_id = $request->branch_id ?? get_current_branch()->id;
        $branch = Branch::findOrFail($branch_id);

        // ! Create CashBondReturn parent #################################
        $model = new \App\Models\CashBondReturn();
        $branch = Branch::find($request->branch_id);

        $model->fill([
            'branch_id' => $branch_id,
            'project_id' => $request->project_id,
            'employee_id' => $request->employee_id,
            'coa_id' => $request->coa_id,
            'currency_id' => $request->currency_id,
            'code' => generate_code(\App\Models\CashBondReturn::class, 'code', 'date', "CBR", branch_sort: $branch->sort ?? null, date: $request->date),
            'date' => Carbon::parse($request->date),
            'description' => $request->description,
            'exchange_rate' => thousand_to_float($request->exchange_rate),
        ]);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        }

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->route("admin.$this->view_folder.create")->with($this->ResponseMessageCRUD(false, "create", null, $th->getMessage()));
        }

        // ! Create CashBondReturn parent #################################

        // ! Create CashBondReturn details #################################

        // find data cash bon
        $cash_bonds = \App\Models\CashBond::whereIn('id', $request->cash_bond_ids)->get();

        // * set data
        $data_details = [];
        foreach ($cash_bonds as $cash_bond_key => $cash_bond) {
            $cash_bond_cash_advance = $cash_bond->cashBondDetails()->where('type', 'cash_advance')->first();
            if (!$cash_bond_cash_advance) {
                DB::rollback();

                return redirect()->route("admin.$this->view_folder.create")->with($this->ResponseMessageCRUD(false, "create", null, "Cash Bond Debit not found"));
            }

            $outstanding_amount = $cash_bond_cash_advance->debit - $cash_bond->total_returned_amount;

            $data_details[] = [
                'cash_bond_return_id' => $model->id,
                'cash_bond_id' => $cash_bond->id,
                'currency_id' => $model->currency_id,
                'coa_id' => $cash_bond_cash_advance->coa_id,
                'date' => $model->date,
                'transaction_code' => $cash_bond->bank_code_mutation ?? $cash_bond->code,
                'exchange_rate' => $model->exchange_rate,
                'amount' => $cash_bond_cash_advance->debit,
                'amount_to_return' => thousand_to_float($request->cash_bond_return_amounts[$cash_bond_key]),
                'outstanding_amount' => $outstanding_amount,
                'balance' => $outstanding_amount - thousand_to_float($request->cash_bond_return_amounts[$cash_bond_key]),
                'note' => $request->note[$cash_bond_key],
            ];

            $CREDIT_TOTAL += thousand_to_float($request->cash_bond_return_amounts[$cash_bond_key]);
            $DEBIT_TOTAL += thousand_to_float($request->cash_bond_return_amounts[$cash_bond_key]);
        }

        // create CashBondReturnDetails
        try {
            $model->cashBondReturnDetails()->createMany($data_details);
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->route("admin.$this->view_folder.create")->with($this->ResponseMessageCRUD(false, "create", null, $th->getMessage()));
        }
        // ! Create CashBondReturn details #################################

        // ! Create CashBondReturn others #################################

        // * set data
        $data_other = [];
        if (is_array($request->other_coa_ids)) {
            foreach ($request->other_coa_ids as $coa_id_key => $coa_id) {
                $data_other[] = [
                    'cash_bond_return_id' => $model->id,
                    'coa_id' => $coa_id,
                    'amount' => thousand_to_float($request->other_amounts[$coa_id_key]),
                    'description' => $request->other_descriptions[$coa_id_key],
                ];

                if (thousand_to_float($request->other_amounts[$coa_id_key]) < 0) {
                    $CREDIT_TOTAL += thousand_to_float($request->other_amounts[$coa_id_key]);
                    $DEBIT_TOTAL += thousand_to_float($request->other_amounts[$coa_id_key]);
                }
            }
        }

        // * create CashBondReturnOthers
        try {
            $model->cashBondReturnOthers()->createMany($data_other);
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->route("admin.$this->view_folder.create")->with($this->ResponseMessageCRUD(false, "create", null, $th->getMessage()));
        }

        if ($CREDIT_TOTAL != $DEBIT_TOTAL) {
            DB::rollback();

            return redirect()->route("admin.$this->view_folder.create")->with($this->ResponseMessageCRUD(false, "create", null, "Total Debit and Total Credit not same"));
        }

        try {
            $code = generate_bank_code(
                ref_model: CashBondReturn::class,
                ref_id: $model->id,
                coa_id: $model->coa_id,
                type: 'in',
                date: Carbon::parse($request->date),
                is_save: true,
                code: $request->sequence_code,
            );

            if (!$code) {
                DB::rollBack();
                $model->cashBondReturnDetails()->delete();
                $model->cashBondReturnOthers()->delete();
                $model->delete();

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
            model: CashBondReturn::class,
            model_id: $model->id,
            amount: $CREDIT_TOTAL ?? 0,
            title: "Pengembalian Kas Bond",
            subtitle: auth()->user()->name . " mengajukan pengembalian kas bond " . $code,
            link: route('admin.cash-bond-return.show', $model->id),
            update_status_link: route('admin.cash-bond-return.update-status', ['id' => $model->id]),
            division_id: auth()->user()->division_id ?? null
        );

        DB::commit();

        foreach ($model->cashBondReturnDetails as $detail) {
            $this->check_cashbond_outstanding($detail->cash_bond_id);
        }

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
        $model = \App\Models\CashBondReturn::with([
            'branch',
            'project',
            'employee',
            'coa',
            'currency',
            'cashBondReturnDetails.cash_bond',
            'cashBondReturnDetails.currency',
            'cashBondReturnDetails.coa',
            'cashBondReturnOthers.coa',
        ])->findOrFail($id);

        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: CashBondReturn::class,
            model_id: $model->id,
            user_id: auth()->user()->id,
        );
        $authorization_logs['can_revert'] = false;
        $authorization_logs['can_void'] = $model->status == "approve" && $model->check_available_date;
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
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $model = \App\Models\CashBondReturn::with([
            'branch',
            'project',
            'employee',
            'coa',
            'currency',
            'cashBondReturnDetails.cash_bond',
            // 'cashBondReturnDetails.cash_bond.branch',
            // 'cashBondReturnDetails.cash_bond.project',
            // 'cashBondReturnDetails.cash_bond.employee',
            'cashBondReturnDetails.cash_bond.currency',
            // 'cashBondReturnDetails.cash_bond.user',
            'cashBondReturnDetails.cash_bond.cashBondDetails.coa',
            'cashBondReturnDetails.currency',
            'cashBondReturnDetails.coa',
            'cashBondReturnOthers.coa',
        ])->findOrFail($id);

        if (!$model->check_available_date) {
            return abort(403);
        }

        if (!in_array($model->status, ['pending', 'revert']) || !$model->check_available_date) {
            return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(false, "edit", null, "Data tidak dapat diubah karena sudah di approve/reject"));
        }

        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        return view("admin.$this->view_folder.edit", $model, compact('model'));
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
            // 'branch_id' => 'nullable|exists:branches,id',
            // 'project_id' => 'nullable|exists:projects,id',
            // 'employee_id' => 'required|exists:employees,id',
            'coa_id' => 'required|exists:coas,id',
            // 'currency_id' => 'required|exists:currencies,id',
            // 'date' => 'required|date',
            'description' => 'nullable|string',
            'exchange_rate' => 'required',

            'cash_bond_ids' => 'required|array',
            'cash_bond_return_amounts' => 'required|array',

            'cash_bond_ids.*' => 'required|exists:cash_bonds,id',
            'cash_bond_return_amounts.*' => 'required|numeric',

            'other_coa_ids' => 'nullable|array',
            'other_amounts' => 'nullable|array',
            'other_debits' => 'nullable|array',
            'other_descriptions' => 'nullable|array',

            'other_coa_ids.*' => 'nullable|exists:coas,id',
            'other_amounts.*' => 'nullable',
            'other_descriptions.*' => 'nullable|string',
        ]);

        // * VARIABLES
        $CREDIT_TOTAL = 0;
        $DEBIT_TOTAL = 0;

        DB::beginTransaction();

        // ! update CashBondReturn parent

        $model = \App\Models\CashBondReturn::findOrFail($id);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal sudah closing'));
        }

        // * check status
        if (!in_array($model->status, ['pending', 'revert']) || !$model->check_available_date) {
            return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(false, "edit", null, "Data tidak dapat diubah karena sudah di approve/reject"));
        }

        $model->fill([
            'coa_id' => $request->coa_id,
            // 'currency_id' => $request->currency_id,
            'description' => $request->description,
            'exchange_rate' => thousand_to_float($request->exchange_rate),
        ]);

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(false, "edit", null, $th->getMessage()));
        }

        // ! update CashBondReturn parent

        // ! update CashBondReturnDetails
        // find data cash bon
        $cash_bonds = \App\Models\CashBond::whereIn('id', $request->cash_bond_ids)->get();

        // * set data
        $data_details = [];
        foreach ($cash_bonds as $cash_bond_key => $cash_bond) {
            $cash_bond_cash_advance = $cash_bond->cashBondDetails()->where('type', 'cash_advance')->first();
            if (!$cash_bond_cash_advance) {
                DB::rollback();

                return redirect()->route("admin.$this->view_folder.edit", $model)->with($this->ResponseMessageCRUD(false, "edit", null, "Cash Bond Debit not found"));
            }

            $outstanding_amount = $cash_bond_cash_advance->debit - $cash_bond->total_returned_amount;

            $data_details[] = [
                'cash_bond_return_id' => $model->id,
                'cash_bond_id' => $cash_bond->id,
                'currency_id' => $model->currency_id,
                'coa_id' => $cash_bond_cash_advance->coa_id,
                'date' => $model->date,
                'transaction_code' => $cash_bond->bank_code_mutation ?? $cash_bond->code,
                'exchange_rate' => $model->exchange_rate,
                'amount' => $cash_bond_cash_advance->debit,
                'amount_to_return' => thousand_to_float($request->cash_bond_return_amounts[$cash_bond_key]),
                'outstanding_amount' => $outstanding_amount,
                'balance' => $outstanding_amount - thousand_to_float($request->cash_bond_return_amounts[$cash_bond_key]),
                'note' => $request->note[$cash_bond_key],
            ];

            $CREDIT_TOTAL += thousand_to_float($request->cash_bond_return_amounts[$cash_bond_key]);
            $DEBIT_TOTAL += thousand_to_float($request->cash_bond_return_amounts[$cash_bond_key]);
        }

        // create CashBondReturnDetails and delete the old data
        try {
            $model->cashBondReturnDetails()->delete();
            $model->cashBondReturnDetails()->createMany($data_details);

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: CashBondReturn::class,
                model_id: $model->id,
                amount: $CREDIT_TOTAL ?? 0,
                title: "Pengembalian Kas Bond",
                subtitle: auth()->user()->name . " mengajukan pengembalian kas bond " . ($model->bank_code_mutation ?? $model->code),
                link: route('admin.cash-bond-return.show', $model->id),
                update_status_link: route('admin.cash-bond-return.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->route("admin.$this->view_folder.edit", $model)->with($this->ResponseMessageCRUD(false, "edit", null, $th->getMessage()));
        }
        // ! update CashBondReturnDetails

        // ! update CashBondReturnOther
        // * set data
        $data_others = [];
        if (is_array($request->other_coa_ids)) {
            foreach ($request->other_coa_ids as $other_key => $other_coa_id) {
                $data_others[] = [
                    'cash_bond_return_id' => $model->id,
                    'coa_id' => $other_coa_id,
                    'amount' => thousand_to_float($request->other_amounts[$other_key]),
                    'description' => $request->other_descriptions[$other_key],
                ];

                if (thousand_to_float($request->other_amounts[$other_key]) < 0) {
                    $CREDIT_TOTAL += thousand_to_float($request->other_amounts[$other_key]);
                    $DEBIT_TOTAL += thousand_to_float($request->other_amounts[$other_key]);
                }
            }
        }

        // create CashBondReturnOther and delete the old data
        try {
            $model->cashBondReturnOthers()->delete();
            $model->cashBondReturnOthers()->createMany($data_others);
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->route("admin.$this->view_folder.edit", $model)->with($this->ResponseMessageCRUD(false, "edit", null, $th->getMessage()));
        }

        // ! update CashBondReturnOther

        // * validate
        if ($CREDIT_TOTAL != $DEBIT_TOTAL) {
            DB::rollback();

            return redirect()->route("admin.$this->view_folder.edit", $model)->with($this->ResponseMessageCRUD(false, "edit", null, "Total Debit dan Credit tidak sama"));
        }

        DB::commit();

        foreach ($model->cashBondReturnDetails as $detail) {
            $this->check_cashbond_outstanding($detail->cash_bond_id);
        }

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, "edit"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = \App\Models\CashBondReturn::findOrFail($id);

        if (!in_array($model->status, ['pending', 'revert'])) {
            return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(false, "edit", null, "Data tidak dapat diubah karena sudah di approve/reject"));
        }

        DB::beginTransaction();

        try {
            $model->delete();

            Authorization::where('model', CashBondReturn::class)->where('model_id', $model->id)->delete();
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(false, "delete", null, $th->getMessage()));
        }

        DB::commit();

        foreach ($model->cashBondReturnDetails as $detail) {
            $this->check_cashbond_outstanding($detail->cash_bond_id);
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, "delete"));
    }

    /**
     * get cash bond data for creating
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getCashBondForCreate(Request $request)
    {
        $data = \App\Models\CashBond::with(['cashBondDetails.coa', 'currency'])
            ->where('employee_id', $request->employee_id)
            ->where('currency_id', $request->currency_id)
            ->when($request->project_id, function ($query) use ($request) {
                $query->where('project_id', $request->project_id);
            })
            ->when($request->branch_id && get_current_branch()->is_primary, function ($query) use ($request) {
                $query->where('branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                $query->where('branch_id', get_current_branch()->id);
            })
            ->whereIn('status', ['approve', 'partial'])
            ->get();

        return $this->ResponseJsonData($data);
    }

    /**
     * update status
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, $id)
    {
        $model = \App\Models\CashBondReturn::findOrFail($id);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update_status', null, 'Tanggal sudah closing'));
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
                $this->create_activity_status_log(\App\Models\CashBondReturn::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);
                $model->status = $request->status;
                $model->save();
            } else {
                $this->create_activity_status_log(\App\Models\CashBondReturn::class, $id, $request->note ?? $request->message ?? 'message not available', null, $request->status);
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

        foreach ($model->cashBondReturnDetails as $detail) {
            $this->check_cashbond_outstanding($detail->cash_bond_id);
        }

        return redirect()->back()->with($this->ResponseMessageCRUD(true, "edit", "update status"));
    }

    public function check_cashbond_outstanding($id)
    {
        try {
            $cash_bond = \App\Models\CashBond::findOrFail($id);
            $cashbond_detail = $cash_bond->cashBondDetails->where('type', 'cash_advance')->first();
            $cash_bond_amount = $cashbond_detail->debit ?? $cashbond_detail->credit;

            $returned = CashBondReturnDetail::where('cash_bond_id', $id)
                ->whereHas('cash_bond_return', function ($query) {
                    $query->whereIn('status', ['pending', 'revert', 'approve']);
                })
                ->sum('amount_to_return');

            if ($returned == 0) {
                $cash_bond->status = 'approve';
            } else if ($returned < $cash_bond_amount && $returned > 0) {
                $cash_bond->status = 'partial';
            } else {
                $cash_bond->status = 'done';
            }
            $cash_bond->returned_amount = $returned;
            $cash_bond->save();

            return $this->ResponseJsonData($cash_bond);
        } catch (\Throwable $th) {
            throw $th;
            return $this->ResponseJsonData($th->getMessage());
        }
    }

    public function export($id)
    {
        $model = CashBondReturn::whereRaw("MD5(id) = ?", [$id])
            ->with(['cashBondReturnDetails' => function ($query) {
                $query->whereHas('cash_bond', function ($cbr) {
                    $cbr->where('status', 'approve');
                });
            }])
            ->firstOrFail();
        $title = "Pengembalian Kasbon " . $model->code;
        $fileName = $title . '.pdf';
        // return $model;
        $pdf = Pdf::loadview("admin.cash-bond-return.export", compact('model',  'title'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream($fileName);
    }
}
