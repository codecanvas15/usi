<?php

namespace App\Jobs;

use App\Models\ItemReceivingReport;
use App\Models\ItemReceivingReportTax;
use App\Models\LpbTaxSummary;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceDetail;
use App\Models\SupplierInvoiceTaxSummary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use stdClass;

class SupplierInvoiceTaxSummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $period;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($period = null)
    {
        $this->period = $period;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::beginTransaction();
        try {

            $supplier_invoices = SupplierInvoice::where('tax_total', '!=', 0)
                ->when($this->period, function ($query) {
                    $query->whereMonth('date', $this->period->month)
                        ->whereYear('date', $this->period->year);
                })
                ->whereNotIn('status', ['pending', 'reject', 'void'])
                ->get();

            $item_receiving_report_id = SupplierInvoiceDetail::whereIn('supplier_invoice_id', $supplier_invoices->pluck('id'))
                ->pluck('item_receiving_report_id')
                ->toArray();

            $lpb_tax_summaries = LpbTaxSummary::whereIn('item_receiving_report_id', $item_receiving_report_id)
                ->get();

            foreach ($supplier_invoices as $key => $supplier_invoice) {
                $tax_summaries = $lpb_tax_summaries->filter(function ($item) use ($supplier_invoice) {
                    return in_array($item->item_receiving_report_id, $supplier_invoice->detail()->pluck('item_receiving_report_id')->toArray());
                })
                    ->groupBy('tax_id')
                    ->map(function ($item) {
                        return $item->groupBy('value');
                    });

                foreach ($tax_summaries as $key => $tax_summary) {
                    foreach ($tax_summary as $key2 => $tax_summary2) {
                        SupplierInvoiceTaxSummary::updateOrCreate(
                            [
                                'supplier_invoice_id' => $supplier_invoice->id,
                                'tax_id' => $key,
                            ],
                            [
                                'supplier_invoice_id' => $supplier_invoice->id,
                                'tax_id' => $key,
                                'tax_value' => $tax_summary2->first()->tax_value,
                                'sub_total' => $tax_summary2->sum('sub_total'),
                                'tax_amount' => $tax_summary2->sum('tax_amount'),
                            ]
                        );

                        $si_tax_summaries = SupplierInvoiceTaxSummary::where('supplier_invoice_id', $supplier_invoice->id)
                            ->where('tax_id', $key)
                            ->get();

                        if ($si_tax_summaries->count() > 1) {
                            $si_tax_summaries->skip(1)
                                ->each(function ($si_tax_summary) {
                                    $si_tax_summary->delete();
                                });
                        }
                    }
                }
            }

            foreach ($supplier_invoices as $key => $supplier_invoice) {
                $down_payment_taxes = new Collection();
                $dpp = $supplier_invoice->sub_total;

                foreach ($supplier_invoice->supplier_invoice_down_payments as $key => $supplier_invoice_down_payment) {
                    $cash_advance_cash_advance =  $supplier_invoice_down_payment->cash_advance_payment->cash_advance_payment_details->where('type', 'cash_advance')->first();
                    $cash_advance_tax =  $supplier_invoice_down_payment->cash_advance_payment->cash_advance_payment_details->where('type', 'tax')->first();
                    if ($supplier_invoice_down_payment->cash_advance_payment->tax) {
                        $dpp -= $cash_advance_cash_advance->debit;

                        $push_down_payment_tax = new stdClass();
                        $push_down_payment_tax->tax_id = $supplier_invoice_down_payment->cash_advance_payment->tax_id;
                        $push_down_payment_tax->amount = $cash_advance_tax->debit;

                        $down_payment_taxes->push($push_down_payment_tax);
                    }
                }

                $tax_summaries = $supplier_invoice->supplier_invoice_tax_summaries->map(function ($item) use ($down_payment_taxes) {
                    $tax_down_payment = $down_payment_taxes->where('tax_id', $item->tax_id)->sum('amount');
                    $item->final_amount = ($item->tax_amount - $tax_down_payment);

                    return $item;
                });

                foreach ($tax_summaries as $key => $tax_summary) {
                    if ($tax_summary->tax->type == 'ppn') {
                        $item_receiving_report_tax = ItemReceivingReportTax::where('reference_parent_model', SupplierInvoice::class)
                            ->where('reference_parent_id', $supplier_invoice->id)
                            ->where('reference_model', SupplierInvoice::class)
                            ->where('reference_id', $supplier_invoice->id)
                            ->where('tax_id', $tax_summary->tax_id)
                            ->first();

                        if (!$item_receiving_report_tax) {
                            $item_receiving_report_tax = new ItemReceivingReportTax();
                        }

                        $item_receiving_report_tax->reference_parent_model = SupplierInvoice::class;
                        $item_receiving_report_tax->reference_parent_id = $supplier_invoice->id;
                        $item_receiving_report_tax->reference_model = SupplierInvoice::class;
                        $item_receiving_report_tax->reference_id = $supplier_invoice->id;
                        $item_receiving_report_tax->date = $supplier_invoice->date;
                        $item_receiving_report_tax->vendor_id = $supplier_invoice->vendor_id;
                        $item_receiving_report_tax->dpp = ($dpp * $supplier_invoice->exchange_rate);
                        $item_receiving_report_tax->value = $tax_summary->tax_value;
                        $item_receiving_report_tax->amount = ($tax_summary->final_amount * $supplier_invoice->exchange_rate);
                        $item_receiving_report_tax->tax_id = $tax_summary->tax_id;
                        $item_receiving_report_tax->save();

                        $item_receiving_report_taxes = ItemReceivingReportTax::where('reference_parent_model', SupplierInvoice::class)
                            ->where('reference_parent_id', $supplier_invoice->id)
                            ->where('reference_model', SupplierInvoice::class)
                            ->where('reference_id', $supplier_invoice->id)
                            ->where('tax_id', $tax_summary->tax_id)
                            ->get();

                        if ($item_receiving_report_taxes->count() > 1) {
                            $item_receiving_report_taxes->skip(1)
                                ->each(function ($item_receiving_report_tax) {
                                    $item_receiving_report_tax->delete();
                                });
                        }
                    }
                }
            }


            DB::commit();
            Log::info('supplier invoice summary job success');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("supplier invoice summary job error " . $th->getMessage());
        }
    }
}
