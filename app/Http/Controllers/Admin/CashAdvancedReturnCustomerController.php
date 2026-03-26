<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Authorization;
use App\Models\Branch;
use App\Models\CashAdvancedReturn;
use App\Models\CashAdvancedReturnInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Google\Service\Bigquery\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CashAdvancedReturnCustomerController extends Controller
{
    use \App\Http\Helpers\ActivityStatusLogHelper;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view cash-advance-return", ['only' => ['index', 'show']]);
        $this->middleware("permission:create cash-advance-return", ['only' => ['create', 'store']]);
        // $this->middleware("permission:edit $this->view_folder", ['only' => ['edit', 'update']]);
        // $this->middleware("permission:delete $this->view_folder", ['only' => ['destroy']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'cash-advance-return-customer';

    /**
     * search  value
     *
     * @var string|null
     */
    protected string|null $search = null;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                'id',
                'date',
                'code',
                'customer.nama',
                'reference_id',
                'status',
                'created_at'
            ];

            // * get data with date
            $search = $request->input('search.value');
            $query = \App\Models\CashAdvancedReturn::leftJoin('customers', 'customers.id', 'cash_advanced_returns.reference_id')
                ->orderByDesc('created_at')
                ->where('cash_advanced_returns.type', 'customer')
                ->when(get_current_branch()->is_primary && $request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
                ->when(!get_current_branch()->is_primary, fn($q) => $q->where('branch_id', get_current_branch()->id))
                ->when($request->from_date, fn($q) => $q->whereDate('cash_advanced_returns.created_at', '>=', Carbon::parse($request->from_date)))
                ->when($request->to_date, fn($q) => $q->whereDate('cash_advanced_returns.created_at', '<=', Carbon::parse($request->to_date)))
                ->when($request->status, fn($q) => $q->where('status', $request->status))
                ->when($request->reference_id, fn($q) => $q->where('reference_id', $request->reference_id))
                ->when($search, function ($q) use ($search) {
                    $q->where('cash_advanced_returns.date', 'like', "%{$search}%")
                        ->orWhere('cash_advanced_returns.code', 'like', "%{$search}%")
                        ->orWhere('cash_advanced_returns.status', 'like', "%{$search}%")
                        ->orWhere('customers.nama', 'like', "%{$search}%");
                });

            // * calculate some data
            $totalData = $query->count();
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $totalFiltered = $query->count();

            $query->select('cash_advanced_returns.*',)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir);

            $data = $query->get();

            // * make data table
            $results = array();
            if (!empty($data)) {
                foreach ($data as $key => $cash_advance_return) {
                    $badge = '<div class="badge badge-lg badge-' . cash_advance_return()[$cash_advance_return->status]['color'] . '">
                                            ' . cash_advance_return()[$cash_advance_return->status]['label'] . ' - ' . cash_advance_return()[$cash_advance_return->status]['text'] . '
                                        </div>';

                    $export_route = route("cash-advance-return-customer.export", ['id' => encryptId($cash_advance_return->id)]);
                    $nestedData['DT_RowIndex'] = $key + 1;
                    $nestedData['id'] = $cash_advance_return->id;
                    $nestedData['date'] = localDate($cash_advance_return->date);
                    $nestedData['code'] = '<a href="' . route("admin.$this->view_folder.index") . '/' . $cash_advance_return->id . '" class="text-primary">' . $cash_advance_return->code . '</a>' . '<br/>' . '<a target="_blank" href="' . $export_route . '" class="btn btn-sm btn-soft btn-info" onclick="show_print_out_modal(event)"><i class="fa fa-file-pdf"></i></a>';
                    $nestedData['reference_id'] = $cash_advance_return->reference?->nama ?? "Undefined";
                    $nestedData['total'] = formatNumber($cash_advance_return->total);
                    $nestedData['status'] = $badge;
                    $nestedData['created_at'] = toDayDateTimeString($cash_advance_return->created_at);
                    $nestedData['action'] =
                        Blade::render('components.datatable.button-datatable', [
                            'permission_name' => 'cash-advance-return',
                            'row' => $cash_advance_return,
                            'main' => $this->view_folder,
                            'btn_config' => [
                                'detail' => [
                                    'display' => false,
                                ],
                                'edit' => [
                                    'display' => in_array($cash_advance_return->status, ['pending', 'revert']) && $cash_advance_return->check_available_date,
                                ],
                                'delete' => [
                                    'display' => $cash_advance_return->status == 'pending' && $cash_advance_return->check_available_date,
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
        if (!$authorization->is_authoirization_exist(CashAdvancedReturn::class)) {
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
        // * validate
        $this->validate($request, [

            // * parent data
            'branch_id' => 'nullable|exists:branches,id',
            'project_id' => 'nullable|exists:projects,id',
            'currency_id' => 'required|exists:currencies,id',
            'invoice_currency_id' => 'required|exists:currencies,id',
            'reference_id' => 'nullable|exists:customers,id',

            // * child data
            'cash_advance_receives' => 'required|array',
            'cash_advance_receives.*' => 'required|exists:cash_advance_receives,id',
            'cash_advance_receive_amount_to_returns' => 'required|array',
            'cash_advance_receive_amount_to_returns.*' => 'required',

            // * invoice data
            'invoice_ids' => 'required|array',
            'invoice_amount_to_returns' => 'required|array',
            'invoice_descriptions' => 'required|array',
            'invoice_ids.*' => 'required|exists:invoice_parents,id',
            'invoice_amount_to_returns.*' => 'required',
            'invoice_descriptions.*' => 'required|string',

            // * other transaction
            'cash_advance_return_other_transactions_coa_id' => 'nullable|array',
            'cash_advance_return_other_transactions_amount' => 'nullable|array',
            'cash_advance_return_other_transactions_description' => 'nullable|array',
            'cash_advance_return_other_transactions_coa_id.*' => 'nullable|exists:coas,id',
            'cash_advance_return_other_transactions_amount.*' => 'nullable',
            'cash_advance_return_other_transactions_description.*' => 'nullable|string',
        ]);

        // ? SET GLOBAL PARENT DATA
        $parent_currency = \App\Models\Currency::find($request->currency_id);
        $invoice_currency = \App\Models\Currency::find($request->invoice_currency_id);
        $parent_exchange_rate = thousand_to_float($request->exchange_rate);
        // ? SET GLOBAL PARENT DATA

        // * validate currency
        if ((!$parent_currency->is_local && !$invoice_currency->is_local) && ($parent_currency->id != $invoice_currency->id)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "pastikan salah satu currency local, atau gunakan currency yang sama"));
        }

        $CASH_ADVANCE_TOTAL = 0;
        $INVOICE_TOTAL = 0;
        $OTHER_DEBIT_TOTAL = 0;
        $OTHER_CREDIT_TOTAL = 0;

        DB::beginTransaction();

        // ! PARENT DATA ########################################

        // * store parent data
        $model = new \App\Models\CashAdvancedReturn();

        $types = [
            "customer" => "C",
            "vendor" => "V",
            "employee" => "E"
        ];

        $last_code = \App\Models\CashAdvancedReturn::orderByDesc('id')
            ->where('type', 'customer')
            ->withTrashed()
            ->whereMonth('date', Carbon::parse($request->date))
            ->whereYear('date', Carbon::parse($request->date))
            ->first();

        $model->fill([
            'code' => generate_code_transaction("CARC", $last_code->code ?? null, date: $request->date),
            'branch_id' => $request->branch_id,
            'project_id' => $request->project_id,
            'currency_id' => $request->currency_id,
            'invoice_currency_id' => $request->invoice_currency_id,
            'reference_id' => $request->reference_id,
            'reference_model' => \App\Models\Customer::class,
            'type' => 'customer',
            'date' => Carbon::parse($request->date),
            'exchange_rate' => $parent_exchange_rate,
            'amount_total' => 0,
        ]);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        }

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        // ! / PARENT DATA ########################################

        // ! CHILD DATA ########################################

        // * get child data
        if (is_array($request->cash_advance_receives)) {

            $cash_advance_receives = \App\Models\CashAdvanceReceive::whereIn('id', $request->cash_advance_receives)->get();
        }

        // * set data for child
        $cash_advanced_return_details = [];
        foreach ($request->cash_advance_receives as $cash_advance_receive_key => $cash_advance_receive_id) {
            $cash_advance_receive = $cash_advance_receives->where('id', $cash_advance_receive_id)->first();
            // * get cash bank
            $cash_advance_receive_bank = $cash_advance_receive->cash_advance_receive_details()->where('type', 'cash_advance')->first();
            if (!$cash_advance_receive_bank) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "cash advance receive bank not found", null));
            }
            $amount_to_return = thousand_to_float($request->cash_advance_receive_amount_to_returns[$cash_advance_receive_key]);

            if ($amount_to_return > $cash_advance_receive->outstanding_amount) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "jumlah uang muka tidak cukup", null));
            }

            // * calculate the outstanding amount and balance
            $outstanding_amount = $cash_advance_receive_bank->credit - $cash_advance_receive_bank->cash_advance_return_total;
            $balance = $outstanding_amount - $amount_to_return;

            $cash_advanced_return_details[$cash_advance_receive_key] = [
                'cash_advanced_return_id' => $model->id,
                'coa_id' => $cash_advance_receive_bank->coa_id,
                'currency_id' => $cash_advance_receive->currency_id,
                'reference_id' => $cash_advance_receive->id,
                'reference_model' => \App\Models\CashAdvanceReceive::class,
                'date' => Carbon::parse($cash_advance_receive->date),
                'transaction_code' => $cash_advance_receive->bank_code_mutation ?? $cash_advance_receive->code,
                'type' => 'customer',
                'exchange_rate' => $cash_advance_receive->exchange_rate,
                'amount' => $cash_advance_receive_bank->credit,
                'amount_to_return' => $amount_to_return,
                'outstanding_amount' => $outstanding_amount,
                'balance' => $balance,
            ];

            $CASH_ADVANCE_TOTAL += $amount_to_return;
        }

        // * store child data
        try {
            $model->cashAdvancedReturnDetails()->createMany($cash_advanced_return_details);
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "create cash advance return detail", $th->getMessage()));
        }

        // ! / CHILD DATA ########################################

        // ! INVOICE DATA ########################################
        // * set data for invoice
        $create_value_cash_advanced_return_invoices = [];
        $cash_advance_return_invoices = [];
        if (is_array($request->invoice_ids)) {
            // * get data for invoices
            $cash_advance_return_invoices = \App\Models\InvoiceParent::whereIn('id', $request->invoice_ids)->get();
        }

        foreach ($request->invoice_ids as $cash_advance_return_invoice_key => $invoice_id) {
            $cash_advance_return_invoice_value = $cash_advance_return_invoices->where('id', $invoice_id)->first();
            // * get set gap exchange rate
            $invoice_currency = $cash_advance_return_invoice_value->currency;
            $exchange_rate_gap = 0;
            $amount_to_paid_or_return = thousand_to_float($request->invoice_amount_to_returns[$cash_advance_return_invoice_key]);

            /**
             * if currency is all local or invoice currency is local or
             * parent currency is not local
             *
             * rate gap 0
             */
            if (
                (!$invoice_currency->is_local and !$parent_currency->is_local) || !$invoice_currency->is_local
            ) {
                $exchange_rate_gap = ($parent_exchange_rate - $cash_advance_return_invoice_value->exchange_rate) * $amount_to_paid_or_return;
            } else {
                $exchange_rate_gap = 0;
            }


            // * calculate outstanding_amount, amount_total

            // * set data
            $amount_to_paid_or_return_convert = $amount_to_paid_or_return;
            if ($parent_currency->id != $invoice_currency->id) {
                if ($parent_currency->is_local and !$invoice_currency->is_local) {
                    $amount_to_paid_or_return_convert = $amount_to_paid_or_return * $parent_exchange_rate;
                } else {
                    $amount_to_paid_or_return_convert = $amount_to_paid_or_return / $parent_exchange_rate;
                }
            }
            $create_value_cash_advanced_return_invoices[] = [
                'cash_advanced_return_id' => $model->id,
                'currency_id' => $cash_advance_return_invoice_value->currency_id,
                'reference_id' => $cash_advance_return_invoice_value->id,
                'reference_model' => \App\Models\InvoiceParent::class,
                'date' => Carbon::parse($cash_advance_return_invoice_value->date),
                'transaction_code' => $cash_advance_return_invoice_value->code,
                'exchange_rate' => $cash_advance_return_invoice_value->exchange_rate,
                'outstanding_amount' => $cash_advance_return_invoice_value->outstanding_amount,
                'amount_to_paid_or_return' => $amount_to_paid_or_return,
                'amount_to_paid_or_return_convert' => $amount_to_paid_or_return_convert,
                'exchange_rate_gap' => $exchange_rate_gap,
                'description' => $request->invoice_descriptions[$cash_advance_return_invoice_key],
            ];

            $INVOICE_TOTAL += $amount_to_paid_or_return_convert;
        }

        // * store invoice data
        try {
            $model->cashAdvancedReturnInvoices()->createMany($create_value_cash_advanced_return_invoices);
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "create cash advance return invoice", $th->getMessage()));
        }
        // ! / INVOICE DATA ########################################

        // ! OTHER TRANSACTION DATA ########################################
        if ($request->cash_advance_return_other_transactions_coa_id) {
            // * set data for other transaction
            $cash_advance_return_other_transactions = [];

            if (is_array($request->cash_advance_return_other_transactions_coa_id)) {
                foreach ($request->cash_advance_return_other_transactions_coa_id as $cash_advance_return_other_transaction_key => $cash_advance_return_other_transaction_value) {
                    $other_amount = thousand_to_float($request->cash_advance_return_other_transactions_amount[$cash_advance_return_other_transaction_key]);
                    $cash_advance_return_other_transactions[] = [
                        'coa_id' => $request->cash_advance_return_other_transactions_coa_id[$cash_advance_return_other_transaction_key],
                        'cash_advanced_return_id' => $model->id,
                        'credit' => $other_amount > 0 ? abs($other_amount) : 0,
                        'debit' => $other_amount < 0 ? abs($other_amount) : 0,
                        'description' => $request->cash_advance_return_other_transactions_description[$cash_advance_return_other_transaction_key] ?? '',
                    ];

                    $OTHER_CREDIT_TOTAL += $other_amount > 0 ? abs($other_amount) : 0;
                    $OTHER_DEBIT_TOTAL += $other_amount < 0 ? abs($other_amount) : 0;
                }
            }

            // * store other transactions data
            try {
                $model->cashAdvancedReturnTransactions()->createMany($cash_advance_return_other_transactions);
            } catch (\Throwable $th) {
                DB::rollback();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "create cash advance return other transaction", $th->getMessage()));
            }
        }
        // ! / OTHER TRANSACTION DATA ########################################

        $CASH_ADVANCE_TOTAL += $OTHER_DEBIT_TOTAL;
        $INVOICE_TOTAL += $OTHER_CREDIT_TOTAL;

        // * check total
        if ($INVOICE_TOTAL != $CASH_ADVANCE_TOTAL) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "total invoice and total other transaction debit not same"));
        }

        $model->update([
            'cash_advance_total' => $model->cashAdvancedReturnDetails->sum('amount_to_return'),
            'invoice_total' => $model->cashAdvancedReturnInvoices->sum('amount_to_paid_or_return_convert'),
            'other_total' => $model->cashAdvancedReturnTransactions->sum('debit') - $model->cashAdvancedReturnTransactions->sum('credit'),
        ]);

        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        $authorization->init(
            branch_id: $model->branch_id,
            user_id: auth()->user()->id,
            model: CashAdvancedReturn::class,
            model_id: $model->id,
            amount: $CASH_ADVANCE_TOTAL ?? 0,
            title: "Pengembalian Uang Muka Customer",
            subtitle: auth()->user()->name . " mengajukan pengembalian uang muka customer " . $model->code,
            link: route('admin.cash-advance-return-customer.show', $model->id),
            update_status_link: route('admin.cash-advance-return-customer.update-status', ['id' => $model->id]),
            division_id: auth()->user()->division_id ?? null
        );

        DB::commit();

        return redirect()->route("admin.cash-advance-return-customer.index")->with($this->ResponseMessageCRUD(true, 'create'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = \App\Models\CashAdvancedReturn::findOrFail($id);

        validate_branch($model->branch_id);
        $invoice_parent_ids = CashAdvancedReturnInvoice::where('cash_advanced_return_id', $model->id)->pluck('reference_id');

        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: CashAdvancedReturn::class,
            model_id: $model->id,
            user_id: auth()->user()->id,
        );
        $authorization_logs['can_revert'] = $model->status == "approve" && $model->check_available_date;
        $authorization_logs['can_void'] = $model->status == "approve" && $model->check_available_date;
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = $model->status == "approve" && $model->check_available_date;
        $authorization_logs['can_void_request'] = $model->status == "approve" && $model->check_available_date;
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'auth_revert_void_button', 'invoice_parent_ids'));
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
        $model = \App\Models\CashAdvancedReturn::with([
            'branch',
            'project',
            'currency',
            'invoice_currency',
            // 'reference',

            'cashAdvancedReturnDetails.coa',
            'cashAdvancedReturnDetails.currency',
            // 'cashAdvancedReturnDetails.reference',

            'cashAdvancedReturnInvoices.currency',
            // 'cashAdvancedReturnInvoices.reference',

            'cashAdvancedReturnTransactions.coa',
        ])->findOrFail($id);

        if (!$model->check_available_date) {
            return abort(403);
        }

        validate_branch($model->branch_id);

        if (!in_array($model->status, ['pending', 'revert']) || !$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', "cash advance return status is {$model->status}"));
        }

        if ($request->ajax()) {
            $model->reference;
            $model->cashAdvancedReturnDetails->map(function ($detail) {
                $detail->reference;
                return $detail;
            });
            $model->cashAdvancedReturnInvoices->map(function ($invoice) {
                $invoice->reference;
                return $invoice;
            });

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
        $this->validate($request, [
            // * parent data
            // 'branch_id' => 'nullable|exists:branches,id',
            // 'project_id' => 'nullable|exists:projects,id',
            // 'currency_id' => 'required|exists:currencies,id',
            // 'invoice_currency_id' => 'required|exists:currencies,id',
            // 'reference_id' => 'nullable|exists:customers,id',

            // * child data
            'cash_advance_receives' => 'required|array',
            'cash_advance_receives.*' => 'required|exists:cash_advance_receives,id',
            'cash_advance_receive_amount_to_returns' => 'required|array',
            'cash_advance_receive_amount_to_returns.*' => 'required',

            // * invoice data
            'invoice_ids' => 'required|array',
            'invoice_amount_to_returns' => 'required|array',
            'invoice_descriptions' => 'required|array',
            'invoice_ids.*' => 'required|exists:invoice_parents,id',
            'invoice_amount_to_returns.*' => 'required',
            'invoice_descriptions.*' => 'required|string',

            // * other transaction
            'other_transaction_coa_ids' => 'nullable|array',
            'other_transaction_amounts' => 'nullable|array',
            'other_transaction_descriptions' => 'nullable|array',
            'other_transaction_coa_ids.*' => 'nullable|exists:coas,id',
            'other_transaction_amounts.*' => 'nullable',
            'other_transaction_descriptions.*' => 'nullable|string',
        ]);

        $model = \App\Models\CashAdvancedReturn::findOrFail($id);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal sudah closing'));
        }

        validate_branch($model->branch_id);

        if (!in_array($model->status, ['pending', 'revert']) || !$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', "cash advance return status is {$model->status}"));
        }

        // ? SET GLOBAL PARENT DATA
        $parent_currency = $model->currency;
        $invoice_currency = $model->invoice_currency;
        $parent_exchange_rate = $model->exchange_rate;
        // ? SET GLOBAL PARENT DATA

        $CASH_ADVANCE_TOTAL = 0;
        $INVOICE_TOTAL = 0;
        $OTHER_DEBIT_TOTAL = 0;
        $OTHER_CREDIT_TOTAL = 0;

        DB::beginTransaction();

        // ! PARENT DATA ###########################################
        // $model->fill([])

        // try {
        //     $model->save();
        // } catch (\Throwable $th) {
        //     DB::rollback();

        //     return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        // }
        // ! END PARENT DATA ###########################################

        // ! CHILD DATA ###########################################
        // * get child data
        if (is_array($request->cash_advance_receives)) {
            $cash_advance_receives = \App\Models\CashAdvanceReceive::whereIn('id', $request->cash_advance_receives)->get();
        }

        // * set data for child
        $cash_advanced_return_details = [];
        foreach ($request->cash_advance_receives as $cash_advance_receive_key => $cash_advance_receive_id) {
            $cash_advance_receive = $cash_advance_receives->where('id', $cash_advance_receive_id)->first();
            // * get cash bank
            $cash_advance_receive_bank = $cash_advance_receive->cash_advance_receive_details()->where('type', 'cash_advance')->first();
            if (!$cash_advance_receive_bank) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', "cash advance receive bank not found", null));
            }

            $amount_to_return = ($request->cash_advance_receive_amount_to_returns[$cash_advance_receive_key]);
            if ($amount_to_return > $cash_advance_receive->outstanding_amount) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', "jumlah uang muka tidak cukup", null));
            }

            // * calculate the outstanding amount and balance
            $outstanding_amount = $cash_advance_receive_bank->credit - $cash_advance_receive_bank->cash_advance_return_total;
            $amount_to_return = $request->cash_advance_receive_amount_to_returns[$cash_advance_receive_key];
            $balance = $outstanding_amount - ($amount_to_return);

            // update code if old date year and month not same as new date year and month
            $code = $model->code;
            if (Carbon::parse($model->date)->format('Ym') != Carbon::parse($request->date)->format('Ym')) {
                $branch = Branch::find($request->branch_id ?? $model->branch_id);
                $code = generate_code(get_class($model), 'code', 'date', 'UMM', branch_sort: $branch->sort ?? null, date: $request->date);
            }

            $model->update([
                'code' => $code,
                'date' => Carbon::parse($request->date),
            ]);

            $cash_advanced_return_details[$cash_advance_receive_key] = [
                'cash_advanced_return_id' => $model->id,
                'coa_id' => $cash_advance_receive_bank->coa_id,
                'currency_id' => $cash_advance_receive->currency_id,
                'reference_id' => $cash_advance_receive->id,
                'reference_model' => \App\Models\CashAdvanceReceive::class,
                'date' => Carbon::parse($cash_advance_receive->date),
                'transaction_code' => $cash_advance_receive->bank_code_mutation ?? $cash_advance_receive->code,
                'type' => 'customer',
                'exchange_rate' => $cash_advance_receive->exchange_rate,
                'amount' => $cash_advance_receive_bank->credit,
                'amount_to_return' => ($amount_to_return),
                'outstanding_amount' => $outstanding_amount,
                'balance' => $balance,
            ];

            $CASH_ADVANCE_TOTAL += ($amount_to_return);
        }

        // * delete old data and create new data
        try {
            $model->cashAdvancedReturnDetails()->delete();
            $model->cashAdvancedReturnDetails()->createMany($cash_advanced_return_details);
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }
        // ! END CHILD DATA ###########################################

        // ! INVOICE DATA ###########################################
        // * set data for invoice
        $create_value_cash_advanced_return_invoices = [];
        $cash_advance_return_invoices = [];
        if (is_array($request->invoice_ids)) {
            // * get data for invoices
            $cash_advance_return_invoices = \App\Models\InvoiceParent::whereIn('id', $request->invoice_ids)->get();
        }

        foreach ($request->invoice_ids as $cash_advance_return_invoice_key => $invoice_id) {
            $cash_advance_return_invoice_value = $cash_advance_return_invoices->where('id', $invoice_id)->first();
            // * get set gap exchange rate
            $invoice_currency = $cash_advance_return_invoice_value->currency;
            $exchange_rate_gap = 0;
            $amount_to_paid_or_return = $request->invoice_amount_to_returns[$cash_advance_return_invoice_key];

            /**
             * if currency is all local or invoice currency is local or
             * parent currency is not local
             *
             * rate gap 0
             */
            if (
                (!$invoice_currency->is_local and !$parent_currency->is_local) || !$invoice_currency->is_local
            ) {
                $exchange_rate_gap = ($parent_exchange_rate - $cash_advance_return_invoice_value->exchange_rate) * $amount_to_paid_or_return;
            } else {
                $exchange_rate_gap = 0;
            }

            $amount_to_paid_or_return_convert = $amount_to_paid_or_return;
            if ($parent_currency->id != $invoice_currency->id) {
                if ($parent_currency->is_local and !$invoice_currency->is_local) {
                    $amount_to_paid_or_return_convert = $amount_to_paid_or_return * $parent_exchange_rate;
                } else {
                    $amount_to_paid_or_return_convert = $amount_to_paid_or_return / $parent_exchange_rate;
                }
            }

            // * calculate outstanding_amount, amount_total

            // * set data
            $create_value_cash_advanced_return_invoices[] = [
                'cash_advanced_return_id' => $model->id,
                'currency_id' => $cash_advance_return_invoice_value->currency_id,
                'reference_id' => $cash_advance_return_invoice_value->id,
                'reference_model' => \App\Models\InvoiceParent::class,
                'date' => Carbon::parse($cash_advance_return_invoice_value->date),
                'transaction_code' => $cash_advance_return_invoice_value->code,
                'exchange_rate' => $cash_advance_return_invoice_value->exchange_rate,
                'outstanding_amount' => $cash_advance_return_invoice_value->outstanding_amount,
                'amount_to_paid_or_return' => $amount_to_paid_or_return,
                'amount_to_paid_or_return_convert' => $amount_to_paid_or_return_convert,
                'exchange_rate_gap' => $exchange_rate_gap,
                'description' => $request->invoice_descriptions[$cash_advance_return_invoice_key],
            ];

            $INVOICE_TOTAL += $amount_to_paid_or_return_convert;
        }

        // * delete old data and create new data
        try {
            $model->cashAdvancedReturnInvoices()->delete();
            $model->cashAdvancedReturnInvoices()->createMany($create_value_cash_advanced_return_invoices);
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }
        // ! END INVOICE DATA ###########################################

        // ! OTHER TRANSACTION DATA ########################################
        if ($request->other_transaction_coa_ids) {
            // * set data for other transaction
            $other_transactions = [];

            if (is_array($request->other_transaction_coa_ids)) {
                foreach ($request->other_transaction_coa_ids as $other_transaction_key => $other_transaction_value) {
                    $amount_other = $request->other_transaction_amounts[$other_transaction_key];
                    $other_transactions[] = [
                        'coa_id' => $request->other_transaction_coa_ids[$other_transaction_key],
                        'cash_advanced_return_id' => $model->id,
                        'credit' => ($amount_other) > 0 ? abs(($amount_other)) : 0,
                        'debit' => ($amount_other) < 0 ? abs(($amount_other)) : 0,
                        'description' => $request->other_transaction_descriptionss[$other_transaction_key] ?? '',
                    ];

                    $OTHER_CREDIT_TOTAL += ($amount_other) > 0 ? abs(($amount_other)) : 0;
                    $OTHER_DEBIT_TOTAL += ($amount_other) < 0 ? abs(($amount_other)) : 0;
                }
            }

            // * store other transactions data and delete the old data
            try {
                $model->cashAdvancedReturnTransactions()->delete();
                $model->cashAdvancedReturnTransactions()->createMany($other_transactions);
            } catch (\Throwable $th) {
                DB::rollback();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', "create cash advance return other transaction", $th->getMessage()));
            }
        }
        // ! / END OTHER TRANSACTION DATA ########################################

        $CASH_ADVANCE_TOTAL += $OTHER_DEBIT_TOTAL;
        $INVOICE_TOTAL += $OTHER_CREDIT_TOTAL;

        // * check if total cash advance and total invoice is same
        if ($CASH_ADVANCE_TOTAL != $INVOICE_TOTAL) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', "total invoice and total other transaction debit not same"));
        }

        $model->update([
            'cash_advance_total' => $model->cashAdvancedReturnDetails->sum('amount_to_return'),
            'invoice_total' => $model->cashAdvancedReturnInvoices->sum('amount_to_paid_or_return_convert'),
            'other_total' => $model->cashAdvancedReturnTransactions->sum('debit') - $model->cashAdvancedReturnTransactions->sum('credit'),
        ]);

        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        $authorization->init(
            branch_id: $model->branch_id,
            user_id: auth()->user()->id,
            model: CashAdvancedReturn::class,
            model_id: $model->id,
            amount: $CASH_ADVANCE_TOTAL ?? 0,
            title: "Pengembalian Uang Muka Customer",
            subtitle: auth()->user()->name . " mengajukan pengembalian uang muka customer " . $model->code,
            link: route('admin.cash-advance-return-customer.show', $model->id),
            update_status_link: route('admin.cash-advance-return-customer.update-status', ['id' => $model->id]),
            division_id: auth()->user()->division_id ?? null
        );

        DB::commit();

        return redirect()->route("admin.cash-advance-return-customer.index")->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = \App\Models\CashAdvancedReturn::findOrFail($id);

        validate_branch($model->branch_id);

        if (!in_array($model->status, ['pending', 'revert'])) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', "cash advance return status is {$model->status}"));
        }

        DB::beginTransaction();

        // * delete data
        try {
            $model->delete();

            Authorization::where('model', CashAdvancedReturn::class)->where('model_id', $model->id)->delete();
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.cash-advance-return-customer.index")->with($this->ResponseMessageCRUD(true, 'delete'));
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
        $model = \App\Models\CashAdvancedReturn::findOrFail($id);

        validate_branch($model->branch_id);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, 'Tanggal sudah closing'));
        }

        DB::beginTransaction();

        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(\App\Models\CashAdvancedReturn::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);
                $model->status = $request->status;
                $model->save();
            } else {
                $this->create_activity_status_log(\App\Models\CashAdvancedReturn::class, $id, $request->note ?? $request->message ?? 'message not available', null, $request->status);
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

    /**
     * get_cash_advance_receives
     *
     * @param int|null $customer_id
     * @param int|null $project_id
     * @param int|null $currency_id
     * @return mixed
     */
    public function get_cash_advance_receives(Request $request)
    {
        $data = \App\Models\CashAdvanceReceive::with(['cash_advance_receive_details.coa', 'currency'])
            ->where('customer_id', $request->customer_id)
            ->where('currency_id', $request->currency_id)
            ->where('project_id', $request->project_id)
            ->whereIn('status', ['approve', 'partial'])
            ->when(get_current_branch()->is_primary && !is_null($request->branch_id), function ($query) use ($request) {
                $query->where('branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                $query->where('branch_id', get_current_branch()->id);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $item->bank_code_mutation = $item->bank_code_mutation ?? null;
                return $item;
            });

        return $this->ResponseJsonData($data->where('outstanding_amount', '>', 0)->flatten());
    }

    /**
     * get_cash_advance_receives
     *
     * @param int|null $customer_id
     * @param int|null $currency_id
     * @return mixed
     */
    public function get_unpaid_full_invoices(Request $request)
    {
        $data = \App\Models\InvoiceParent::with(['customer.customer_coas.coa', 'currency'])
            ->where('customer_id', $request->customer_id)
            ->where('currency_id', $request->currency_id)
            ->whereIn('payment_status', ['unpaid', 'partial', 'partial-paid'])
            ->whereIn('status', ['approve'])
            ->when(get_current_branch()->is_primary && !is_null($request->branch_id), function ($query) use ($request) {
                $query->where('branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                $query->where('branch_id', get_current_branch()->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->ResponseJsonData($data);
    }

    /**
     * get details data for edit
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetailForEdit(Request $request)
    {
        $model = \App\Models\CashAdvancedReturn::with([
            'branch',
            'project',
            'currency',
            // 'reference',

            'cashAdvancedReturnDetails.coa',
            'cashAdvancedReturnDetails.currency',
            // 'cashAdvancedReturnDetails.reference',

            'cashAdvancedReturnInvoices.currency',
            // 'cashAdvancedReturnInvoices.reference',

            'cashAdvancedReturnTransactions.coa',
        ])->findOrFail($request->id);

        validate_branch($model->branch_id);

        if (!in_array($model->status, ['pending', 'revert'])) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', "cash advance return status is {$model->status}"));
        }

        $model->reference;

        $cash_advance_receive_ids = $model->cashAdvancedReturnDetails->pluck('reference_id')->toArray();
        $invoice_ids = $model->cashAdvancedReturnInvoices->pluck('reference_id')->toArray();

        $cash_advance_receives = \App\Models\CashAdvanceReceive::with(['cash_advance_receive_details.coa', 'currency'])
            ->whereIn('id', $cash_advance_receive_ids)
            ->orderBy('created_at', 'desc')
            ->get();

        $invoices = \App\Models\InvoiceParent::with(['customer.customer_coas.coa', 'currency'])
            ->whereIn('id', $invoice_ids)
            ->orderBy('created_at', 'desc')
            ->get();

        $model->cashAdvancedReturnDetails->map(function ($d, $key) use ($cash_advance_receives) {
            $d->reference = $cash_advance_receives->where('id', $d->reference_id)->first();
        });

        $model->cashAdvancedReturnInvoices->map(function ($d, $key) use ($invoices) {
            $d->reference = $invoices->where('id', $d->reference_id)->first();
        });

        return $this->ResponseJsonData($model);
    }

    public function export($id, Request $request)
    {
        $model = CashAdvancedReturn::findOrFail(decryptId($id));
        $title = "Pengembalian Uang Muka " . $model->type;

        $fileName = $title . ' - ' . ucfirst($model->code) . '.pdf';

        $qr_url = route('cash-advance-return-customer.export', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $pdf = Pdf::loadview("admin.cash-advance-return-$model->type.export", compact('model', 'qr'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');

        return $pdf->stream($fileName);
    }
}
