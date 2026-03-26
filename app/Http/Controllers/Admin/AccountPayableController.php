<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\FundSubmission;
use App\Models\AccountPayable as model;
use App\Models\AccountPayable;
use App\Models\AccountPayableCustomer;
use App\Models\AccountPayableDetail;
use App\Models\AccountPayableDetailLpb;
use App\Models\AccountPayableOther;
use App\Models\AccountPayablePurchaseReturn;
use App\Models\Authorization;
use App\Models\Branch;
use App\Models\SupplierInvoicePayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AccountPayableController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'account-payable';

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
            $data = model::join('vendors', 'vendors.id', 'account_payables.vendor_id')
                ->join('currencies', 'currencies.id', 'account_payables.currency_id')
                ->leftJoin('bank_code_mutations', function ($q) {
                    $q->on('account_payables.id', 'bank_code_mutations.ref_id')
                        ->where('bank_code_mutations.ref_model', AccountPayable::class);
                })
                ->select('account_payables.*', 'vendors.nama as vendor_nama', 'currencies.nama as currency_nama')
                ->when($request->from_date, function ($query, $from_date) {
                    return $query->whereDate('account_payables.date', '>=', Carbon::parse($from_date));
                })
                ->when($request->to_date, function ($query, $to_date) {
                    return $query->whereDate('account_payables.date', '<=', Carbon::parse($to_date));
                })
                ->groupBy('account_payables.id');

            if ($request->vendor_id) {
                $data->where('account_payables.vendor_id', $request->vendor_id);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', function ($row) {
                    return localDate($row->date);
                })
                ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->bank_code_mutation,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('total', function ($row) {
                    return floatDotFormat($row->total);
                })
                ->editColumn('status', function ($row) {
                    $status = incoming_payment_status()[$row->status];
                    $badge = '<div class="badge badge-lg badge-' . $status['color'] . '">
                                    ' . $status['text'] . '
                                </div>';

                    return $badge;
                })
                ->addColumn('action', function ($row) {
                    $btn = $row->check_available_date;

                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'btn_config' => [
                            'detail' => [
                                'display' => true,
                            ],
                            'edit' => [
                                'display' => false,
                            ],
                            'delete' => [
                                'display' => in_array($row->status, ['pending', 'revert']) && $btn,
                            ],
                        ],
                    ]);
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.' . $this->view_folder . '.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.' . $this->view_folder . '.create');
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
        try {
            $fund_submission = FundSubmission::findOrFail($request->fund_submission_id);
            $branch = Branch::find($fund_submission->branch_id);

            // Condition to check date more then fund submission
            if (Carbon::parse($fund_submission->date)->gt(Carbon::parse($request->date))) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal pembayaran tidak boleh kurang dari tanggal pengajuan dana!'));
            }

            $model = new model();
            $model->loadModel([
                'code' => generate_code(AccountPayable::class, 'code', 'date', 'AP', date: Carbon::parse($request->date), branch_sort: $fund_submission->branch->sort),
                'branch_id' => $fund_submission->branch_id,
                'fund_submission_id' => $fund_submission->id,
                'project_id' => $fund_submission->project_id,
                'vendor_id' => $fund_submission->to_id,
                'coa_id' => $request->coa_id ?? $fund_submission->fund_submission_supplier->coa_id,
                'currency_id' => $fund_submission->currency_id,
                'supplier_invoice_currency_id' => $fund_submission->fund_submission_supplier->currency_id,
                'exchange_rate' => thousand_to_float($request->exchange_rate ?? $fund_submission->exchange_rate),
                'date' => Carbon::parse($request->date),
                'status' => 'pending',
                'note' => $fund_submission->fund_submission_supplier->note,
                'change_bank_reason' => $request->change_bank_reason,
                'customer_id' => $fund_submission->customer_id,
            ]);

            if (!$model->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
            }

            $model->save();

            $is_pay_with_local_currency = $model->currency->is_local;
            $foreign_operation = $is_pay_with_local_currency ? '/' : '*';
            foreach ($fund_submission->fund_submission_supplier_details as $key => $detail) {
                // CHECK CURRENCY AND SUPPLIER INVOICE CURRENCY
                $is_same_currency = $model->currency_id == $model->supplier_invoice_currency_id;
                $si_currency_is_local = $model->supplier_invoice_currency->is_local;
                $local_operation = $is_pay_with_local_currency ? '*' : '/';
                $rate = $is_same_currency ? 1 : $model->exchange_rate;

                $exchange_rate = $model->exchange_rate;
                $amount = $detail->amount;
                $amount_foreign = $amount / $model->exchange_rate;

                if ($is_same_currency) {
                    $amount_foreign = $amount;
                }

                if ($si_currency_is_local) {
                    $amount_foreign = $amount * $model->exchange_rate;
                }

                // $gap = ($model->exchange_rate - $detail->supplier_invoice_parent->exchange_rate) * $amount_foreign;
                $gap = ($amount_foreign * $detail->supplier_invoice_parent->exchange_rate) - ($amount_foreign * $model->exchange_rate);
                if ($si_currency_is_local) {
                    $gap = 0;
                }

                $outstanding_amount = $detail->outstanding_amount;
                $amount_gap_foreign = $outstanding_amount - $amount_foreign;

                $total_foreign = $amount_foreign;
                if ($detail->is_clearing) {
                    $total_foreign = $amount_foreign + $amount_gap_foreign;
                }

                $account_payable_detail = new AccountPayableDetail();
                $account_payable_detail->coa_id = $detail->coa_id;
                $account_payable_detail->account_payable_id = $model->id;
                $account_payable_detail->supplier_invoice_parent_id = $detail->supplier_invoice_parent_id;
                $account_payable_detail->exchange_rate = $detail->supplier_invoice_parent->exchange_rate;
                $account_payable_detail->outstanding_amount = $detail->outstanding_amount;

                $account_payable_detail->exchange_rate_gap_idr = $gap;
                // AMOUNT BASE ON ACCOUNT PAYABLE CURRENCY
                $account_payable_detail->amount = $detail->amount;
                $account_payable_detail->amount_gap = eval("return $amount_gap_foreign $local_operation $rate;");
                $account_payable_detail->total = $detail->is_clearing ? eval("return $total_foreign $local_operation $rate;") : $detail->amount;
                $account_payable_detail->exchange_rate_gap = $gap / $exchange_rate;

                // AMOUNT BASE ON SI CURRENCY
                $account_payable_detail->amount_foreign = $amount_foreign;
                $account_payable_detail->amount_gap_foreign = $amount_gap_foreign;
                $account_payable_detail->total_foreign = $total_foreign;
                $account_payable_detail->exchange_rate_gap_foreign = $gap / $exchange_rate;

                $account_payable_detail->exchange_rate_gap_note = $detail->exchange_rate_gap_note;
                $account_payable_detail->note = $detail->note;
                $account_payable_detail->is_clearing = $detail->is_clearing;
                $account_payable_detail->clearing_note = $detail->clearing_note;
                $account_payable_detail->save();

                foreach ($detail->fund_submission_supplier_lpbs as $key => $lpb) {
                    $lpb_amount = $lpb->amount;
                    $lpb_amount_foreign = $lpb_amount / $model->exchange_rate;

                    if ($is_same_currency) {
                        $lpb_amount_foreign = $lpb_amount;
                    }

                    if ($si_currency_is_local) {
                        $lpb_amount_foreign = $lpb_amount * $model->exchange_rate;
                    }

                    $account_payable_detail_lpb = new AccountPayableDetailLpb();
                    $account_payable_detail_lpb->account_payable_detail_id = $account_payable_detail->id;
                    $account_payable_detail_lpb->item_receiving_report_id = $lpb->item_receiving_report_id;
                    $account_payable_detail_lpb->outstanding = $lpb->outstanding;
                    $account_payable_detail_lpb->amount = $lpb_amount;
                    $account_payable_detail_lpb->amount_foreign = $lpb_amount_foreign;
                    $account_payable_detail_lpb->save();
                }
            }

            foreach ($fund_submission->fund_submission_customers ?? [] as $key => $detail) {
                // CHECK CURRENCY AND SUPPLIER INVOICE CURRENCY
                $is_same_currency = $model->currency_id == $model->supplier_invoice_currency_id;
                $is_pay_with_local_currency = $model->currency->is_local;
                $si_currency_is_local = $model->supplier_invoice_currency->is_local;
                $foreign_operation = $is_pay_with_local_currency ? '/' : '*';
                $local_operation = $is_pay_with_local_currency ? '*' : '/';
                $rate = $is_same_currency ? 1 : $model->exchange_rate;

                $exchange_rate = $model->exchange_rate;
                $receive_amount = $detail->receive_amount;
                $receive_amount_foreign = $receive_amount / $model->exchange_rate;

                if ($is_same_currency) {
                    $receive_amount_foreign = $receive_amount;
                }

                if ($si_currency_is_local) {
                    $receive_amount_foreign = $receive_amount * $model->exchange_rate;
                }

                $gap = ($receive_amount_foreign * $detail->invoice_parent->exchange_rate) - ($receive_amount_foreign * $model->exchange_rate);
                if ($si_currency_is_local) {
                    $gap = 0;
                }

                $outstanding_amount = $detail->outstanding_amount;
                $receive_amount_gap_foreign = $outstanding_amount - $receive_amount_foreign;

                $total_foreign = $receive_amount_foreign;
                if ($detail->is_clearing) {
                    $total_foreign = $receive_amount_foreign + $receive_amount_gap_foreign;
                }

                $account_payable_customer = new AccountPayableCustomer();
                $account_payable_customer->fund_submission_customer_id = $detail->id;
                $account_payable_customer->coa_id = $detail->coa_id;
                $account_payable_customer->account_payable_id = $model->id;
                $account_payable_customer->invoice_parent_id = $detail->invoice_parent_id;
                $account_payable_customer->exchange_rate = $detail->invoice_parent->exchange_rate;
                $account_payable_customer->outstanding_amount = $detail->outstanding_amount;

                $account_payable_customer->exchange_rate_gap_idr = $gap;
                // AMOUNT BASE ON ACCOUNT PAYABLE CURRENCY
                $account_payable_customer->receive_amount = $detail->receive_amount;
                $account_payable_customer->receive_amount_gap = eval("return $receive_amount_gap_foreign $local_operation $rate;");
                $account_payable_customer->total = $detail->is_clearing ? eval("return $total_foreign $local_operation $rate;") : $detail->receive_amount;
                $account_payable_customer->exchange_rate_gap = $gap / $exchange_rate;

                // AMOUNT BASE ON SI CURRENCY
                $account_payable_customer->receive_amount_foreign = $receive_amount_foreign;
                $account_payable_customer->receive_amount_gap_foreign = $receive_amount_gap_foreign;
                $account_payable_customer->total_foreign = $total_foreign;
                $account_payable_customer->exchange_rate_gap_foreign = $gap / $exchange_rate;

                $account_payable_customer->exchange_rate_gap_note = $detail->exchange_rate_gap_note;
                $account_payable_customer->note = $detail->note;
                $account_payable_customer->is_clearing = $detail->is_clearing;
                $account_payable_customer->clearing_note = $detail->clearing_note;
                $account_payable_customer->save();
            }

            foreach ($fund_submission->fund_submission_purchase_returns ?? [] as $key => $fund_submission_purchase_return) {
                $is_same_currency = $model->currency_id == $model->supplier_invoice_currency_id;
                $is_pay_with_local_currency = $model->currency->is_local;
                $si_currency_is_local = $model->supplier_invoice_currency->is_local;
                $foreign_operation = $is_pay_with_local_currency ? '/' : '*';
                $local_operation = $is_pay_with_local_currency ? '*' : '/';
                $rate = $is_same_currency ? 1 : $model->exchange_rate;

                $exchange_rate = $model->exchange_rate;
                $amount = $fund_submission_purchase_return->amount;
                $amount_foreign = $amount / $model->exchange_rate;

                if ($is_same_currency) {
                    $amount_foreign = $amount;
                }

                if ($si_currency_is_local) {
                    $amount_foreign = $amount * $model->exchange_rate;
                }

                $gap = ($amount_foreign * $fund_submission_purchase_return->purchase_return->exchange_rate) - ($amount_foreign * $model->exchange_rate);
                if ($si_currency_is_local) {
                    $gap = 0;
                }

                $account_payable_purchase_return = new AccountPayablePurchaseReturn();
                $account_payable_purchase_return->account_payable_id = $model->id;
                $account_payable_purchase_return->purchase_return_id = $fund_submission_purchase_return->purchase_return_id;
                $account_payable_purchase_return->exchange_rate = $fund_submission_purchase_return->exchange_rate;
                $account_payable_purchase_return->outstanding_amount = $fund_submission_purchase_return->outstanding_amount;
                $account_payable_purchase_return->amount = $fund_submission_purchase_return->amount;
                $account_payable_purchase_return->amount_foreign = $amount_foreign;
                $account_payable_purchase_return->exchange_rate_gap = $gap / $exchange_rate;;
                $account_payable_purchase_return->exchange_rate_gap_idr = $gap;
                $account_payable_purchase_return->exchange_rate_gap_foreign = $gap / $exchange_rate;
                $account_payable_purchase_return->save();
            }

            foreach ($request->coa_detail_id ?? [] as $key => $coa_detail_id) {
                $debit = thousand_to_float($request->debit[$key] ?? 0);
                $account_payable_other = new AccountPayableOther();
                $account_payable_other->account_payable_id = $model->id;
                $account_payable_other->coa_id = $coa_detail_id;
                $account_payable_other->note =  $request->note_other[$key] ?? '-';
                $account_payable_other->debit = $debit;
                $account_payable_other->debit_foreign = eval("return $debit $foreign_operation $model->exchange_rate;");
                $account_payable_other->save();
            }

            $model->total = $model->account_payable_details->sum('amount') - $model->account_payable_customers->sum('receive_amount') + $model->account_payable_others->sum('debit') - $model->account_payable_purchase_returns->sum('amount');
            $model->exchange_rate_gap_total = $model->account_payable_details->sum('exchange_rate_gap_idr');
            $model->save();

            try {
                $code = generate_bank_code(
                    ref_model: AccountPayable::class,
                    ref_id: $model->id,
                    coa_id: $fund_submission->fund_submission_supplier->coa_id,
                    type: 'out',
                    date: $request->date,
                    is_save: true,
                    code: $request->sequence_code,
                );

                if (!$code) {
                    DB::rollBack();
                    $model->account_payable_details()->delete();
                    $model->account_payable_others()->delete();
                    $model->account_payable_customers()->delete();
                    $model->account_payable_purchase_returns()->delete();
                    $model->delete();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'generate kode error, periksa bank internal coa kas/bank yang dipilih'));
                }
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'generate kode error, periksa bank internal coa kas/bank yang dipilih'));
            }

            // * if bank changed
            if ($request->coa_id == $fund_submission->fund_submission_supplier->coa_id) {
                $model->status = "approve";
            } else {
                $model->status = "pending";
            }

            $model->save();

            if ($request->coa_id != $fund_submission->fund_submission_supplier->coa_id) {
                $authorization = new \App\Http\Helpers\AuthorizationHelper();
                $authorization->init(
                    branch_id: $model->branch_id,
                    user_id: auth()->user()->id,
                    model: model::class,
                    model_id: $model->id,
                    amount: $model->total ?? 0,
                    title: "Pembayaran Hutang",
                    subtitle: Auth::user()->name . " mengajukan perubahan kas/bank - " . $model->bank_code_mutation,
                    link: route('admin.account-payable.show', $model),
                    update_status_link: route('admin.account-payable.update-status', ['id' => $model->id]),
                    division_id: auth()->user()->division_id ?? null
                );
            } else {
                $authorization = new \App\Http\Helpers\AuthorizationHelper();
                $authorization->init(
                    branch_id: $model->branch_id,
                    user_id: auth()->user()->id,
                    model: model::class,
                    model_id: $model->id,
                    amount: $model->total ?? 0,
                    title: "Pembayaran Hutang",
                    subtitle: Auth::user()->name . " menambahkan pembayaran hutang " . $model->code,
                    link: route('admin.account-payable.show', $model),
                    update_status_link: route('admin.account-payable.update-status', ['id' => $model->id]),
                    division_id: auth()->user()->division_id ?? null,
                    auto_approve: true
                );
            }
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();
        return redirect()->route("admin.outgoing-payment.index", ['tab' => 'account-payable'])->with($this->ResponseMessageCRUD());
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
        $account_payable_details = $model->account_payable_details;
        foreach ($account_payable_details as $key => $account_payable_detail) {
            $account_payable_detail->payment_informations = SupplierInvoicePayment::where('supplier_invoice_model', $account_payable_detail->supplier_invoice_parent->model_reference)
                ->where('supplier_invoice_id', $account_payable_detail->supplier_invoice_parent->reference_id)
                ->whereDate('date', '<=', $model->date)
                ->where('created_at', '<=', $model->created_at)
                ->get();

            if ($account_payable_detail->supplier_invoice_parent->type != "general") {
                foreach ($account_payable_detail->supplier_invoice_parent->reference_model->detail as $key => $detail) {
                    $detail->item_receiving_report->payment_informations = SupplierInvoicePayment::where('item_receiving_report_id', $detail->item_receiving_report->id)
                        ->whereDate('date', '<=', $model->date)
                        ->where('created_at', '<=', $model->created_at)
                        ->get();
                }
            }
        }

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: model::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );


        $authorization_logs['can_revert'] = false;
        $authorization_logs['can_void'] = $model->check_available_date && $model->status == 'approve';
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = false;
        $authorization_logs['can_void_request'] = $model->status == "approve" && $model->check_available_date;
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.{$this->view_folder}.show", compact('model', 'account_payable_details', 'auth_revert_void_button', 'authorization_log_view'));
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

        if (!$model->check_available_date) {
            return abort(403);
        }

        if (!in_array($model->status, ['pending', 'revert']) || !$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, 'Data tidak dapat diedit'));
        }

        return view("admin.{$this->view_folder}.edit", compact('model'));
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
            $model = model::find($id);
            $model->account_payable_details()->delete();
            $model->account_payable_others()->delete();
            $model->account_payable_customers()->delete();
            $model->account_payable_purchase_returns()->delete();
            $model->delete();

            Authorization::where('model', model::class)
                ->where('model_id', $id)
                ->delete();
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

        return redirect()->route("admin.outgoing-payment.index", ['tab' => 'account-payable'])->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request) {}

    public function fund_submission(Request $request, $id)
    {
        $model = FundSubmission::with('fund_submission_supplier.coa')
            ->with('project')
            ->with('fund_submission_supplier.currency')
            ->with('branch')
            ->with('currency')
            ->with('fund_submission_supplier_details.supplier_invoice_parent.currency')
            ->with('fund_submission_supplier_others.coa')
            ->find($id);

        if ($model->send_payment) {
            $model->send_payment->due_status_by_date = $model->send_payment->getDueStatus($request->date);
        }

        $data = [
            'html' => view('admin.fund-submission.__giro_table', ['send_payment' => $model->send_payment])->render(),
            'html_detail' => view('admin.account-payable._detail_table', ['model' => $model])->render(),
            'data' => $model,
        ];

        if ($request->ajax()) {
            return response()->json($data);
        }
    }

    public function update_status(Request $request, $id)
    {
        DB::beginTransaction();
        $model = model::findOrfail($id);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        }

        validate_branch($model->branch_id);

        // * saving and make response
        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);
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

    public function history($id, Request $request)
    {
        try {
            $account_payables = DB::table('account_payable_details')
                ->join('account_payables', 'account_payables.id', '=', 'account_payable_details.account_payable_id')
                ->leftJoin('bank_code_mutations', function ($j) {
                    $j->on('account_payables.id', '=', 'bank_code_mutations.ref_id')
                        ->where('bank_code_mutations.ref_model', AccountPayable::class);
                })
                ->whereNull('account_payable_details.deleted_at')
                ->whereNull('account_payables.deleted_at')
                ->whereIn('account_payables.status', ['approve'])
                ->where('account_payables.id', $id)
                ->select(
                    'account_payables.id',
                    'account_payables.code',
                    'bank_code_mutations.code as bank_code_mutation',
                    'account_payables.date',
                    'account_payables.fund_submission_id',
                    'account_payable_details.supplier_invoice_parent_id'
                )
                ->get()
                ->map(function ($item) {
                    return $item->code = $item->bank_code_mutation ?? $item->code;
                });

            $fund_submissions = DB::table('fund_submission_supplier_details')
                ->join('fund_submissions', 'fund_submissions.id', '=', 'fund_submission_supplier_details.fund_submission_id')
                ->whereNull('fund_submissions.deleted_at')
                ->whereNull('fund_submission_supplier_details.deleted_at')
                ->whereIn('fund_submissions.id', $account_payables->pluck('fund_submission_id')->toArray())
                ->select(
                    'fund_submissions.id',
                    'fund_submissions.code',
                    'fund_submissions.date',
                    'fund_submission_supplier_details.supplier_invoice_parent_id'
                )
                ->get();

            $supplier_invoices = DB::table('supplier_invoice_details')
                ->join('supplier_invoices', 'supplier_invoices.id', '=', 'supplier_invoice_details.supplier_invoice_id')
                ->join('supplier_invoice_parents', function ($j) {
                    $j->on('supplier_invoice_parents.reference_id', '=', 'supplier_invoices.id')
                        ->where('supplier_invoice_parents.model_reference', 'App\Models\SupplierInvoice');
                })
                ->whereNull('supplier_invoices.deleted_at')
                ->whereNull('supplier_invoice_details.deleted_at')
                ->whereIn('supplier_invoices.status', ['approve'])
                ->whereIn('supplier_invoice_parents.id', $fund_submissions->pluck('supplier_invoice_parent_id')->toArray())
                ->select(
                    'supplier_invoices.id',
                    'supplier_invoices.code',
                    'supplier_invoices.accepted_doc_date as date',
                    'supplier_invoice_details.item_receiving_report_id',
                    'supplier_invoice_parents.id as supplier_invoice_parent_id'
                )
                ->get();

            $item_receiving_reports = DB::table('item_receiving_reports')
                ->whereIn('id', $supplier_invoices->pluck('item_receiving_report_id')->toArray())
                ->whereNull('item_receiving_reports.deleted_at')
                ->select(
                    'item_receiving_reports.id',
                    'item_receiving_reports.kode as code',
                    'item_receiving_reports.date_receive as date',
                    'item_receiving_reports.reference_id',
                    'item_receiving_reports.reference_model',
                    'item_receiving_reports.tipe'
                )
                ->get();

            $purchase_order_generals = DB::table('purchase_order_general_details')
                ->join('purchase_order_generals', 'purchase_order_generals.id', '=', 'purchase_order_general_details.purchase_order_general_id')
                ->whereNull('purchase_order_generals.deleted_at')
                ->whereIn('purchase_order_general_id', $item_receiving_reports
                    ->where('reference_model', 'App\Models\PurchaseOrderGeneral')
                    ->pluck('reference_id')->toArray())
                ->select(
                    'purchase_order_generals.id',
                    'purchase_order_generals.code',
                    'purchase_order_generals.date',
                    'purchase_order_generals.status',
                    'purchase_order_general_details.purchase_request_id',
                )
                ->get();

            $purchase_order_services = DB::table('purchase_order_service_details')
                ->join('purchase_order_services', 'purchase_order_services.id', '=', 'purchase_order_service_details.purchase_order_service_id')
                ->whereNull('purchase_order_services.deleted_at')
                ->whereIn('purchase_order_services.id', $item_receiving_reports
                    ->where('reference_model', 'App\Models\PurchaseOrderService')
                    ->pluck('reference_id')->toArray())
                ->select(
                    'purchase_order_services.id',
                    'purchase_order_services.code',
                    'purchase_order_services.date',
                    'purchase_order_services.status',
                    'purchase_order_service_details.purchase_request_id',
                )
                ->get();

            $purchase_orders = DB::table('purchase_orders')
                ->whereIn('id', $item_receiving_reports
                    ->where('reference_model', 'App\Models\PoTrading')
                    ->pluck('reference_id')->toArray())
                ->select(
                    'purchase_orders.id',
                    'purchase_orders.nomor_po as code',
                    'purchase_orders.tanggal as date',
                    'purchase_orders.status',
                )
                ->get();

            $purchase_transports = DB::table('purchase_transports')
                ->whereIn('id', $item_receiving_reports
                    ->where('reference_model', 'App\Models\PurchaseTransport')
                    ->pluck('reference_id')->toArray())
                ->select(
                    'purchase_transports.id',
                    'purchase_transports.kode as code',
                    'purchase_transports.target_delivery as date',
                    'purchase_transports.status',
                )
                ->get();

            $purchase_request_id = $purchase_order_generals->pluck('purchase_request_id')->toArray();
            $purchase_request_id = array_merge($purchase_request_id, $purchase_order_services->pluck('purchase_request_id')->toArray());

            $purhase_requests = DB::table('purchase_requests')
                ->whereIn('id', $purchase_request_id)
                ->whereNull('deleted_at')
                ->whereIn('status', ['approve', 'done', 'partial'])
                ->select(
                    'id',
                    'kode as code',
                    'tanggal as date'
                )
                ->get();

            $purchase_returns = DB::table('purchase_returns')
                ->whereIn('item_receiving_report_id', $item_receiving_reports->pluck('id')->toArray())
                ->whereIn('status', ['approve', 'done'])
                ->whereNull('purchase_returns.deleted_at')
                ->select(
                    'id',
                    'code',
                    'date',
                    'item_receiving_report_id'
                )
                ->get();

            $supplier_invoices = $supplier_invoices->map(function ($item) {
                $item->link = route('admin.supplier-invoice.show', $item->id);
                $item->menu = 'purchase invoice';
                return $item;
            });

            $purchase_order_generals = $purchase_order_generals->map(function ($item) {
                $item->link = route('admin.purchase-order-general.show', $item->id);
                $item->menu = 'purchase order general';
                return $item;
            });

            $purchase_order_services = $purchase_order_services->map(function ($item) {
                $item->link = route('admin.purchase-order-service.show', $item->id);
                $item->menu = 'purchase order service';
                return $item;
            });

            $purchase_orders = $purchase_orders->map(function ($item) {
                $item->link = route('admin.purchase-order.show', $item->id);
                $item->menu = 'purchase order trading';
                return $item;
            });

            $purchase_transports = $purchase_transports->map(function ($item) {
                $item->link = route('admin.purchase-order-transport.show', $item->id);
                $item->menu = 'purchase order transport';
                return $item;
            });

            $item_receiving_reports = $item_receiving_reports->map(function ($item) {
                if ($item->tipe == 'jasa') {
                    $item_type = 'item-receiving-report-service';
                } elseif ($item->tipe == 'general') {
                    $item_type = 'item-receiving-report-general';
                } elseif ($item->tipe == 'trading') {
                    $item_type = 'item-receiving-report-trading';
                } elseif ($item->tipe == 'transport') {
                    $item_type = 'item-receiving-report-transport';
                }


                $item->link = route('admin.' . $item_type . '.show', $item->id);
                $item->menu = 'penerimaan barang ' . $item->tipe;
                return $item;
            });

            $purhase_requests = $purhase_requests->map(function ($item) {
                $item->link = route('admin.purchase-request.show', $item->id);
                $item->menu = 'purchase request';
                return $item;
            });

            $purchase_returns = $purchase_returns->map(function ($item) {
                $item->link = route('admin.purchase-return.show', $item->id);
                $item->menu = 'retur pembelian';
                return $item;
            });
            $fund_submissions = $fund_submissions->map(function ($item) {
                $item->link = route('admin.fund-submission.show', $item->id);
                $item->menu = 'pengajuan dana';
                return $item;
            });

            $account_payables = $account_payables->map(function ($item) {
                $item->link = route('admin.account-payable.show', $item->id);
                $item->menu = 'pelunasan hutang';
                return $item;
            });

            $histories = $purhase_requests->unique('id')
                ->merge($purchase_order_generals->unique('id'))
                ->merge($purchase_order_services->unique('id'))
                ->merge($purchase_orders->unique('id'))
                ->merge($purchase_transports->unique('id'))
                ->merge($item_receiving_reports->unique('id'))
                ->merge($supplier_invoices->unique('id'))
                ->merge($fund_submissions->unique('id'))
                ->merge($account_payables->unique('id'))
                ->merge($purchase_returns->unique('id'))
                ->sortBy('date')
                ->values()
                ->all();
            return response()->json([
                'success' => true,
                'data' => $histories
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
