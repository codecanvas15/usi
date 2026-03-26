<?php

namespace App\Jobs;

use App\Models\ItemReceivingPoTrading;
use App\Models\ItemReceivingReport;
use App\Models\ItemReceivingReportCoa;
use App\Models\ItemReceivingReportDetail;
use App\Models\LpbTaxSummary;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemReceivingReportTaxSummaryJob implements ShouldQueue
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
            // GENERAL & SERVICE
            $item_receiving_report_general_coas = ItemReceivingReportCoa::whereHas('item_receiving_report', function ($q) {
                $q->whereIn('tipe', ['general', 'jasa'])
                    ->whereIn('status', ['done', 'approve'])
                    ->when($this->period, function ($q) {
                        $q->whereMonth('date_receive', Carbon::parse($this->period)->format('m'))
                            ->whereYear('date_receive', Carbon::parse($this->period)->format('Y'));
                    });
            })
                ->where(function ($q) {
                    $q->where('type', 'Tax')
                        ->orWhere('type', 'tax')
                        ->orWhere('type', 'TAX');
                })
                ->get();

            $item_receiving_report_generals = ItemReceivingReport::whereIn('id', $item_receiving_report_general_coas->pluck('item_receiving_report_id'))
                ->get();

            $item_receiving_report_general_details = ItemReceivingReportDetail::whereIn('item_receiving_report_id', $item_receiving_report_generals->pluck('id')->toArray())
                ->get();

            $item_receiving_report_general_coas = $item_receiving_report_general_coas->map(function ($item_receiving_report_general_coa) use ($item_receiving_report_general_details) {
                $item_receiving_report_general_detail = $item_receiving_report_general_details->where('id', $item_receiving_report_general_coa->item_receiving_report_detail_id)->first();
                if ($item_receiving_report_general_coa->reference) {
                    $item_receiving_report_general_coa->tax_id = $item_receiving_report_general_coa->reference->tax_id;
                    $item_receiving_report_general_coa->tax_value = $item_receiving_report_general_coa->reference->value;
                    $item_receiving_report_general_coa->sub_total = $item_receiving_report_general_detail->sub_total;
                    $item_receiving_report_general_coa->tax_amount = $item_receiving_report_general_detail->sub_total * $item_receiving_report_general_coa->tax_value;
                }

                return $item_receiving_report_general_coa;
            });

            foreach ($item_receiving_report_generals as $key => $item_receiving_report_general) {
                $lpb_coas = $item_receiving_report_general_coas->where('item_receiving_report_id', $item_receiving_report_general->id);

                $group_lpb_coas = $lpb_coas->groupBy('tax_id')
                    ->map(function ($item) {
                        return $item->groupBy('tax_value');
                    });

                foreach ($group_lpb_coas as $key => $group_lpb_coa) {
                    foreach ($group_lpb_coa as $key2 => $group_lpb_coa2) {
                        LpbTaxSummary::updateOrCreate(
                            [
                                'item_receiving_report_id' => $item_receiving_report_general->id,
                                'tax_id' => $key,
                            ],
                            [
                                'item_receiving_report_id' => $item_receiving_report_general->id,
                                'tax_id' => $key,
                                'tax_value' => $group_lpb_coa2->first()->tax_value,
                                'sub_total' => $group_lpb_coa2->sum('sub_total'),
                                'tax_amount' => $group_lpb_coa2->sum('tax_amount'),
                            ]
                        );

                        $lpb_tax_summaries = LpbTaxSummary::where('item_receiving_report_id', $item_receiving_report_general->id)
                            ->where('tax_id', $key)
                            ->get();

                        if ($lpb_tax_summaries->count() > 1) {
                            $lpb_tax_summaries->skip(1)
                                ->each(function ($lpb_tax_summary) {
                                    $lpb_tax_summary->delete();
                                });
                        }
                    }
                }
            }

            // TRADING
            $item_receiving_report_trading_coas = ItemReceivingReportCoa::whereHas('item_receiving_report', function ($q) {
                $q->where('tipe', 'trading')
                    ->whereIn('status', ['done', 'approve'])
                    ->when($this->period, function ($q) {
                        $q->whereMonth('date_receive', Carbon::parse($this->period)->format('m'))
                            ->whereYear('date_receive', Carbon::parse($this->period)->format('Y'));
                    });;
            })
                ->where(function ($q) {
                    $q->where('type', 'Tax')
                        ->orWhere('type', 'tax')
                        ->orWhere('type', 'TAX');
                })
                ->get();

            $item_receiving_report_tradings = ItemReceivingReport::whereIn('id', $item_receiving_report_trading_coas->pluck('item_receiving_report_id'))
                ->get();

            $item_receiving_report_po_tradings = ItemReceivingPoTrading::whereIn('item_receiving_report_id', $item_receiving_report_tradings->pluck('id'))
                ->get();

            $item_receiving_report_trading_coas = $item_receiving_report_trading_coas->map(function ($item_receiving_report_trading_coa) use ($item_receiving_report_po_tradings) {
                $item_receiving_report_po_trading = $item_receiving_report_po_tradings->where('item_receiving_report_id', $item_receiving_report_trading_coa->item_receiving_report_id)->first();
                if ($item_receiving_report_trading_coa->reference) {
                    if ($item_receiving_report_trading_coa->reference->tax_trading_id) {
                        $sub_total = $item_receiving_report_trading_coa->item_receiving_report->sub_total;
                        $tax_id = Tax::where('value', $item_receiving_report_trading_coa->reference->tax_trading->value)->first()->id ?? null;
                    } else {
                        $sub_total = $item_receiving_report_trading_coa->item_receiving_report->reference->po_trading_detail->harga * $item_receiving_report_po_trading->liter_15;
                        $tax_id = $item_receiving_report_trading_coa->reference->tax_id;
                    }

                    $item_receiving_report_trading_coa->tax_id = $tax_id;
                    $item_receiving_report_trading_coa->tax_value = $item_receiving_report_trading_coa->reference->value;
                    $item_receiving_report_trading_coa->sub_total = $sub_total;
                    $item_receiving_report_trading_coa->tax_amount = $sub_total * $item_receiving_report_trading_coa->tax_value;
                }

                return $item_receiving_report_trading_coa;
            });

            foreach ($item_receiving_report_tradings as $key => $item_receiving_report_trading) {
                $lpb_coas = $item_receiving_report_trading_coas->where('item_receiving_report_id', $item_receiving_report_trading->id);

                $group_lpb_coas = $lpb_coas->groupBy('tax_id')
                    ->map(function ($item) {
                        return $item->groupBy('tax_value');
                    });

                foreach ($group_lpb_coas as $key => $group_lpb_coa) {
                    foreach ($group_lpb_coa as $key2 => $group_lpb_coa2) {
                        LpbTaxSummary::updateOrCreate(
                            [
                                'item_receiving_report_id' => $item_receiving_report_trading->id,
                                'tax_id' => $key,
                            ],
                            [
                                'item_receiving_report_id' => $item_receiving_report_trading->id,
                                'tax_id' => $key,
                                'tax_value' => $group_lpb_coa2->first()->tax_value,
                                'sub_total' => $group_lpb_coa2->sum('sub_total'),
                                'tax_amount' => $group_lpb_coa2->sum('tax_amount'),
                            ]
                        );

                        $lpb_tax_summaries = LpbTaxSummary::where('item_receiving_report_id', $item_receiving_report_trading->id)
                            ->where('tax_id', $key)
                            ->get();

                        if ($lpb_tax_summaries->count() > 1) {
                            $lpb_tax_summaries->skip(1)
                                ->each(function ($lpb_tax_summary) {
                                    $lpb_tax_summary->delete();
                                });
                        }
                    }
                }
            }

            // TRANSPORT
            $item_receiving_report_transport_coas = ItemReceivingReportCoa::whereHas('item_receiving_report', function ($q) {
                $q->where('tipe', 'transport')
                    ->whereIn('status', ['done', 'approve'])
                    ->when($this->period, function ($q) {
                        $q->whereMonth('date_receive', Carbon::parse($this->period)->format('m'))
                            ->whereYear('date_receive', Carbon::parse($this->period)->format('Y'));
                    });;
            })
                ->where(function ($q) {
                    $q->where('type', 'Tax')
                        ->orWhere('type', 'tax')
                        ->orWhere('type', 'TAX');
                })
                ->get();

            $item_receiving_report_transports = ItemReceivingReport::whereIn('id', $item_receiving_report_transport_coas->pluck('item_receiving_report_id'))
                ->get();

            $item_receiving_report_transport_coas = $item_receiving_report_transport_coas->map(function ($item_receiving_report_transport_coa) use ($item_receiving_report_transports) {
                $item_receiving_report_transport = $item_receiving_report_transport_coa->item_receiving_report;
                if ($item_receiving_report_transport_coa->reference) {
                    $item_receiving_report_transport_coa->tax_id = $item_receiving_report_transport_coa->reference->tax_id;
                    $item_receiving_report_transport_coa->tax_value = $item_receiving_report_transport_coa->reference->value;
                    $item_receiving_report_transport_coa->sub_total = $item_receiving_report_transport->sub_total;
                    $item_receiving_report_transport_coa->tax_amount = $item_receiving_report_transport->sub_total * $item_receiving_report_transport_coa->tax_value;
                }

                return $item_receiving_report_transport_coa;
            });

            foreach ($item_receiving_report_transports as $key => $item_receiving_report_transport) {
                $lpb_coas = $item_receiving_report_transport_coas->where('item_receiving_report_id', $item_receiving_report_transport->id);

                $group_lpb_coas = $lpb_coas->groupBy('tax_id')
                    ->map(function ($item) {
                        return $item->groupBy('tax_value');
                    });

                foreach ($group_lpb_coas as $key => $group_lpb_coa) {
                    foreach ($group_lpb_coa as $key2 => $group_lpb_coa2) {
                        LpbTaxSummary::updateOrCreate(
                            [
                                'item_receiving_report_id' => $item_receiving_report_transport->id,
                                'tax_id' => $key,
                            ],
                            [
                                'item_receiving_report_id' => $item_receiving_report_transport->id,
                                'tax_id' => $key,
                                'tax_value' => $group_lpb_coa2->first()->tax_value,
                                'sub_total' => $group_lpb_coa2->sum('sub_total'),
                                'tax_amount' => $group_lpb_coa2->sum('tax_amount'),
                            ]
                        );

                        $lpb_tax_summaries = LpbTaxSummary::where('item_receiving_report_id', $item_receiving_report_transport->id)
                            ->where('tax_id', $key)
                            ->get();

                        if ($lpb_tax_summaries->count() > 1) {
                            $lpb_tax_summaries->skip(1)
                                ->each(function ($lpb_tax_summary) {
                                    $lpb_tax_summary->delete();
                                });
                        }
                    }
                }
            }

            DB::commit();
            Log::info('item receiving report tax summary job success');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("item receiving report tax summary job error " . $th->getMessage());
        }
    }
}
