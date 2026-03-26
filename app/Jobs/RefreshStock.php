<?php

namespace App\Jobs;

use App\Http\Helpers\JournalHelpers;
use App\Http\Helpers\NotificationHelper;
use App\Models\InvoiceGeneral;
use App\Models\InvoiceTrading;
use App\Models\ItemCategoryCoa;
use App\Models\ItemReceivingReportCoa;
use App\Models\Journal;
use App\Models\JournalDetail;
use App\Models\RefreshStockLog;
use App\Models\StockMutation;
use App\Models\StockOpnameDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefreshStock implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $period;
    protected $user_id;

    public function __construct($period = null, $user_id = null)
    {
        $this->period = $period;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $period = $this->period;
        $user_id = $this->user_id;
        $title = 'Refresh Stock Mutation';

        if ($period) {
            $period = Carbon::parse('01-' . $period);
        } else {
            $period = Carbon::now();
        }

        if (!checkAvailableDate($period->startOfMonth())) {
            Log::info('Closing period is exist');
            if ($user_id) {
                $user = User::find($user_id);
                $notification = new NotificationHelper();
                $notification->send_notification(
                    title: $title,
                    body: 'Refresh Stock Mutation Failed Because Closing Period is Exist',
                    reference_model: StockMutation::class,
                    reference_id: null,
                    branch_id: $user->branch_id,
                    user_id: $user_id,
                    roles: [],
                    permissions: [],
                    link: route('admin.stock-mutation.index'),
                );
            }

            return;
        }

        DB::beginTransaction();
        try {
            // UPDATE STOCK ORDER

            DB::table('stock_mutations')
                ->whereMonth('date', $period)
                ->whereYear('date', $period)
                ->update(['ordering' => null]);

            $stockMutations = DB::table('stock_mutations')
                ->whereMonth('date', $period)
                ->whereYear('date', $period)
                ->orderBy('date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($stockMutations as $stockMutation) {
                DB::table('stock_mutations')
                    ->where('id', $stockMutation->id)
                    ->update(['ordering' => generate_stock_mutation_order($stockMutation->date)]);
            }

            // REGENERATE STOCK MUTATION
            $stockMutations = StockMutation::whereMonth('date', $period)
                ->whereYear('date', $period)
                ->whereNull('deleted_at')
                ->orderBy('ordering', 'asc')
                ->get();
            foreach ($stockMutations as $stockMutation) {
                $item_inventory_coa = ItemCategoryCoa::where('item_category_id', $stockMutation->item->item_category_id)
                    ->whereRaw('LOWER(type) = ?', ['inventory'])
                    ->first();

                $item_hpp_coa = ItemCategoryCoa::where('item_category_id', $stockMutation->item->item_category_id)
                    ->whereRaw('LOWER(type) = ?', ['hpp'])
                    ->first();

                $item_goods_in_transit_coa = ItemCategoryCoa::where('item_category_id', $stockMutation->item->item_category_id)
                    ->whereRaw('LOWER(type) = ?', ['goods_in_transit'])
                    ->first();

                $current_price_unit = $stockMutation->price_unit;
                $current_subtotal = $stockMutation->subtotal;
                $current_total = $stockMutation->total;

                if ($stockMutation->type == 'supplier invoice trading') {
                    $item_receving_report = $stockMutation->document_model::find($stockMutation->document_id);
                    ItemReceivingReportCoa::where('item_receiving_report_id', $item_receving_report->id)->delete();
                    // * create coa
                    $lpb_coa = new \App\Http\Helpers\ItemReceivingReportCoaHelpers($item_receving_report->tipe, $item_receving_report->reference_id, $item_receving_report->id);
                    $lpb_coa->create_item_receiving_report_coa();

                    $purchase_order = $item_receving_report->reference;
                    $price_before_discount = $purchase_order->po_trading_detail->harga;
                    $subtotal_before_discount = $price_before_discount * $item_receving_report->item_receiving_report_po_trading->liter_15;

                    $tax_with_inventory_coas = ItemReceivingReportCoa::where('item_receiving_report_id', $item_receving_report->id)->where('coa_id', $item_inventory_coa->coa_id)->get();
                    $inventory_tax_total = 0;
                    foreach ($tax_with_inventory_coas as $tax_with_inventory_coa) {
                        $inventory_tax_total += $subtotal_before_discount * $tax_with_inventory_coa->reference->value;
                    }

                    $price_unit = ($item_receving_report->item_receiving_report_po_trading->sub_total + $inventory_tax_total) / $item_receving_report->item_receiving_report_po_trading->liter_obs * $item_receving_report->exchange_rate;
                    $price_unit = $price_unit;

                    $stockMutation_before = StockMutation::where('item_id', $stockMutation->item_id)
                        ->where('ordering', '<', $stockMutation->ordering)
                        ->orderBy('ordering', 'desc')
                        ->first();

                    $subtotal = $price_unit * $stockMutation->in;
                    $total = ($stockMutation_before->total ?? 0) + $subtotal;
                    $stock_before = $stockMutation->stockBefore();
                    $final_stock = $stock_before + $stockMutation->in;

                    if ($final_stock > 0) {
                        $value = $total / $final_stock;
                    } else {
                        $value = 0;
                    }

                    $stockMutation->price_unit = $price_unit;
                    $stockMutation->subtotal = $subtotal;
                    $stockMutation->total = $total;
                    $stockMutation->value = $value;
                    $stockMutation->save();
                }

                if ($stockMutation->type == 'supplier invoice') {
                    $item_receving_report = $stockMutation->document_model::find($stockMutation->document_id);
                    $item_receving_report_detail = $item_receving_report->item_receiving_report_details
                        ->where('item_id', $stockMutation->item_id)
                        ->where('price_id', $stockMutation->price_id)
                        ->first();
                    $price = $item_receving_report_detail->reference->price * $item_receving_report->exchange_rate;

                    $price_unit = $price;
                    $price_unit = $price_unit;

                    $stockMutation_before = StockMutation::where('item_id', $stockMutation->item_id)
                        ->where('ordering', '<', $stockMutation->ordering)
                        ->orderBy('ordering', 'desc')
                        ->first();

                    $subtotal = $price_unit * $stockMutation->in;
                    $total = ($stockMutation_before->total ?? 0) + $subtotal;
                    $stock_before = $stockMutation->stockBefore();
                    $final_stock = $stock_before + $stockMutation->in;
                    if ($final_stock > 0) {
                        $value = $total / $final_stock;
                    } else {
                        $value = 0;
                    }

                    $stockMutation->price_unit = $price_unit;
                    $stockMutation->subtotal = $subtotal;
                    $stockMutation->total = $total;
                    $stockMutation->value = $value;
                    $stockMutation->save();
                }

                if ($stockMutation->type == 'delivery order trading') {
                    $delivery_order_trading = $stockMutation->document_model::find($stockMutation->document_id);
                    $stockMutation_before = StockMutation::where('item_id', $stockMutation->item_id)
                        ->where('ordering', '<', $stockMutation->ordering)
                        ->orderBy('ordering', 'desc')
                        ->first();

                    $price_unit  = $stockMutation_before->value ?? 0;
                    $subtotal = $price_unit * $stockMutation->out;
                    $total = ($stockMutation_before->total ?? 0) - $subtotal;

                    $stock_before = $stockMutation->stockBefore();
                    $final_stock = $stock_before - $stockMutation->out;
                    if ($final_stock > 0) {
                        $value = $total / $final_stock;
                    } else {
                        $value = 0;
                    }

                    $stockMutation->price_unit = $price_unit;
                    $stockMutation->subtotal = $subtotal;
                    $stockMutation->total = $total;
                    $stockMutation->value = $value;
                    $stockMutation->save();


                    if ($current_price_unit != $price_unit || $subtotal != $current_subtotal || $total != $current_total) {
                        DB::table('delivery_orders')
                            ->where('id', $delivery_order_trading->id)
                            ->update([
                                'hpp' => $price_unit
                            ]);

                        //find journal
                        $journal = Journal::where('reference_id', $delivery_order_trading->id)
                            ->where('reference_model', 'App\Models\DeliveryOrder')
                            ->first();

                        JournalDetail::where('journal_id', $journal->id)
                            ->where('coa_id', $item_inventory_coa->coa_id)
                            ->update([
                                'credit' => $stockMutation->subtotal,
                                'debit' => 0,
                                'credit_exchanged' => $stockMutation->subtotal,
                                'debit_exchanged' => 0,
                            ]);

                        $hpp_or_goods_in_transit =  JournalDetail::where('journal_id', $journal->id)
                            ->where('coa_id', $item_hpp_coa->coa_id)
                            ->first();

                        if (!$hpp_or_goods_in_transit) {
                            $hpp_or_goods_in_transit = JournalDetail::where('journal_id', $journal->id)
                                ->where('coa_id', $item_goods_in_transit_coa->coa_id)
                                ->first();
                        }

                        if ($hpp_or_goods_in_transit) {
                            $hpp_or_goods_in_transit
                                ->update([
                                    'credit' => 0,
                                    'credit_exchanged' => 0,
                                    'debit' => $stockMutation->subtotal,
                                    'debit_exchanged' => $stockMutation->subtotal,
                                ]);
                        }

                        $journal->update([
                            'debit_total' => $journal->journal_details()->sum('debit'),
                            'credit_total' => $journal->journal_details()->sum('credit'),
                        ]);

                        $invoice_trading = InvoiceTrading::whereHas('invoice_trading_details', function ($query) use ($delivery_order_trading) {
                            $query->where('delivery_order_id', $delivery_order_trading->id);
                        })
                            ->first();

                        if ($invoice_trading) {
                            $invoice_journal = Journal::where('reference_id', $invoice_trading->id)
                                ->where('reference_model', InvoiceTrading::class)
                                ->first();

                            if ($invoice_journal && ($invoice_journal->status ?? '') == 'approve') {
                                $invoice_journal->delete();

                                $new_invoice_journal = new JournalHelpers('invoice-trading', $invoice_trading->id);
                                $new_invoice_journal->generate();
                            }
                        }
                    }
                }

                if ($stockMutation->type == 'delivery order general') {
                    $delivery_order_general_detail = $stockMutation->document_model::find($stockMutation->document_id);
                    $delivery_order_general = $delivery_order_general_detail->delivery_order_general;

                    $stockMutation_before = StockMutation::where('item_id', $stockMutation->item_id)
                        ->where('ordering', '<', $stockMutation->ordering)
                        ->orderBy('ordering', 'desc')
                        ->first();

                    $price_unit  = $stockMutation_before->value ?? 0;
                    $subtotal = $price_unit * $stockMutation->out;
                    $total = ($stockMutation_before->total ?? 0) - $subtotal;

                    $stock_before = $stockMutation->stockBefore();
                    $final_stock = $stock_before - $stockMutation->out;
                    if ($final_stock > 0) {
                        $value = $total / $final_stock;
                    } else {
                        $value = 0;
                    }

                    $stockMutation->price_unit = $price_unit;
                    $stockMutation->subtotal = $subtotal;
                    $stockMutation->total = $total;
                    $stockMutation->value = $value;
                    $stockMutation->save();

                    if ($current_price_unit != $price_unit || $subtotal != $current_subtotal || $total != $current_total) {
                        $delivery_order_general_detail->hpp = $price_unit;
                        $delivery_order_general_detail->save();

                        // delete journal
                        $journal = Journal::where('reference_id', $delivery_order_general->id)
                            ->where('reference_model', 'App\Models\DeliveryOrderGeneral')
                            ->where('journal_type', 'Delivery Order General')
                            ->get();

                        JournalDetail::whereIn('journal_id', $journal->pluck('id')->toArray())
                            ->delete();

                        Journal::whereIn('id', $journal->pluck('id')->toArray())
                            ->delete();

                        $journal = new \App\Http\Helpers\JournalHelpers('delivery-order-general', $delivery_order_general->id);
                        $journal->generate();

                        // LOSSES JOURNAL
                        $losses_journal = new \App\Http\Helpers\JournalHelpers('delivery-order-general-losses', $delivery_order_general->id);
                        $losses_journal->generate();

                        $invoice_generals = InvoiceGeneral::whereHas('invoice_general_details', function ($query) use ($delivery_order_general) {
                            $query->whereHas('delivery_order_general_detail', function ($query) use ($delivery_order_general) {
                                $query->where('delivery_order_general_id', $delivery_order_general->id);
                            });
                        })
                            ->get();

                        $invoice_journals = Journal::whereIn('reference_id', $invoice_generals->pluck('id')->toArray())
                            ->where('reference_model', 'App\Models\InvoiceGeneral')
                            ->get();

                        foreach ($invoice_generals as $key => $invoice_general) {
                            if ($invoice_general->status == 'approve') {
                                $invoice_journal = $invoice_journals->where('reference_id', $invoice_general->id)->first();
                                if ($invoice_journal) {
                                    $invoice_journal->delete();

                                    $new_invoice_journal = new JournalHelpers('invoice-general', $invoice_general->id);
                                    $new_invoice_journal->generate();
                                }
                            }
                        }
                    }
                }

                if ($stockMutation->type == 'purchase return') {
                    $purchase_return_detail = $stockMutation->document_model::find($stockMutation->document_id);
                    $purchase_return = $purchase_return_detail->purchase_return;
                    $item_receving_report = $purchase_return->item_receiving_report;
                    $purchase_return->currency_id = $item_receving_report->currency_id;
                    $purchase_return->exchange_rate = $item_receving_report->exchange_rate;
                    $purchase_return->save();


                    $stockMutation_before = StockMutation::where('item_id', $stockMutation->item_id)
                        ->where('ordering', '<', $stockMutation->ordering)
                        ->orderBy('ordering', 'desc')
                        ->first();

                    $original_price_unit  = $purchase_return_detail->reference->reference->price;
                    $price_unit  = $original_price_unit * $item_receving_report->exchange_rate;
                    $subtotal = $price_unit * $stockMutation->out;
                    $total = ($stockMutation_before->total ?? 0) - $subtotal;
                    $stock_before = $stockMutation->stockBefore();
                    $final_stock = $stock_before - $stockMutation->out;
                    if ($final_stock > 0) {
                        $value = $total / $final_stock;
                    } else {
                        $value = 0;
                    }

                    $stockMutation->price_unit = $price_unit;
                    $stockMutation->subtotal = $subtotal;
                    $stockMutation->total = $total;
                    $stockMutation->value = $value;
                    $stockMutation->save();

                    if ($current_price_unit != $price_unit || $subtotal != $current_subtotal || $total != $current_total) {
                        $purchase_return_detail->price = $original_price_unit;
                        $purchase_return_detail->subtotal = $original_price_unit * $purchase_return_detail->qty;
                        $purchase_return_detail->save();
                        // delete journal
                        $journal = Journal::where('reference_id', $purchase_return->id)
                            ->where('reference_model', 'App\Models\PurchaseReturn')
                            ->get();

                        JournalDetail::whereIn('journal_id', $journal->pluck('id')->toArray())
                            ->delete();

                        Journal::whereIn('id', $journal->pluck('id')->toArray())
                            ->delete();

                        $journal = new JournalHelpers('purchase-return', $purchase_return->id);
                        $journal->generate();
                    }
                }

                if ($stockMutation->type == 'invoice return') {
                    $invoice_return_detail = $stockMutation->document_model::find($stockMutation->document_id);
                    $invoice_return = $invoice_return_detail->invoice_return;

                    $stockMutation_before = StockMutation::where('item_id', $stockMutation->item_id)
                        ->where('ordering', '<', $stockMutation->ordering)
                        ->orderBy('ordering', 'desc')
                        ->first();

                    $sale_order_detail = $invoice_return_detail->reference->sale_order_general_detail ?? $invoice_return_detail->reference->sale_order_detail;
                    $price = $sale_order_detail->price ?? $sale_order_detail->harga ?? 0;
                    $price_unit  = $invoice_return_detail->reference->hpp;
                    $subtotal = $price_unit * $stockMutation->in;
                    $total = ($stockMutation_before->total ?? 0) + $subtotal;
                    $stock_before = $stockMutation->stockBefore();
                    $final_stock = $stock_before + $stockMutation->in;
                    if ($final_stock > 0) {
                        $value = $total / $final_stock;
                    } else {
                        $value = 0;
                    }

                    $stockMutation->price_unit = $price_unit;
                    $stockMutation->subtotal = $subtotal;
                    $stockMutation->total = $total;
                    $stockMutation->value = $value;
                    $stockMutation->save();

                    if ($current_price_unit != $price_unit || $subtotal != $current_subtotal || $total != $current_total) {
                        $invoice_return_detail->hpp = $price_unit;
                        $invoice_return_detail->hpp_total = $price_unit * $invoice_return_detail->qty;
                        $invoice_return_detail->price = $price;
                        $invoice_return_detail->subtotal = $price * $invoice_return_detail->qty;
                        $invoice_return_detail->save();

                        // delete journal
                        $journal = Journal::where('reference_id', $invoice_return->id)
                            ->where('reference_model', 'App\Models\InvoiceReturn')
                            ->get();

                        JournalDetail::whereIn('journal_id', $journal->pluck('id')->toArray())
                            ->delete();

                        Journal::whereIn('id', $journal->pluck('id')->toArray())
                            ->delete();

                        $journal = new JournalHelpers('invoice-return', $invoice_return->id);
                        $journal->generate();
                    }
                }

                if ($stockMutation->type == 'stock opname') {
                    $stock_opname = $stockMutation->document_model::find($stockMutation->document_id);
                    $stock_opname_detail = StockOpnameDetail::where('stock_opname_id', $stock_opname->id)
                        ->where('item_id', $stockMutation->item_id)
                        ->first();

                    $stockMutation_before = StockMutation::where('item_id', $stockMutation->item_id)
                        ->where('ordering', '<', $stockMutation->ordering)
                        ->orderBy('ordering', 'desc')
                        ->first();

                    $price_unit  = $stockMutation_before->value ?? 0;
                    if ($stockMutation->in) {
                        $subtotal = $price_unit * $stockMutation->in;
                        $total = ($stockMutation_before->total ?? 0) + $subtotal;
                    } else {
                        $subtotal = $price_unit * $stockMutation->out;
                        $total = ($stockMutation_before->total ?? 0) - $subtotal;
                    }
                    $stock_before = $stockMutation->stockBefore();
                    if ($stockMutation->in) {
                        $final_stock = $stock_before + $stockMutation->in;
                    } else {
                        $final_stock = $stock_before - $stockMutation->out;
                    }
                    if ($final_stock > 0) {
                        $value = $total / $final_stock;
                    } else {
                        $value = 0;
                    }

                    $stockMutation->price_unit = $price_unit;
                    $stockMutation->subtotal = $subtotal;
                    $stockMutation->total = $total;
                    $stockMutation->value = $value;
                    $stockMutation->save();

                    if ($current_price_unit != $price_unit || $subtotal != $current_subtotal || $total != $current_total) {
                        $stock_opname_detail->value = $price_unit * $stock_opname_detail->difference;
                        $stock_opname_detail->save();

                        // delete journal
                        $journal = Journal::where('reference_id', $stock_opname->id)
                            ->where('reference_model', 'App\Models\StockOpname')
                            ->get();

                        JournalDetail::whereIn('journal_id', $journal->pluck('id')->toArray())
                            ->delete();

                        Journal::whereIn('id', $journal->pluck('id')->toArray())
                            ->delete();

                        $journal = new \App\Http\Helpers\JournalHelpers("stock-opname", $stock_opname->id);
                        $journal->generate();
                    }
                }

                // delivery order trading losses
                if ($stockMutation->type == 'delivery order trading losses') {
                    $delivery_order_trading_losses = $stockMutation->document_model::find($stockMutation->document_id);
                    $stockMutation_before = StockMutation::where('item_id', $stockMutation->item_id)
                        ->where('ordering', '<', $stockMutation->ordering)
                        ->orderBy('ordering', 'desc')
                        ->first();

                    $price_unit  = $delivery_order_trading_losses->deliveryOrder->hpp ?? 0;
                    $subtotal = $price_unit * $stockMutation->out;
                    $total = ($stockMutation_before->total ?? 0) - $subtotal;

                    $stock_before = $stockMutation->stockBefore();
                    $final_stock = $stock_before - $stockMutation->out;
                    if ($final_stock > 0) {
                        $value = $total / $final_stock;
                    } else {
                        $value = 0;
                    }

                    $stockMutation->price_unit = $price_unit;
                    $stockMutation->subtotal = $subtotal;
                    $stockMutation->total = $total;
                    $stockMutation->value = $value;
                    $stockMutation->save();

                    if ($current_price_unit != $price_unit || $subtotal != $current_subtotal || $total != $current_total) {
                        //find journal
                        $journal = Journal::where('reference_id', $delivery_order_trading_losses->id)
                            ->where('reference_model', \App\Models\ClosingDeliveryOrderShip::class)
                            ->first();

                        JournalDetail::where('journal_id', $journal->id)
                            ->where('coa_id', $item_inventory_coa->coa_id)
                            ->update([
                                'credit' => $stockMutation->subtotal,
                                'debit' => 0,
                            ]);

                        JournalDetail::where('journal_id', $journal->id)
                            ->where('coa_id', $delivery_order_trading_losses->losses_coa_id)
                            ->update([
                                'credit' => 0,
                                'debit' => $stockMutation->subtotal,
                            ]);

                        $journal->update([
                            'debit_total' => $journal->journal_details()->sum('debit'),
                            'credit_total' => $journal->journal_details()->sum('credit'),
                        ]);
                    }
                }

                if ($stockMutation->type == 'beginning balance') {
                    $stockMutation_before = StockMutation::where('item_id', $stockMutation->item_id)
                        ->where('ordering', '<', $stockMutation->ordering)
                        ->orderBy('ordering', 'desc')
                        ->first();

                    $price_unit  = $stockMutation->price_unit ?? 0;
                    $subtotal = $price_unit * $stockMutation->in;
                    $total = ($stockMutation_before->total ?? 0) + $subtotal;
                    $stock_before = $stockMutation->stockBefore();
                    $final_stock = $stock_before + $stockMutation->in;
                    if ($final_stock > 0) {
                        $value = $total / $final_stock;
                    } else {
                        $value = 0;
                    }

                    $stockMutation->price_unit = $price_unit;
                    $stockMutation->subtotal = $subtotal;
                    $stockMutation->total = $total;
                    $stockMutation->value = $value;
                    $stockMutation->save();
                }

                if ($stockMutation->type == 'stock usage') {
                    $stock_usage_detail = $stockMutation->document_model::find($stockMutation->document_id);
                    $stock_usage = $stock_usage_detail->stock_usage;

                    $stockMutation_before = StockMutation::where('item_id', $stockMutation->item_id)
                        ->where('ordering', '<', $stockMutation->ordering)
                        ->orderBy('ordering', 'desc')
                        ->first();

                    $price_unit  = $stockMutation_before->value;
                    $subtotal = $price_unit * $stockMutation->out;
                    $total = ($stockMutation_before->total ?? 0) - $subtotal;
                    $stock_before = $stockMutation->stockBefore();
                    $final_stock = $stock_before - $stockMutation->out;
                    if ($final_stock > 0) {
                        $value = $total / $final_stock;
                    } else {
                        $value = 0;
                    }

                    $stockMutation->price_unit = $price_unit;
                    $stockMutation->subtotal = $subtotal;
                    $stockMutation->total = $total;
                    $stockMutation->value = $value;
                    $stockMutation->save();

                    if ($current_price_unit != $price_unit || $subtotal != $current_subtotal || $total != $current_total) {
                        $stock_usage_detail->price_unit = $price_unit;
                        $stock_usage_detail->save();

                        //find journal
                        $journals = Journal::where('reference_id', $stock_usage->id)
                            ->where('reference_model', \App\Models\StockUsage::class)
                            ->get();

                        JournalDetail::whereIn('journal_id', $journals->pluck('id')->toArray())
                            ->delete();

                        Journal::whereIn('id', $journals->pluck('id')->toArray())
                            ->delete();

                        $journal = new JournalHelpers("stock-usage", $stock_usage->id);
                        $journal->generate();
                    }
                }
            }

            DB::commit();

            if ($user_id) {
                RefreshStockLog::create([
                    'user_id' => $user_id,
                    'status' => 'success',
                    'period' => $period,
                    'message' => 'manual stock refresh'
                ]);

                $user = User::find($user_id);
                $notification = new NotificationHelper();
                $notification->send_notification(
                    title: $title,
                    body: 'Refresh Stock Mutation Success',
                    reference_model: StockMutation::class,
                    reference_id: null,
                    branch_id: $user->branch_id,
                    user_id: $user_id,
                    roles: [],
                    permissions: [],
                    link: route('admin.stock-mutation.index'),
                );
            }

            Log::info('Refresh Stock Mutation Success');
        } catch (\Throwable $th) {
            DB::rollBack();

            if ($user_id) {
                $user = User::find($user_id);
                $notification = new NotificationHelper();
                $notification->send_notification(
                    title: $title,
                    body: 'Refresh Stock Mutation Failed',
                    reference_model: StockMutation::class,
                    reference_id: null,
                    branch_id: $user->branch_id,
                    user_id: $user_id,
                    roles: [],
                    permissions: [],
                    link: route('admin.stock-mutation.index'),
                );
            }
            Log::info('Refresh Stock Mutation Failed ' . $th);
        }
    }
}
