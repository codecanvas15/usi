<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\Branch;
use App\Models\FundSubmission as model;
use App\Models\FundSubmission;
use App\Models\FundSubmissionCustomer;
use App\Models\FundSubmissionPurchaseReturn;
use App\Models\FundSubmissionSupplier;
use App\Models\FundSubmissionSupplierDetail;
use App\Models\FundSubmissionSupplierLpb;
use App\Models\FundSubmissionSupplierOther;
use App\Models\InvoiceParent;
use App\Models\PurchaseReturn;
use App\Models\SendPayment;
use App\Models\SupplierInvoiceParent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FundSubmissionLpbController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'fund-submission';

    /**
     * search  value
     *
     * @var string|null
     */
    protected string|null $search = null;


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        // * create data
        $model = new model();
        $branch = Branch::find($request->branch_id);

        $to_name = $request->to_name ?? '-';
        if ($request->to_model) {
            $to_name = $request->to_model::find($request->to_id);
        }

        if ($request->due_date) {
            // if due date less than date
            if (Carbon::parse($request->due_date)->lt(Carbon::parse($request->date))) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'tanggal jatuh tempo tidak boleh lebih kecil dari tanggal pengajuan dana!'));
            }
        }

        $branch_id = $request->branch_id ?? Auth::user()->branch_id;
        $branch = Branch::find($branch_id);
        $model->loadModel([
            'code' => generate_code(model::class, 'code', 'date', "PD", branch_sort: $branch->sort ?? null, date: $request->date),
            'status' => 'pending',
            'created_by' => auth()->user()->id,
            'branch_id' => $branch_id,
            'date' => Carbon::parse($request->date),
            'item' => strtolower($request->item),
            'currency_id' => $request->currency_id,
            'exchange_rate' => thousand_to_float($request->exchange_rate),
            'project_id' => $request->project_id,
            'reference' => $request->referensi,
            'coa_id' => $request->coa_id,
            'purchase_id' => $request->purchase_id,
            'is_giro' => $request->is_giro,
            'giro_number' => $request->cheque_no,
            'giro_liquid_date' => Carbon::parse($request->due_date),
            'to_model' => $request->to_model,
            'to_id' => $request->to_id,
            'to_name' => $to_name->name ?? $to_name->nama ?? $to_name,
            'keterangan' => $request->keterangan ?? '-',
            'customer_id' => $request->customer_id ?? null,
        ]);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        }

        // * saving
        try {
            $model->save();

            if ($request->is_giro) {
                $send_payment = new SendPayment();
                $branch = Branch::find($model->branch_id);

                $send_payment->loadModel(
                    [
                        'fund_submission_id' => $model->id,
                        'branch_id' => $model->branch_id,
                        'code' => generate_code(SendPayment::class, 'code', 'date', 'GRK', branch_sort: $branch->sort ?? null, date: Carbon::parse($request->date)),
                        'date' => Carbon::parse($request->date),
                        'due_date' => Carbon::parse($request->due_date),
                        'cheque_no' => $request->cheque_no,
                        'from_bank' => $model->coa->bank_internal->nama_bank ?? '',
                        'realization_bank' => $request->realization_bank,
                        'status' => 'pending',
                        'reject_reason' => '',
                    ]
                );

                $send_payment->save();
            }

            $fund_submission_supplier = new FundSubmissionSupplier();
            $fund_submission_supplier->fund_submission_id = $model->id;
            $fund_submission_supplier->coa_id = $request->coa_id;
            $fund_submission_supplier->currency_id = $request->supplier_invoice_currency_id;
            $fund_submission_supplier->note = $request->parent_note ?? '-';
            $fund_submission_supplier->save();

            // CHECK CURRENCY AND SUPPLIER INVOICE CURRENCY
            $is_same_currency = $model->currency_id == $fund_submission_supplier->currency_id;
            $is_pay_with_local_currency = $model->currency->is_local;
            $si_currency_is_local = $fund_submission_supplier->currency->is_local;
            $foreign_operation = $is_pay_with_local_currency ? '/' : '*';
            $local_operation = $is_pay_with_local_currency ? '*' : '/';
            $rate = $is_same_currency ? 1 : $model->exchange_rate;

            // !1 SAVE SUPPLIER INVOICE
            foreach ($request->supplier_invoice_parent_id ?? [] as $key => $supplier_invoice_parent_id) {
                $supplier_invoice = SupplierInvoiceParent::find($supplier_invoice_parent_id);
                $amount = (float) $request->amount[$key];
                $outstanding_amount = (float) $request->outstanding_amount[$key];
                $amount_foreign = (float) $request->amount_foreign[$key];
                $exchange_rate = $supplier_invoice->exchange_rate;

                // $gap = ($model->exchange_rate - $exchange_rate) * $amount_foreign;
                $gap = ($amount_foreign * $supplier_invoice->exchange_rate) - ($amount_foreign * $model->exchange_rate);
                if ($si_currency_is_local) {
                    $gap = 0;
                }

                $amount_gap_foreign = $outstanding_amount - $amount_foreign;
                $total_foreign = $amount_foreign;
                $is_clearing = $request->is_clearing[$key] ?? 0;
                if ($is_clearing == 1) {
                    $total_foreign = $amount_foreign + $amount_gap_foreign;
                }

                $supplier_invoice_parent = SupplierInvoiceParent::find($supplier_invoice_parent_id);
                $fund_submission_supplier_detail = new FundSubmissionSupplierDetail();
                $fund_submission_supplier_detail->coa_id = $request->clearing_coa_id[$key] ?? null;
                $fund_submission_supplier_detail->fund_submission_id = $model->id;
                $fund_submission_supplier_detail->supplier_invoice_parent_id = $supplier_invoice_parent_id;
                $fund_submission_supplier_detail->exchange_rate = $supplier_invoice_parent->exchange_rate;
                $fund_submission_supplier_detail->outstanding_amount = (float) $request->outstanding_amount[$key];

                $fund_submission_supplier_detail->exchange_rate_gap_idr = $gap;
                // AMOUNT BASE ON FUND SUBMISSION CURRENCY
                $fund_submission_supplier_detail->amount = $amount;
                $fund_submission_supplier_detail->amount_gap = eval("return $amount_gap_foreign $local_operation $rate;");
                $fund_submission_supplier_detail->total = $is_clearing ? eval("return $total_foreign $local_operation $rate;") : $amount;
                $fund_submission_supplier_detail->exchange_rate_gap = $gap / $exchange_rate;

                // AMOUNT BASE ON SI CURRENCY
                $fund_submission_supplier_detail->amount_gap_foreign = $amount_gap_foreign;
                $fund_submission_supplier_detail->amount_foreign = $amount_foreign;
                $fund_submission_supplier_detail->total_foreign = $total_foreign;
                $fund_submission_supplier_detail->exchange_rate_gap_foreign = $gap / $exchange_rate;

                $fund_submission_supplier_detail->exchange_rate_gap_note = $request->exchange_rate_gap_note[$key] ?? '-';
                $fund_submission_supplier_detail->note = $request->note[$key] ?? '-';
                $fund_submission_supplier_detail->is_clearing = $request->is_clearing[$key];
                $fund_submission_supplier_detail->clearing_note = $request->clearing_note[$key] ?? '-';
                $fund_submission_supplier_detail->save();

                $item_receiving_reports = json_decode($request->item_receiving_reports[$key]);
                foreach ($item_receiving_reports as $key => $item_receiving_report) {
                    $fund_submission_lpb = new FundSubmissionSupplierLpb();
                    $fund_submission_lpb->fund_submission_supplier_detail_id = $fund_submission_supplier_detail->id;
                    $fund_submission_lpb->item_receiving_report_id = $item_receiving_report->id;
                    $fund_submission_lpb->outstanding = (float) $item_receiving_report->outstanding;
                    $fund_submission_lpb->amount = (float) $item_receiving_report->amount;
                    $fund_submission_lpb->amount_foreign = (float) $item_receiving_report->amount_foreign;
                    $fund_submission_lpb->save();
                }
            }

            // !! SAVE ADJUSTMENT
            foreach ($request->coa_detail_id ?? [] as $key => $coa_detail_id) {
                $debit = thousand_to_float($request->debit[$key] ?? 0);
                $fund_submission_supplier_other = new FundSubmissionSupplierOther();
                $fund_submission_supplier_other->fund_submission_id = $model->id;
                $fund_submission_supplier_other->coa_id = $coa_detail_id;
                $fund_submission_supplier_other->note = $request->note_other[$key];
                $fund_submission_supplier_other->debit = $debit;
                $fund_submission_supplier_other->debit_foreign = eval("return $debit $foreign_operation $model->exchange_rate;");
                $fund_submission_supplier_other->save();
            }

            // !! SAVE INVOICE
            foreach ($request->invoice_id ?? [] as $key => $invoice_id) {
                $invoice = InvoiceParent::find($invoice_id);
                $outstanding_amount = (float) $request->outstanding_amount_customer[$key];
                $receive_amount = (float) $request->receive_amount_customer[$key];
                $receive_amount_foreign = (float) $request->receive_amount_foreign_customer[$key];
                $exchange_rate = $invoice->exchange_rate;

                // $gap = ($model->exchange_rate - $exchange_rate) * $receive_amount_foreign;
                $gap = ($receive_amount_foreign * $invoice->exchange_rate) - ($receive_amount_foreign * $model->exchange_rate);
                if ($si_currency_is_local) {
                    $gap = 0;
                }

                $receive_amount_gap_foreign = $outstanding_amount - $receive_amount_foreign;
                $total_foreign = $receive_amount_foreign;
                $is_clearing = $request->is_clearing_customer[$key] ?? 0;
                if ($is_clearing == 1) {
                    $total_foreign = $receive_amount_foreign + $receive_amount_gap_foreign;
                }

                $detail = new FundSubmissionCustomer();
                $detail->coa_id = $request->clearing_coa_id_customer[$key] ?? null;
                $detail->fund_submission_id = $model->id;
                $detail->invoice_parent_id = $invoice_id;
                $detail->exchange_rate =  $exchange_rate;
                $detail->outstanding_amount = $outstanding_amount;

                $detail->exchange_rate_gap_idr = $gap;
                // AMOUNT BASE ON AR CURRENCY
                $detail->receive_amount = $receive_amount;
                $detail->receive_amount_gap = eval("return $receive_amount_gap_foreign $local_operation $rate;");
                $detail->total = $is_clearing ? eval("return $total_foreign $local_operation $rate;") : $receive_amount;
                $detail->exchange_rate_gap = $gap / $exchange_rate;

                // AMOUNT BASE ON INVOICE CURRENCY
                $detail->receive_amount_gap_foreign = $receive_amount_gap_foreign;
                $detail->receive_amount_foreign = $receive_amount_foreign;
                $detail->total_foreign = $total_foreign;
                $detail->exchange_rate_gap_foreign = $gap / $exchange_rate;

                $detail->exchange_rate_gap_note = $request->exchange_rate_gap_note_customer[$key] ?? '-';
                $detail->note = $request->note_customer[$key] ?? '-';
                $detail->is_clearing = $request->is_clearing_customer[$key] ?? '0';
                $detail->clearing_note = $request->clearing_note_customer[$key] ?? '-';
                $detail->save();
            }

            // save return
            foreach ($request->purchase_return_id ?? [] as $key => $purchase_return_id) {
                $purchase_return = PurchaseReturn::find($purchase_return_id);
                $outstanding_amount = thousand_to_float($request->return_outstanding_amount[$key]);
                $return_amount = thousand_to_float($request->return_amount[$key]);
                $return_amount_foreign = thousand_to_float($request->return_amount_foreign[$key]);
                $exchange_rate = $purchase_return->exchange_rate;
                // $gap = ($model->exchange_rate - $exchange_rate) * $return_amount_foreign;
                $gap = ($return_amount_foreign * $exchange_rate)  - ($return_amount_foreign * $model->exchange_rate);
                if ($si_currency_is_local) {
                    $gap = 0;
                }
                $exchange_rate_gap_idr = $gap;
                $exchange_rate_gap = $gap / $exchange_rate;
                $exchange_rate_gap_foreign = $gap / $exchange_rate;

                $fund_submission_purchase_return = new FundSubmissionPurchaseReturn();
                $fund_submission_purchase_return->fund_submission_id = $model->id;
                $fund_submission_purchase_return->purchase_return_id = $purchase_return_id;
                $fund_submission_purchase_return->exchange_rate = $exchange_rate;
                $fund_submission_purchase_return->outstanding_amount = $outstanding_amount;
                $fund_submission_purchase_return->amount = $return_amount;
                $fund_submission_purchase_return->amount_foreign = $return_amount_foreign;
                $fund_submission_purchase_return->exchange_rate_gap_idr = $exchange_rate_gap_idr;
                $fund_submission_purchase_return->exchange_rate_gap = $exchange_rate_gap;
                $fund_submission_purchase_return->exchange_rate_gap_foreign = $exchange_rate_gap_foreign;
                $fund_submission_purchase_return->save();
            }

            $model->total = $model->fund_submission_supplier_details->sum('amount') - $model->fund_submission_customers->sum('receive_amount') + $model->fund_submission_supplier_others->sum('debit') - $model->fund_submission_purchase_returns->sum('amount');
            $model->save();
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: FundSubmission::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "Pengajuan Dana $model->item",
                subtitle: Auth::user()->name . " mengajukan Pengajuan Dana $model->item " . $model->code,
                link: route('admin.fund-submission.show', $model),
                update_status_link: route('admin.fund-submission.update-status', ['id' => $model->id]),
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

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD());
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
        DB::beginTransaction();
        // * create data
        $model = model::findOrFail($id);
        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        }
        $to_name = $request->to_name ?? '-';
        if ($request->to_model) {
            $to_name = $request->to_model::find($request->to_id);
        }

        if (strtolower($request->item) != $model->item) {
            $model->fund_submission_generals()->delete();
            $model->fund_submission_cash_advance()->delete();
        }

        if ($request->due_date) {
            // if due date less than date
            if (strtotime($request->due_date) < strtotime($model->date)) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'tanggal jatuh tempo tidak boleh lebih kecil dari tanggal pengajuan dana!'));
            }
        }

        $branch_id = $request->branch_id ?? Auth::user()->branch_id;
        $branch = Branch::find($branch_id);

        $model->loadModel([
            'branch_id' =>  $branch_id,
            'date' => Carbon::parse($request->date),
            'item' => strtolower($request->item),
            'currency_id' => $request->currency_id,
            'exchange_rate' => thousand_to_float($request->exchange_rate),
            'project_id' => $request->project_id,
            'reference' => $request->referensi,
            'is_giro' => $request->is_giro,
            'giro_number' => $request->cheque_no,
            'giro_liquid_date' => Carbon::parse($request->due_date),
            'coa_id' => $request->coa_id,
            'purchase_id' => $request->purchase_id,
            'to_model' => $request->to_model,
            'to_id' => $request->to_id,
            'to_name' => $to_name->name ?? $to_name->nama ?? $to_name,
            'keterangan' => $request->keterangan ?? '-',
            'customer_id' => $request->customer_id ?? null,
        ]);

        // * saving
        try {
            $model->save();

            if ($request->is_giro) {
                $send_payment = SendPayment::where('fund_submission_id', $model->id)
                    ->first();

                if (!$send_payment) {
                    $send_payment = new SendPayment();
                    $send_payment->status = "pending";
                }

                $branch = Branch::find($model->branch_id);

                $send_payment->loadModel(
                    [
                        'fund_submission_id' => $model->id,
                        'branch_id' => $model->branch_id,
                        'code' => generate_code(SendPayment::class, 'code', 'date', 'GRK', branch_sort: $branch->sort ?? null, date: Carbon::parse($request->date)),
                        'date' => Carbon::parse($request->date),
                        'due_date' => Carbon::parse($request->due_date),
                        'cheque_no' => $request->cheque_no,
                        'from_bank' => $model->coa->bank_internal->nama_bank ?? '',
                        'realization_bank' => $request->realization_bank,
                        'reject_reason' => '',
                    ]
                );

                $send_payment->save();
            } else {
                $send_payment = SendPayment::where('fund_submission_id', $model->id)
                    ->delete();
            }

            $fund_submission_supplier = FundSubmissionSupplier::where('fund_submission_id', $model->id)->first();
            $fund_submission_supplier->fund_submission_id = $model->id;
            $fund_submission_supplier->coa_id = $request->coa_id;
            $fund_submission_supplier->currency_id = $request->supplier_invoice_currency_id;
            $fund_submission_supplier->note = $request->parent_note ?? '-';
            $fund_submission_supplier->save();

            $delete_fund_submission_supplier_detail = FundSubmissionSupplierDetail::where('fund_submission_id', $model->id);
            $fund_submission_supplier_detail_ids = $request->fund_submission_supplier_id ?? [];
            if (($key = array_search("", $fund_submission_supplier_detail_ids)) !== false) {
                unset($fund_submission_supplier_detail_ids[$key]);
            }
            if (count($fund_submission_supplier_detail_ids) > 0) {
                $delete_fund_submission_supplier_detail->whereNotIn('id', $fund_submission_supplier_detail_ids);
            }
            $delete_fund_submission_supplier_detail = $delete_fund_submission_supplier_detail->delete();

            // CHECK CURRENCY AND SUPPLIER INVOICE CURRENCY
            $is_same_currency = $model->currency_id == $fund_submission_supplier->currency_id;
            $is_pay_with_local_currency = $model->currency->is_local;
            $si_currency_is_local = $fund_submission_supplier->currency->is_local;
            $foreign_operation = $is_pay_with_local_currency ? '/' : '*';
            $local_operation = $is_pay_with_local_currency ? '*' : '/';
            $rate = $is_same_currency ? 1 : $model->exchange_rate;

            // !! SAVE SUPPLIER INVOICE
            foreach ($request->supplier_invoice_parent_id ?? [] as $key => $supplier_invoice_parent_id) {
                $supplier_invoice = SupplierInvoiceParent::find($supplier_invoice_parent_id);
                $amount = (float) $request->amount[$key];
                $outstanding_amount = (float) $request->outstanding_amount[$key];
                $amount_foreign = (float) $request->amount_foreign[$key];
                $exchange_rate = $model->exchange_rate;

                // $gap = ($model->exchange_rate - $exchange_rate) * $amount_foreign;
                $gap = ($amount_foreign * $supplier_invoice->exchange_rate) - ($amount_foreign * $model->exchange_rate);
                // CURRENCY LOCAL = CURRENCY INVOICE
                if ($si_currency_is_local) {
                    $gap = 0;
                }

                $amount_gap_foreign = $outstanding_amount - $amount_foreign;
                $total_foreign = $amount_foreign;
                $is_clearing = $request->is_clearing[$key] ?? 0;
                if ($is_clearing == 1) {
                    $total_foreign = $amount_foreign + $amount_gap_foreign;
                }

                $supplier_invoice_parent = SupplierInvoiceParent::find($supplier_invoice_parent_id);
                $fund_submission_supplier_detail = FundSubmissionSupplierDetail::find($request->fund_submission_supplier_detail_id[$key]);
                if (!$fund_submission_supplier_detail) {
                    $fund_submission_supplier_detail = new FundSubmissionSupplierDetail();
                }
                $fund_submission_supplier_detail->coa_id = $request->clearing_coa_id[$key] ?? null;
                $fund_submission_supplier_detail->fund_submission_id = $model->id;
                $fund_submission_supplier_detail->supplier_invoice_parent_id = $supplier_invoice_parent_id;
                $fund_submission_supplier_detail->exchange_rate = $supplier_invoice_parent->exchange_rate;
                $fund_submission_supplier_detail->outstanding_amount = (float) $request->outstanding_amount[$key];

                $fund_submission_supplier_detail->exchange_rate_gap_idr = $gap;
                // AMOUNT BASE ON FUND SUBMISSION CURRENCY
                $fund_submission_supplier_detail->amount = $amount;
                $fund_submission_supplier_detail->amount_gap = eval("return $amount_gap_foreign $local_operation $rate;");
                $fund_submission_supplier_detail->total = $is_clearing ? eval("return $total_foreign $local_operation $rate;") : $amount;
                $fund_submission_supplier_detail->exchange_rate_gap = $gap / $exchange_rate;

                // AMOUNT BASE ON SI CURRENCY
                $fund_submission_supplier_detail->amount_gap_foreign = $amount_gap_foreign;
                $fund_submission_supplier_detail->amount_foreign = $amount_foreign;
                $fund_submission_supplier_detail->total_foreign = $total_foreign;
                $fund_submission_supplier_detail->exchange_rate_gap_foreign = $gap / $exchange_rate;

                $fund_submission_supplier_detail->exchange_rate_gap_note = $request->exchange_rate_gap_note[$key] ?? '-';
                $fund_submission_supplier_detail->note = $request->note[$key] ?? '-';
                $fund_submission_supplier_detail->is_clearing = $request->is_clearing[$key];
                $fund_submission_supplier_detail->clearing_note = $request->clearing_note[$key] ?? '-';
                $fund_submission_supplier_detail->save();

                $item_receiving_reports = json_decode($request->item_receiving_reports[$key]);
                foreach ($item_receiving_reports as $key => $item_receiving_report) {
                    $fund_submission_lpb = FundSubmissionSupplierLpb::where('fund_submission_supplier_detail_id', $fund_submission_supplier_detail->id)
                        ->where('item_receiving_report_id', $item_receiving_report->item_receiving_report_id ?? $item_receiving_report->id)
                        ->first();

                    if (!$fund_submission_lpb) {
                        $fund_submission_lpb = new FundSubmissionSupplierLpb();
                    }
                    $fund_submission_lpb->fund_submission_supplier_detail_id = $fund_submission_supplier_detail->id;
                    $fund_submission_lpb->item_receiving_report_id = $item_receiving_report->item_receiving_report_id ?? $item_receiving_report->id;
                    $fund_submission_lpb->outstanding = (float) $item_receiving_report->outstanding;
                    $fund_submission_lpb->amount = (float) $item_receiving_report->amount;
                    $fund_submission_lpb->amount_foreign = (float) $item_receiving_report->amount_foreign;
                    $fund_submission_lpb->save();
                }
            }

            // !! SAVE ADJUSTMENT
            $fund_submission_supplier_other_ids = $request->fund_submission_supplier_other_id ?? [];
            foreach ($fund_submission_supplier_other_ids as $key => $fund_submission_supplier_other_id) {
                if (!$fund_submission_supplier_other_id) {
                    $fund_submission_supplier_other_ids[$key] = '';
                }
            }

            FundSubmissionSupplierOther::whereNotIn('id', $fund_submission_supplier_other_ids)
                ->where('fund_submission_id', $model->id)
                ->delete();

            foreach ($request->coa_detail_id ?? [] as $key => $coa_detail_id) {
                $debit = thousand_to_float($request->debit[$key] ?? 0);
                $fund_submission_supplier_other = FundSubmissionSupplierOther::find($request->fund_submission_supplier_other_id[$key]);
                if (!$fund_submission_supplier_other) {
                    $fund_submission_supplier_other = new FundSubmissionSupplierOther();
                }
                $fund_submission_supplier_other->fund_submission_id = $model->id;
                $fund_submission_supplier_other->coa_id = $coa_detail_id;
                $fund_submission_supplier_other->note = $request->note_other[$key];
                $fund_submission_supplier_other->debit = $debit;
                $fund_submission_supplier_other->debit_foreign = eval("return $debit $foreign_operation $model->exchange_rate;");
                $fund_submission_supplier_other->save();
            }

            $invoice_id = $request->invoice_id ?? [];
            FundSubmissionCustomer::where('fund_submission_id', $model->id)
                ->whereNotIn('invoice_parent_id', $invoice_id)
                ->delete();

            // !! SAVE INVOICE
            foreach ($request->invoice_id ?? [] as $key => $invoice_id) {
                $invoice = InvoiceParent::find($invoice_id);
                $outstanding_amount = (float) $request->outstanding_amount_customer[$key];
                $receive_amount = (float) $request->receive_amount_customer[$key];
                $receive_amount_foreign = (float) $request->receive_amount_foreign_customer[$key];
                $exchange_rate = $invoice->exchange_rate;

                // $gap = ($model->exchange_rate - $exchange_rate) * $receive_amount_foreign;
                $gap = $receive_amount_foreign * $invoice->exchange_rate - $receive_amount_foreign * $model->exchange_rate;
                if ($si_currency_is_local) {
                    $gap = 0;
                }

                $receive_amount_gap_foreign = $outstanding_amount - $receive_amount_foreign;
                $total_foreign = $receive_amount_foreign;
                $is_clearing = $request->is_clearing_customer[$key] ?? 0;
                if ($is_clearing == 1) {
                    $total_foreign = $receive_amount_foreign + $receive_amount_gap_foreign;
                }

                $detail = FundSubmissionCustomer::where('fund_submission_id', $model->id)
                    ->where('invoice_parent_id', $invoice_id)
                    ->first();

                if (!$detail) {
                    $detail = new FundSubmissionCustomer();
                }
                $detail->coa_id = $request->clearing_coa_id_customer[$key] ?? null;
                $detail->fund_submission_id = $model->id;
                $detail->invoice_parent_id = $invoice_id;
                $detail->exchange_rate =  $exchange_rate;
                $detail->outstanding_amount = $outstanding_amount;

                $detail->exchange_rate_gap_idr = $gap;
                // AMOUNT BASE ON AR CURRENCY
                $detail->receive_amount = $receive_amount;
                $detail->receive_amount_gap = eval("return $receive_amount_gap_foreign $local_operation $rate;");
                $detail->total = $is_clearing ? eval("return $total_foreign $local_operation $rate;") : $receive_amount;
                $detail->exchange_rate_gap = $gap / $exchange_rate;

                // AMOUNT BASE ON INVOICE CURRENCY
                $detail->receive_amount_gap_foreign = $receive_amount_gap_foreign;
                $detail->receive_amount_foreign = $receive_amount_foreign;
                $detail->total_foreign = $total_foreign;
                $detail->exchange_rate_gap_foreign = $gap / $exchange_rate;

                $detail->exchange_rate_gap_note = $request->exchange_rate_gap_note_customer[$key] ?? '-';
                $detail->note = $request->note_customer[$key] ?? '-';
                $detail->is_clearing = $request->is_clearing_customer[$key] ?? '0';
                $detail->clearing_note = $request->clearing_note_customer[$key] ?? '-';
                $detail->save();
            }

            // save return

            $purchase_return_id = $request->purchase_return_id ?? [];
            FundSubmissionPurchaseReturn::where('fund_submission_id', $model->id)
                ->whereNotIn('purchase_return_id', $purchase_return_id)
                ->delete();

            foreach ($request->purchase_return_id ?? [] as $key => $purchase_return_id) {
                $purchase_return = PurchaseReturn::find($purchase_return_id);
                $outstanding_amount = thousand_to_float($request->return_outstanding_amount[$key]);
                $return_amount = thousand_to_float($request->return_amount[$key]);
                $return_amount_foreign = thousand_to_float($request->return_amount_foreign[$key]);
                $exchange_rate = $purchase_return->exchange_rate;
                // $gap = ($model->exchange_rate - $exchange_rate) * $return_amount_foreign;
                $gap = $return_amount_foreign * $purchase_return->exchange_rate - $return_amount_foreign * $model->exchange_rate;
                if ($si_currency_is_local) {
                    $gap = 0;
                }
                $exchange_rate_gap_idr = $gap;
                $exchange_rate_gap = $gap / $exchange_rate;
                $exchange_rate_gap_foreign = $gap / $exchange_rate;

                $fund_submission_purchase_return = FundSubmissionPurchaseReturn::where('fund_submission_id', $model->id)
                    ->where('purchase_return_id', $purchase_return_id)
                    ->first();

                if (!$fund_submission_purchase_return) {
                    $fund_submission_purchase_return = new FundSubmissionPurchaseReturn();
                }
                $fund_submission_purchase_return->fund_submission_id = $model->id;
                $fund_submission_purchase_return->purchase_return_id = $purchase_return_id;
                $fund_submission_purchase_return->exchange_rate = $exchange_rate;
                $fund_submission_purchase_return->outstanding_amount = $outstanding_amount;
                $fund_submission_purchase_return->amount = $return_amount;
                $fund_submission_purchase_return->amount_foreign = $return_amount_foreign;
                $fund_submission_purchase_return->exchange_rate_gap_idr = $exchange_rate_gap_idr;
                $fund_submission_purchase_return->exchange_rate_gap = $exchange_rate_gap;
                $fund_submission_purchase_return->exchange_rate_gap_foreign = $exchange_rate_gap_foreign;
                $fund_submission_purchase_return->save();
            }

            $model->total = $model->fund_submission_supplier_details->sum('amount') - $model->fund_submission_customers->sum('receive_amount') + $model->fund_submission_supplier_others->sum('debit') - $model->fund_submission_purchase_returns->sum('amount');
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: FundSubmission::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "Pengajuan Dana $model->item",
                subtitle: Auth::user()->name . " mengajukan Pengajuan Dana $model->item " . $model->code,
                link: route('admin.fund-submission.show', $model),
                update_status_link: route('admin.fund-submission.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();
        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'edit'));
    }
}
