<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Authorization;
use App\Models\Branch;
use App\Models\CashAdvancedReturn;
use App\Models\CashAdvancedReturnInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CashAdvancedReturnVendorController extends Controller
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
    protected string $view_folder = 'cash-advance-return-vendor';

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
                'vendors.name',
                'total',
                'status',
                'created_at'
            ];

            // * get data with date
            $search = $request->input('search.value');
            $query = \App\Models\CashAdvancedReturn::leftJoin('vendors', 'vendors.id', 'cash_advanced_returns.reference_id')
                ->orderByDesc('created_at')
                ->where('cash_advanced_returns.type', 'vendor')
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
                        ->orWhere('vendors.nama', 'like', "%{$search}%");
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

                    $nestedData['DT_RowIndex'] = $key + 1;
                    $nestedData['id'] = $cash_advance_return->id;
                    $nestedData['date'] = localDate($cash_advance_return->date);
                    $export_route = route("cash-advance-return-vendor.export", ['id' => encryptId($cash_advance_return->id)]);
                    $nestedData['code'] = '<a href="' . route("admin.$this->view_folder.index") . '/' . $cash_advance_return->id . '" class="text-primary">' . $cash_advance_return->code . '</a>' . '<br />' . '<a target="_blank" href="' . $export_route . '" class="btn btn-sm btn-light" onclick="show_print_out_modal(event)"><i class="fa fa-file-pdf"></i></a>';
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
            'supplier_invoice_currency_id' => 'required|exists:currencies,id',
            'reference_id' => 'nullable|exists:vendors,id',

            // * child data
            'cash_advance_payments' => 'required|array',
            'cash_advance_payments.*' => 'required|exists:cash_advance_payments,id',
            'cash_advance_payment_amount_to_returns' => 'required|array',
            'cash_advance_payment_amount_to_returns.*' => 'required',

            // * invoice data
            'invoice_ids' => 'required|array',
            'invoice_amount_to_returns' => 'required|array',
            'invoice_descriptions' => 'required|array',
            'invoice_ids.*' => 'required|exists:supplier_invoice_parents,id',
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
        $invoice_currency = \App\Models\Currency::find($request->supplier_invoice_currency_id);
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

        $last_code = \App\Models\CashAdvancedReturn::orderByDesc('id')
            ->where('type', 'vendor')
            ->withTrashed()
            ->whereMonth('date', Carbon::parse($request->date))
            ->whereYear('date', Carbon::parse($request->date))
            ->first();

        $model->fill([
            'code' => generate_code_transaction("CARV", $last_code->code ?? null, date: $request->date),
            'branch_id' => $request->branch_id,
            'project_id' => $request->project_id,
            'currency_id' => $request->currency_id,
            'invoice_currency_id' => $request->supplier_invoice_currency_id,
            'reference_id' => $request->reference_id,
            'reference_model' => \App\Models\Vendor::class,
            'type' => 'vendor',
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
        if (is_array($request->cash_advance_payments)) {
            $cash_advance_payments = \App\Models\CashAdvancePayment::whereIn('id', $request->cash_advance_payments)->get();
        }

        // * set data for child
        $cash_advanced_return_details = [];
        foreach ($request->cash_advance_payments as $cash_advance_payment_key => $cash_advance_payment_id) {
            $cash_advance_payment = $cash_advance_payments->where('id', $cash_advance_payment_id)->first();
            // * get cash bank
            $cash_advance_payment_cash_advance = $cash_advance_payment->cash_advance_payment_details()->where('type', 'cash_advance')->first();
            if (!$cash_advance_payment_cash_advance) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "cash advance payment bank not found", null));
            }

            $amount_to_return = $request->cash_advance_payment_amount_to_returns[$cash_advance_payment_key];
            // * calculate the outstanding amount and balance
            $debit = $cash_advance_payment_cash_advance->debit;
            if ($cash_advance_payment->tax) {
                $tax = $cash_advance_payment->cash_advance_payment_details()->where('type', 'tax')->first();
                if ($tax) {
                    $debit += $tax->debit;
                }
            }
            $outstanding_amount = $debit - $cash_advance_payment_cash_advance->cash_advance_return_total;
            $balance = $outstanding_amount - $amount_to_return;

            $cash_advanced_return_details[$cash_advance_payment_key] = [
                'cash_advanced_return_id' => $model->id,
                'coa_id' => $cash_advance_payment_cash_advance->coa_id,
                'currency_id' => $cash_advance_payment->currency_id,
                'reference_id' => $cash_advance_payment->id,
                'reference_model' => \App\Models\CashAdvancePayment::class,
                'date' => Carbon::parse($cash_advance_payment->date),
                'transaction_code' =>  $cash_advance_payment->bank_code_mutation ?? $cash_advance_payment->code,
                'type' => 'vendor',
                'exchange_rate' => $cash_advance_payment->exchange_rate,
                'amount' => $debit,
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

        // ! INVOICE DATA ########################################

        // * set data for invoice
        $cash_advance_return_invoices = [];
        if (is_array($request->invoice_ids)) {
            // * get data for invoices
            $cash_advance_return_invoices = \App\Models\SupplierInvoiceParent::whereIn('id', $request->invoice_ids)->get();
        }

        foreach ($request->invoice_ids as $cash_advance_return_invoice_key => $invoiceid) {
            $cash_advance_return_invoice_value = $cash_advance_return_invoices->where('id', $invoiceid)->first();
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


            // * calculate outstanding_amount, amount_total

            // * set data
            $cash_advance_return_invoice = new \App\Models\CashAdvancedReturnInvoice();
            $amount_to_paid_or_return_convert = $amount_to_paid_or_return;
            if ($parent_currency->id != $invoice_currency->id) {
                if ($parent_currency->is_local and !$invoice_currency->is_local) {
                    $amount_to_paid_or_return_convert = $amount_to_paid_or_return * $parent_exchange_rate;
                } else {
                    $amount_to_paid_or_return_convert = $amount_to_paid_or_return / $parent_exchange_rate;
                }
            }
            $cash_advance_return_invoice->fill([
                'cash_advanced_return_id' => $model->id,
                'currency_id' => $cash_advance_return_invoice_value->currency_id,
                'reference_id' => $cash_advance_return_invoice_value->id,
                'reference_model' => \App\Models\SupplierInvoiceParent::class,
                'date' => Carbon::parse($cash_advance_return_invoice_value->date),
                'transaction_code' => $cash_advance_return_invoice_value->code,
                'exchange_rate' => $cash_advance_return_invoice_value->exchange_rate,
                'outstanding_amount' => $cash_advance_return_invoice_value->outstanding_amount,
                'amount_to_paid_or_return' => $amount_to_paid_or_return,
                'amount_to_paid_or_return_convert' => $amount_to_paid_or_return_convert,
                'exchange_rate_gap' => $exchange_rate_gap,
                'description' => $request->invoice_descriptions[$cash_advance_return_invoice_key],
            ]);

            try {
                $cash_advance_return_invoice->save();
            } catch (\Throwable $th) {
                DB::rollback();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "update invoice outstanding amount", $th->getMessage()));
            }

            $item_receiving_report_ids = array_values($request->item_receiving_report_ids);
            $item_receiving_report_amount_to_return = array_values($request->item_receiving_report_amount_to_return);

            $cash_advance_return_invoice_item_receiving_report = [];
            foreach ($item_receiving_report_ids[$cash_advance_return_invoice_key] as $item_receiving_report_id_key => $item_receiving_report_id) {
                $item_receiving_report_model = \App\Models\ItemReceivingReport::find($item_receiving_report_id);
                $amount = $item_receiving_report_amount_to_return[$cash_advance_return_invoice_key][$item_receiving_report_id_key];
                $amount_convert = $amount;
                if ($parent_currency->id != $invoice_currency->id) {
                    if ($parent_currency->is_local and !$invoice_currency->is_local) {
                        $amount_convert = $amount * $parent_exchange_rate;
                    } else {
                        $amount_convert = $amount / $parent_exchange_rate;
                    }
                }

                $cash_advance_return_invoice_item_receiving_report[] = [
                    'cash_advance_return_invoice_id' => $cash_advance_return_invoice->id,
                    'item_receiving_report_id' => $item_receiving_report_id,
                    'amount' => $amount,
                    'amount_convert' => $amount_convert,
                    'outstanding' => $item_receiving_report_model->outstanding - $amount,
                ];
            }

            try {
                \App\Models\CashAdvanceReturnInvoiceDetail::insert($cash_advance_return_invoice_item_receiving_report);
            } catch (\Throwable $th) {
                DB::rollback();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "create cash advance return invoice item receiving report", $th->getMessage()));
            }

            $INVOICE_TOTAL += $amount_to_paid_or_return_convert;
        }
        // ! / INVOICE DATA ########################################

        // ! OTHER TRANSACTION DATA ########################################
        if ($request->cash_advance_return_other_transactions_coa_id) {
            // * set data for other transaction
            $cash_advance_return_other_transactions = [];

            if (is_array($request->cash_advance_return_other_transactions_coa_id)) {
                foreach ($request->cash_advance_return_other_transactions_coa_id as $cash_advance_return_other_transaction_key => $cash_advance_return_other_transaction_value) {
                    $other_amount = $request->cash_advance_return_other_transactions_amount[$cash_advance_return_other_transaction_key];
                    $cash_advance_return_other_transactions[] = [
                        'coa_id' => $request->cash_advance_return_other_transactions_coa_id[$cash_advance_return_other_transaction_key],
                        'cash_advanced_return_id' => $model->id,
                        'credit' => $other_amount < 0 ? abs($other_amount) : 0,
                        'debit' => $other_amount > 0 ? abs($other_amount) : 0,
                        'description' => $request->cash_advance_return_other_transactions_description[$cash_advance_return_other_transaction_key] ?? '',
                    ];

                    $OTHER_CREDIT_TOTAL += $other_amount < 0 ? abs($other_amount) : 0;
                    $OTHER_DEBIT_TOTAL += $other_amount  > 0 ? abs($other_amount) : 0;
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
        $CASH_ADVANCE_TOTAL += $OTHER_CREDIT_TOTAL;
        $INVOICE_TOTAL += $OTHER_DEBIT_TOTAL;

        // * check total
        if ($CASH_ADVANCE_TOTAL != $INVOICE_TOTAL) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "total credit dan debit tidak sama"));
        }

        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        $authorization->init(
            branch_id: $model->branch_id,
            user_id: auth()->user()->id,
            model: CashAdvancedReturn::class,
            model_id: $model->id,
            amount: $CASH_ADVANCE_TOTAL ?? 0,
            title: "Pengembalian Uang Muka Vendor",
            subtitle: auth()->user()->name . " mengajukan pengembalian uang muka vendor " . $model->code,
            link: route('admin.cash-advance-return-vendor.show', $model->id),
            update_status_link: route('admin.cash-advance-return-vendor.update-status', ['id' => $model->id]),
            division_id: auth()->user()->division_id ?? null
        );

        $model->update([
            'cash_advance_total' => $model->cashAdvancedReturnDetails->sum('amount_to_return'),
            'invoice_total' => $model->cashAdvancedReturnInvoices->sum('amount_to_paid_or_return_convert'),
            'other_total' => $model->cashAdvancedReturnTransactions->sum('debit') - $model->cashAdvancedReturnTransactions->sum('credit'),
        ]);

        DB::commit();

        return redirect()->route("admin.cash-advance-return-vendor.index")->with($this->ResponseMessageCRUD(true, 'create'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = \App\Models\CashAdvancedReturn::with(['cashAdvancedReturnInvoices'])->findOrFail($id);


        validate_branch($model->branch_id);
        $supplier_invoice_parent_ids = CashAdvancedReturnInvoice::where('cash_advanced_return_id', $model->id)->pluck('reference_id');
        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $cash_advance_return_invoice_ids = $model->cashAdvancedReturnInvoices->pluck('id');
        $cash_advance_return_invoice_details = \App\Models\CashAdvanceReturnInvoiceDetail::with(['item_receiving_report'])->whereIn('cash_advance_return_invoice_id', $cash_advance_return_invoice_ids)->get();

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

        return view("admin.$this->view_folder.show", compact('model', 'cash_advance_return_invoice_details', 'status_logs', 'activity_logs', 'authorization_log_view', 'auth_revert_void_button', 'supplier_invoice_parent_ids'));
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
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
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
        // * validate
        $this->validate($request, [

            // * parent data
            // 'branch_id' => 'nullable|exists:branches,id',
            // 'project_id' => 'nullable|exists:projects,id',
            // 'currency_id' => 'required|exists:currencies,id',
            // 'supplier_invoice_currency_id' => 'required|exists:currencies,id',
            // 'reference_id' => 'nullable|exists:vendors,id',

            // * child data
            'cash_advance_payments' => 'required|array',
            'cash_advance_payments.*' => 'required|exists:cash_advance_payments,id',
            'cash_advance_payment_amount_to_returns' => 'required|array',
            'cash_advance_payment_amount_to_returns.*' => 'required',

            // * invoice data
            'invoice_ids' => 'required|array',
            'invoice_amount_to_returns' => 'required|array',
            'invoice_descriptions' => 'required|array',
            'invoice_ids.*' => 'required|exists:supplier_invoice_parents,id',
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

        $model = \App\Models\CashAdvancedReturn::findOrFail($id);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
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

        //     return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        // }
        // ! END PARENT DATA ###########################################

        // ! CHILD DATA ###########################################
        // * get child data
        if (is_array($request->cash_advance_payments)) {
            $cash_advance_payments = \App\Models\CashAdvancePayment::whereIn('id', $request->cash_advance_payments)->get();
        }

        // * set data for child
        $cash_advanced_return_details = [];
        foreach ($request->cash_advance_payments as $cash_advance_payment_key => $cash_advance_payment_id) {
            $cash_advance_payment = $cash_advance_payments->where('id', $cash_advance_payment_id)->first();
            // * get cash bank
            $cash_advance_payment_cash_advance = $cash_advance_payment->cash_advance_payment_details()->where('type', 'cash_advance')->first();
            if (!$cash_advance_payment_cash_advance) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', "cash advance payment bank not found", null));
            }

            // * calculate the outstanding amount and balance
            $debit = $cash_advance_payment_cash_advance->debit;
            if ($cash_advance_payment->tax) {
                $tax = $cash_advance_payment->cash_advance_payment_details()->where('type', 'tax')->first();
                if ($tax) {
                    $debit += $tax->debit;
                }
            }
            $outstanding_amount = $debit - $cash_advance_payment_cash_advance->cash_advance_return_total;
            $amount_to_return = ($request->cash_advance_payment_amount_to_returns[$cash_advance_payment_key]);
            $balance = $outstanding_amount - $amount_to_return;

            $cash_advanced_return_details[$cash_advance_payment_key] = [
                'cash_advanced_return_id' => $model->id,
                'coa_id' => $cash_advance_payment_cash_advance->coa_id,
                'currency_id' => $cash_advance_payment->currency_id,
                'reference_id' => $cash_advance_payment->id,
                'reference_model' => \App\Models\CashAdvancePayment::class,
                'date' => Carbon::parse($cash_advance_payment->date),
                'transaction_code' =>  $cash_advance_payment->bank_code_mutation ?? $cash_advance_payment->code,
                'type' => 'vendor',
                'exchange_rate' => $cash_advance_payment->exchange_rate,
                'amount' => $debit,
                'amount_to_return' => $amount_to_return,
                'outstanding_amount' => $outstanding_amount,
                'balance' => $balance,
            ];

            $CASH_ADVANCE_TOTAL += $amount_to_return;
        }

        // * store and delete old data
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
        $cash_advance_return_invoices = [];
        if (is_array($request->invoice_ids)) {
            // * get data for invoices
            $cash_advance_return_invoices = \App\Models\SupplierInvoiceParent::whereIn('id', $request->invoice_ids)->get();
        }

        // * delete old data
        try {
            $model->cashAdvancedReturnInvoices()->delete();
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
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
            $cash_advance_return_invoice = new \App\Models\CashAdvancedReturnInvoice();
            $cash_advance_return_invoice->fill([
                'cash_advanced_return_id' => $model->id,
                'currency_id' => $cash_advance_return_invoice_value->currency_id,
                'reference_id' => $cash_advance_return_invoice_value->id,
                'reference_model' => \App\Models\SupplierInvoiceParent::class,
                'date' => Carbon::parse($cash_advance_return_invoice_value->date),
                'transaction_code' => $cash_advance_return_invoice_value->code,
                'exchange_rate' => $cash_advance_return_invoice_value->exchange_rate,
                'outstanding_amount' => $cash_advance_return_invoice_value->outstanding_amount,
                'amount_to_paid_or_return' => ($amount_to_paid_or_return),
                'amount_to_paid_or_return_convert' => $amount_to_paid_or_return_convert,
                'exchange_rate_gap' => $exchange_rate_gap,
                'description' => $request->invoice_descriptions[$cash_advance_return_invoice_key],
            ]);

            try {
                $cash_advance_return_invoice->save();
            } catch (\Throwable $th) {
                DB::rollback();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "update invoice outstanding amount", $th->getMessage()));
            }

            $cash_advance_return_invoice_item_receiving_report = [];
            foreach ($request->item_receiving_report_ids[$cash_advance_return_invoice_key] as $item_receiving_report_id_key => $item_receiving_report_id) {
                $item_receiving_report_model = \App\Models\ItemReceivingReport::find($item_receiving_report_id);
                $amount = $request->item_receiving_report_amount_to_return[$cash_advance_return_invoice_key][$item_receiving_report_id_key];
                $amount_convert = $amount;
                if ($parent_currency->id != $invoice_currency->id) {
                    if ($parent_currency->is_local and !$invoice_currency->is_local) {
                        $amount_convert = $amount * $parent_exchange_rate;
                    } else {
                        $amount_convert = $amount / $parent_exchange_rate;
                    }
                }

                $cash_advance_return_invoice_item_receiving_report[] = [
                    'cash_advance_return_invoice_id' => $cash_advance_return_invoice->id,
                    'item_receiving_report_id' => $item_receiving_report_id,
                    'amount' => $amount,
                    'amount_convert' => $amount_convert,
                    'outstanding' => $item_receiving_report_model->outstanding - ($amount),
                ];
            }

            try {
                \App\Models\CashAdvanceReturnInvoiceDetail::insert($cash_advance_return_invoice_item_receiving_report);
            } catch (\Throwable $th) {
                DB::rollback();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "create cash advance return invoice item receiving report", $th->getMessage()));
            }

            $INVOICE_TOTAL += ($amount_to_paid_or_return_convert);
        }
        // ! END INVOICE DATA ###########################################

        // ! OTHER TRANSACTION DATA ########################################
        try {
            $model->cashAdvancedReturnTransactions()->delete();
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', "edit cash advance return other transaction", $th->getMessage()));
        }

        if ($request->cash_advance_return_other_transactions_coa_id) {
            // * set data for other transaction
            $cash_advance_return_other_transactions = [];

            if (is_array($request->cash_advance_return_other_transactions_coa_id)) {
                foreach ($request->cash_advance_return_other_transactions_coa_id as $cash_advance_return_other_transaction_key => $cash_advance_return_other_transaction_value) {
                    $amount_other = $request->cash_advance_return_other_transactions_amount[$cash_advance_return_other_transaction_key];
                    $cash_advance_return_other_transactions[] = [
                        'coa_id' => $request->cash_advance_return_other_transactions_coa_id[$cash_advance_return_other_transaction_key],
                        'cash_advanced_return_id' => $model->id,
                        'credit' => ($amount_other) < 0 ? abs(($amount_other)) : 0,
                        'debit' => ($amount_other) > 0 ? abs(($amount_other)) : 0,
                        'description' => $request->cash_advance_return_other_transactions_description[$cash_advance_return_other_transaction_key] ?? '',
                    ];

                    $OTHER_CREDIT_TOTAL += ($amount_other) < 0 ? abs(($amount_other)) : 0;
                    $OTHER_DEBIT_TOTAL += ($amount_other)  > 0 ? abs(($amount_other)) : 0;
                }
            }

            // * store other transactions data and delete old data
            try {
                $model->cashAdvancedReturnTransactions()->createMany($cash_advance_return_other_transactions);
            } catch (\Throwable $th) {
                DB::rollback();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', "edit cash advance return other transaction", $th->getMessage()));
            }
        }
        // ! / OTHER TRANSACTION DATA ########################################

        $CASH_ADVANCE_TOTAL += $OTHER_CREDIT_TOTAL;
        $INVOICE_TOTAL += $OTHER_DEBIT_TOTAL;

        // * check total
        if ($CASH_ADVANCE_TOTAL != $INVOICE_TOTAL) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', "total credit dan debit tidak sama"));
        }

        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        $authorization->init(
            branch_id: $model->branch_id,
            user_id: auth()->user()->id,
            model: CashAdvancedReturn::class,
            model_id: $model->id,
            amount: $CASH_ADVANCE_TOTAL ?? 0,
            title: "Pengembalian Uang Muka Vendor",
            subtitle: auth()->user()->name . " mengajukan pengembalian uang muka vendor " . $model->code,
            link: route('admin.cash-advance-return-vendor.show', $model->id),
            update_status_link: route('admin.cash-advance-return-vendor.update-status', ['id' => $model->id]),
            division_id: auth()->user()->division_id ?? null
        );

        $model->update([
            'cash_advance_total' => $model->cashAdvancedReturnDetails->sum('amount_to_return'),
            'invoice_total' => $model->cashAdvancedReturnInvoices->sum('amount_to_paid_or_return_convert'),
            'other_total' => $model->cashAdvancedReturnTransactions->sum('debit') - $model->cashAdvancedReturnTransactions->sum('credit'),
        ]);

        DB::commit();

        return redirect()->route('admin.cash-advance-return-vendor.index')->with($this->ResponseMessageCRUD(true, 'edit', "cash advance return"));
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
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', "cash advance return status is {$model->status}"));
        }

        DB::beginTransaction();

        // * delete data
        try {
            $model->delete();

            Authorization::where('model', CashAdvancedReturn::class)->where('model_id', $model->id)->delete();
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', "cash advance return", $th->getMessage()));
        }

        DB::commit();

        return redirect()->route('admin.cash-advance-return-vendor.index')->with($this->ResponseMessageCRUD(true, 'delete', "cash advance return"));
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

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
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
     * get_cash_advance_payments
     *
     * @return mixed
     */
    public function get_cash_advance_payments(Request $request)
    {
        $branch_id = $request->branch_id ?? get_current_branch()->id;
        $branch = \App\Models\Branch::find($branch_id);

        $data = \App\Models\CashAdvancePayment::with([
            'cash_advance_payment_details.coa',
            'currency',
            'purchase'
        ])
            ->where('to_id', $request->vendor_id)
            ->where('to_model', \App\Models\Vendor::class)
            ->where('currency_id', $request->currency_id)
            ->where('project_id', $request->project_id)
            ->whereIn('status', ['approve', 'partial'])
            ->whereDoesntHave('supplier_invoice_down_payments', function ($q) use ($request) {
                $q->whereHas('supplier_invoice', function ($q) use ($request) {
                    $q->whereIn('status', ['pending', 'approve', 'revert']);
                });
            })
            ->whereDoesntHave('cash_advanced_return_details', function ($q) {
                $q->whereHas('cash_advanced_return', function ($q) {
                    $q->whereIn('status', ['pending', 'revert']);
                });
            })
            ->when($request->date, function ($query) use ($request) {
                $query->whereDate('date', '<=', Carbon::parse($request->date));
            })
            ->when(!$branch->is_primary, function ($query) use ($branch) {
                $query->where('branch_id', $branch->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->ResponseJsonData($data->where('outstanding_amount', ">", "0")->flatten());
    }

    /**
     * get_unpaid_full_supplier_invoices
     *
     * @param Request $request
     * @return mixed
     */
    public function get_unpaid_full_supplier_invoices(Request $request)
    {
        $branch_id = $request->branch_id ?? Auth::user()->branch_id;
        $branch = Branch::find($branch_id);
        $supplier_invoice_parent = \App\Models\SupplierInvoiceParent::with(['vendor.vendor_coas.coa', 'currency'])
            ->where('vendor_id', $request->vendor_id)
            ->where('currency_id', $request->currency_id)
            ->whereIn('payment_status', ['unpaid', 'partial', 'partial-paid'])
            ->whereIn('status', ['approve'])
            ->when(!$branch->is_primary, function ($query) use ($branch) {
                $query->where('branch_id', $branch->id);
            })
            ->when($request->date, function ($query) use ($request) {
                $query->whereDate('date', '<=', Carbon::parse($request->date));
            })
            ->whereDoesntHave('cash_advanced_return_invoices', function ($q) {
                $q->whereHas('cash_advanced_return', function ($q) {
                    $q->whereIn('status', ['pending', 'revert']);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $supplier_invoice_ids = $supplier_invoice_parent->pluck('reference_id')->toArray();
        $supplier_invoice = \App\Models\SupplierInvoice::with([
            'detail.item_receiving_report',
            'currency',
        ])
            ->whereIn('id', $supplier_invoice_ids)
            ->get();

        $results = $supplier_invoice_parent->map(function ($supplier_invoice_parent) use ($supplier_invoice) {
            $supplier_invoices = $supplier_invoice->where('id', $supplier_invoice_parent->reference_id)
                ->values()
                ->all();

            $supplier_invoices = collect($supplier_invoices)->map(function ($supplier_invoice) {
                $details = $supplier_invoice->detail->map(function ($detail) {
                    $detail->item_receiving_report->outstanding = 0;

                    return $detail;
                });

                $supplier_invoice->detail = $details;
                return $supplier_invoice;
            });

            $supplier_invoice_parent->supplier_invoice = $supplier_invoices;

            return $supplier_invoice_parent;
        });

        return $this->ResponseJsonData($results);
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
            'invoice_currency',
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

        $cash_advance_payment_ids = $model->cashAdvancedReturnDetails->pluck('reference_id')->toArray();
        $supplier_invoice_ids = $model->cashAdvancedReturnInvoices->pluck('reference_id')->toArray();

        $cash_advance_payments = \App\Models\CashAdvancePayment::with(['cash_advance_payment_details.coa', 'currency', 'purchase'])
            ->whereIn('id', $cash_advance_payment_ids)
            ->orderBy('created_at', 'desc')
            ->get();

        $supplier_invoices = \App\Models\SupplierInvoiceParent::with(['vendor.vendor_coas.coa', 'currency'])
            ->whereIn('id', $supplier_invoice_ids)
            ->orderBy('created_at', 'desc')
            ->get();

        $cash_advance_return_invoice = \App\Models\CashAdvanceReturnInvoiceDetail::with('item_receiving_report')->whereIn('cash_advance_return_invoice_id', $model->cashAdvancedReturnInvoices->pluck('id')->toArray())->get();

        $model->cashAdvancedReturnDetails->each(function ($d, $key) use ($cash_advance_payments) {
            $d->reference = $cash_advance_payments->where('id', $d->reference_id)->first();
        });

        $model->cashAdvancedReturnInvoices->each(function ($d, $key) use ($supplier_invoices, $cash_advance_return_invoice) {
            $d->reference = $supplier_invoices->where('id', $d->reference_id)->first();
            $details = $cash_advance_return_invoice->where('cash_advance_return_invoice_id', $d->id)->values()->all();

            $details = collect($details)->map(function ($detail) {
                $detail->item_receiving_report->outstanding = 0;

                return $detail;
            });

            $d->reference->detail = $details;
        });

        return $this->ResponseJsonData($model);
    }

    public function export($id, Request $request)
    {
        $model = CashAdvancedReturn::findOrFail(decryptId($id));
        $title = "Pengembalian Uang Muka " . $model->type;

        $fileName = $title . ' - ' . ucfirst($model->code) . '.pdf';

        $qr_url = route('cash-advance-return-vendor.export', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $pdf = Pdf::loadview("admin.cash-advance-return-$model->type.export", compact('model', 'qr'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ??  'portrait');

        return $pdf->stream($fileName);
    }
}
