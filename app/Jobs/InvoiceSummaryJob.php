<?php

namespace App\Jobs;

use App\Models\InvoiceDownPaymentTax;
use App\Models\InvoiceGeneral;
use App\Models\InvoiceGeneralAdditionalTax;
use App\Models\InvoiceGeneralDetailTax;
use App\Models\InvoiceTax;
use App\Models\InvoiceTaxSummary;
use App\Models\InvoiceTrading;
use App\Models\InvoiceTradingTax;
use App\Models\InvTradingAddOnTax;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceSummaryJob implements ShouldQueue
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
            $invoice_tax_summaries = InvoiceTaxSummary::where('model_class', InvoiceGeneral::class)
                ->groupBy('model_id')
                ->get();

            $invoice_generals = InvoiceGeneral::whereNotIn('id', $invoice_tax_summaries->pluck('model_id'))
                ->whereNotIn('status', ['pending', 'reject', 'void'])
                ->get();

            foreach ($invoice_generals as $key => $invoice_general) {
                $all_invoice_taxes = new Collection();
                $invoice_general_detail_taxes = InvoiceGeneralDetailTax::whereHas('invoice_general_detail', function ($q) use ($invoice_general) {
                    $q->where('invoice_general_id', $invoice_general->id);
                })
                    ->get();

                $invoice_general_detail_taxes->map(function ($item) use ($all_invoice_taxes) {
                    $all_invoice_taxes->push($item);
                });

                $invoice_general_additional_taxes = InvoiceGeneralAdditionalTax::whereHas('invoice_general_additional', function ($q) use ($invoice_general) {
                    $q->where('invoice_general_id', $invoice_general->id);
                })
                    ->get();

                $invoice_general_additional_taxes->map(function ($item) use ($all_invoice_taxes) {
                    $all_invoice_taxes->push($item);
                });

                $all_invoice_taxes = $all_invoice_taxes
                    ->groupBy('tax_id')
                    ->map(function ($item) {
                        return $item->groupBy('value');
                    });

                foreach ($all_invoice_taxes as $key => $all_invoice_tax) {
                    foreach ($all_invoice_tax as $key2 => $all_invoice_tax2) {
                        InvoiceTaxSummary::create([
                            'model_class' => get_class($invoice_general),
                            'model_id' => $invoice_general->id,
                            'tax_id' => $key,
                            'tax_value' => $all_invoice_tax2->first()->value,
                            'tax_amount' => $all_invoice_tax2->sum('total'),
                        ]);
                    }
                }
            }

            InvoiceTax::whereIn('reference_parent_id', $invoice_generals->pluck('id')->toArray())
                ->where('reference_parent_model', InvoiceGeneral::class)
                ->forceDelete();

            $invoice_summaries = InvoiceTaxSummary::where('model_class', InvoiceGeneral::class)
                ->whereHas('tax', function ($q) {
                    $q->where('type', 'ppn');
                })
                ->whereIn('model_id', $invoice_generals->pluck('id')->toArray())
                ->get();


            $invoice_summaries = $invoice_summaries->map(function ($item) {
                $down_payment_invoice = $item->reference->invoice_parent()->down_payment_invoices;

                $down_payment_taxes = InvoiceDownPaymentTax::whereHas('invoice_down_payment', function ($q) use ($down_payment_invoice) {
                    $q->whereIn('id', $down_payment_invoice->pluck('invoice_down_payment_id')->toArray());
                })
                    ->get();

                $item->final_amount = $item->tax_amount - $down_payment_taxes->where('tax_id', $item->tax_id)
                    ->where('value', $item->tax_value)
                    ->sum('amount');

                return $item;
            });

            foreach ($invoice_summaries as $key => $invoice_tax_summary) {
                $invoice_general = $invoice_general->where('id', $invoice_tax_summary->model_id)->first();
                $dpp = $invoice_tax_summary->final_amount / $invoice_tax_summary->tax_value;

                $invoice_tax = new InvoiceTax();
                $invoice_tax->loadModel(
                    [
                        'reference_model' => InvoiceGeneral::class,
                        'reference_id' => $invoice_general->id,
                        'reference_parent_model' => InvoiceGeneral::class,
                        'reference_parent_id' => $invoice_general->id,
                        'date' => Carbon::parse($invoice_general->date),
                        'customer_id' => $invoice_general->customer_id,
                        'tax_id' => $invoice_tax_summary->tax_id,
                        'dpp' => ($dpp * $invoice_general->exchange_rate),
                        'value' => $invoice_tax_summary->tax_value,
                        'amount' => ($invoice_tax_summary->final_amount * $invoice_general->exchange_rate),
                    ]
                );
                $invoice_tax->save();
            }


            // trading
            $invoice_tax_summaries = InvoiceTaxSummary::where('model_class', InvoiceTrading::class)
                ->groupBy('model_id')
                ->get();

            $invoice_tradings = InvoiceTrading::whereNotIn('id', $invoice_tax_summaries->pluck('model_id'))
                ->whereNotIn('status', ['pending', 'reject', 'void'])
                ->get();

            foreach ($invoice_tradings as $key => $invoice_trading) {
                $invoice_trading_detail_taxes = InvoiceTradingTax::where('invoice_trading_id', $invoice_trading->id)
                    ->get();

                $all_invoice_taxes = new Collection();

                $invoice_trading_detail_taxes = $invoice_trading_detail_taxes->map(function ($item) {
                    $item->total = $item->amount;
                    return $item;
                });

                $invoice_trading_detail_taxes->map(function ($item) use (&$all_invoice_taxes) {
                    $all_invoice_taxes->push($item);
                });

                $invoice_trading_additional_taxes = InvTradingAddOnTax::whereHas('inv_trading_add_on', function ($q) use ($invoice_trading) {
                    $q->where('invoice_trading_id', $invoice_trading->id);
                })
                    ->get();

                $invoice_trading_additional_taxes->map(function ($item) use (&$all_invoice_taxes) {
                    $all_invoice_taxes->push($item);
                });

                $all_invoice_taxes = $all_invoice_taxes
                    ->groupBy('tax_id')
                    ->map(function ($item) {
                        return $item->groupBy('value');
                    });

                foreach ($all_invoice_taxes as $key => $all_invoice_tax) {
                    foreach ($all_invoice_tax as $key2 => $all_invoice_tax2) {
                        InvoiceTaxSummary::create([
                            'model_class' => get_class($invoice_trading),
                            'model_id' => $invoice_trading->id,
                            'tax_id' => $key,
                            'tax_value' => $all_invoice_tax2->first()->value,
                            'tax_amount' => $all_invoice_tax2->sum('total'),
                        ]);
                    }
                }
            }

            InvoiceTax::whereIn('reference_parent_id', $invoice_tradings->pluck('id')->toArray())
                ->where('reference_parent_model', InvoiceTrading::class)
                ->forceDelete();

            $invoice_summaries = InvoiceTaxSummary::where('model_class', InvoiceTrading::class)
                ->whereHas('tax', function ($q) {
                    $q->where('type', 'ppn');
                })
                ->whereIn('model_id', $invoice_tradings->pluck('id')->toArray())
                ->get();


            $invoice_summaries = $invoice_summaries->map(function ($item) {
                $down_payment_invoice = $item->reference->invoice_parent()->down_payment_invoices;

                $down_payment_taxes = InvoiceDownPaymentTax::whereHas('invoice_down_payment', function ($q) use ($down_payment_invoice) {
                    $q->whereIn('id', $down_payment_invoice->pluck('invoice_down_payment_id')->toArray());
                })
                    ->get();
                $item->final_amount = $item->tax_amount - $down_payment_taxes->where('tax_id', $item->tax_id)
                    ->where('value', $item->tax_value)
                    ->sum('amount');

                return $item;
            });

            foreach ($invoice_summaries as $key => $invoice_tax_summary) {
                $invoice_trading = $invoice_trading->where('id', $invoice_tax_summary->model_id)->first();
                $dpp = $invoice_tax_summary->final_amount / $invoice_tax_summary->tax_value;
                $invoice_tax = new InvoiceTax();
                $invoice_tax->loadModel(
                    [
                        'reference_model' => InvoiceTrading::class,
                        'reference_id' => $invoice_trading->id,
                        'reference_parent_model' => InvoiceTrading::class,
                        'reference_parent_id' => $invoice_trading->id,
                        'date' => Carbon::parse($invoice_trading->date),
                        'customer_id' => $invoice_trading->customer_id,
                        'tax_id' => $invoice_tax_summary->tax_id,
                        'dpp' => ($dpp * $invoice_trading->exchange_rate),
                        'value' => $invoice_tax_summary->tax_value,
                        'amount' => ($invoice_tax_summary->final_amount * $invoice_trading->exchange_rate),
                    ]
                );
                $invoice_tax->save();
            }


            DB::commit();
            Log::info('invoice summary job success');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("invoice summary job error " . $th->getMessage());
        }
    }
}
