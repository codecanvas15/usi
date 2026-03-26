<?php

namespace App\Jobs;

use App\Http\Helpers\JournalHelpers;
use App\Models\InvoiceGeneral;
use App\Models\InvoiceTrading;
use App\Models\ItemCategoryCoa;
use App\Models\ItemReceivingReportCoa;
use App\Models\Journal;
use App\Models\JournalDetail;
use App\Models\RefreshStockLog;
use App\Models\StockMutation;
use App\Models\StockOpnameDetail;
use App\Models\StockTransfer;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DailyRefreshStockJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $period;
    protected $user_id;
    protected $force;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($period = null, $user_id = null, $force = false)
    {
        $this->period = $period;
        $this->user_id = $user_id;
        $this->force = $force;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $selected_period = $this->period;
        $period = $selected_period ? $selected_period : Carbon::now();
        $force = $this->force ?? false;

        $will_update_delivery_orders = new Collection();

        DB::beginTransaction();
        try {

            // REGENERATE STOCK MUTATION
            $stockMutations = StockMutation::when($period, function ($query) use ($period) {
                return $query->whereMonth('date', $period)
                    ->whereYear('date', $period);
            })
                // ->where('item_id', 2204)
                ->whereNull('deleted_at')
                ->orderBy('ordering', 'asc')
                ->get();

            $stockMutationsLpb = StockMutation::where('in', '>', 0)
                // ->where('item_id', 2204)
                ->whereDate('date', '>=', $period->copy()->startOfMonth()->toDateString())
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
                    if ($item_receving_report) {
                        $item_receving_report =  $item_receving_report->observerAfterCreate();
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

                        $stockMutation_before = StockMutation::where('item_id', $stockMutation->item_id)
                            ->where('ordering', '<', $stockMutation->ordering)
                            ->orderBy('ordering', 'desc')
                            ->first();

                        $subtotal = $price_unit * $stockMutation->in;
                        $total = ($stockMutation_before->total ?? 0) + $subtotal;
                        $stock_before = $stockMutation->stockBefore();
                        $final_stock = $stock_before + $stockMutation->in;

                        if ($final_stock != 0) {
                            $value = $total / $final_stock;
                        } else {
                            $value = 0;
                        }

                        $stockMutation->price_unit = $price_unit;
                        $stockMutation->subtotal = $subtotal;
                        $stockMutation->total = $total;
                        $stockMutation->value = $value;
                        $stockMutation->save();

                        // if price_unit, subtotal, total changed, regenerate journal
                        if ($current_price_unit != $price_unit || $subtotal != $current_subtotal || $total != $current_total || $force) {
                            $journal = new JournalHelpers($item_receving_report->tipe, $item_receving_report->id);
                            $journal->generate();
                        }
                    } else {
                        Log::error('reference not found ' . $stockMutation);
                    }
                }

                if ($stockMutation->type == 'supplier invoice') {
                    $item_receving_report = $stockMutation->document_model::find($stockMutation->document_id);
                    if ($item_receving_report) {
                        $item_receving_report_detail = $item_receving_report->item_receiving_report_details
                            ->where('item_id', $stockMutation->item_id)
                            ->where('price_id', $stockMutation->price_id)
                            ->first();

                        if (!$item_receving_report_detail) {
                            Log::error('supplier invoice reference not found ' . $stockMutation);
                        }
                        $price = $item_receving_report_detail->reference->price * $item_receving_report->exchange_rate;

                        $price_unit = $price;

                        $stockMutation_before = StockMutation::where('item_id', $stockMutation->item_id)
                            ->where('ordering', '<', $stockMutation->ordering)
                            ->orderBy('ordering', 'desc')
                            ->first();

                        $subtotal = $price_unit * $stockMutation->in;
                        $total = ($stockMutation_before->total ?? 0) + $subtotal;
                        $stock_before = $stockMutation->stockBefore();
                        $final_stock = $stock_before + $stockMutation->in;
                        if ($final_stock != 0) {
                            $value = $total / $final_stock;
                        } else {
                            $value = 0;
                        }

                        $stockMutation->price_unit = $price_unit;
                        $stockMutation->subtotal = $subtotal;
                        $stockMutation->total = $total;
                        $stockMutation->value = $value;
                        $stockMutation->save();
                    } else {
                        Log::error('reference not found ' . $stockMutation);
                    }
                }

                if ($stockMutation->type === 'delivery order trading') {

                    $delivery_order_trading = $stockMutation->document_model::find($stockMutation->document_id);

                    if (!$delivery_order_trading) {
                        Log::error('reference not found', ['stock_mutation_id' => $stockMutation->id]);
                        return;
                    }

                    /**
                     * =============================
                     * INIT REVALUATION LOG
                     * =============================
                     */
                    $revaluationLog = [];

                    /**
                     * =============================
                     * AMBIL STOCK SEBELUM DO
                     * =============================
                     */
                    $stockMutation_before = StockMutation::where('item_id', $stockMutation->item_id)
                        ->where('ordering', '<', $stockMutation->ordering)
                        ->orderBy('ordering', 'desc')
                        ->first();

                    $stock_before = $stockMutation->stockBefore();
                    $out_qty = $stockMutation->out;

                    $subtotal = 0;
                    $used_qty_total = 0;
                    $revaluated_qty_total = 0;

                    /**
                     * =============================
                     * 1️⃣ PAKAI STOCK LAMA (JIKA ADA)
                     * =============================
                     */
                    if ($stock_before > 0 && $stockMutation_before) {

                        $qty_from_old_stock = min($stock_before, $out_qty);
                        $old_price = $stockMutation_before->value ?? 0;
                        $old_subtotal = $qty_from_old_stock * $old_price;

                        $subtotal += $old_subtotal;
                        $used_qty_total += $qty_from_old_stock;
                        $revaluated_qty_total += $qty_from_old_stock;

                        $revaluationLog['old_stock'] = [
                            'qty' => $qty_from_old_stock,
                            'price' => $old_price,
                            'subtotal' => $old_subtotal,
                        ];
                    }

                    /**
                     * =============================
                     * 2️⃣ TAMBAL DARI LPB SETELAHNYA
                     * =============================
                     */
                    $remaining_qty = $out_qty - $used_qty_total;

                    $is_revaluated = false;
                    if ($remaining_qty > 0) {

                        $is_revaluated = true;
                        $revaluationLog['stock_mutation_id'] = $stockMutation->id;
                        $revaluationLog['item_id'] = $stockMutation->item_id;
                        $revaluationLog['do_qty'] = $stockMutation->out;
                        $revaluationLog['lpbs'] = [];
                        $revaluationLog['total_subtotal'] = 0;
                        $revaluationLog['final_hpp'] = 0;


                        $future_lpbs = $stockMutationsLpb
                            ->where('item_id', $stockMutation->item_id)
                            ->filter(function ($mutation) use ($stockMutation) {
                                return $mutation->ordering > $stockMutation->ordering
                                    && $mutation->in > 0
                                    && $mutation->available_qty > 0;
                            })
                            ->sortBy('ordering');

                        foreach ($future_lpbs as $lpb) {

                            if ($remaining_qty <= 0) break;

                            $qty_before = $lpb->available_qty;
                            $used_qty = min($qty_before, $remaining_qty);

                            $revaluated_qty = $used_qty;
                            if ($qty_before > $remaining_qty) {
                                $revaluated_qty = $qty_before;
                            } else {
                                $revaluated_qty = $used_qty;
                            }

                            $lpb_subtotal = $revaluated_qty * $lpb->price_unit;

                            $subtotal += $lpb_subtotal;
                            $remaining_qty -= $used_qty;
                            $revaluated_qty_total += $revaluated_qty;

                            // update available qty LPB
                            DB::table('stock_mutations')
                                ->where('id', $lpb->id)
                                ->update([
                                    'available_qty' => $qty_before - $used_qty
                                ]);

                            $stockMutationsLpb->where('id', $lpb->id)->first()->available_qty = $qty_before - $used_qty;

                            /**
                             * LOG LPB
                             */
                            $revaluationLog['lpbs'][] = [
                                'lpb_id' => $lpb->id,
                                'lpb_document_code' => $lpb->document_code,
                                'ordering' => $lpb->ordering,
                                'price_unit' => $lpb->price_unit,
                                'qty_before' => $qty_before,
                                'qty_used' => $used_qty,
                                'revaluated_qty' => $revaluated_qty,
                                'qty_after' => $qty_before - $used_qty,
                                'subtotal' => $lpb_subtotal,
                            ];
                        }

                        if ($remaining_qty > 0) {
                            Log::error('Revaluasi gagal: LPB tidak cukup menutup DO Trading', ['stock_mutation_id' => $stockMutation->id]);
                        }
                    }

                    /**
                     * =============================
                     * 3️⃣ HITUNG HPP FINAL DO
                     * =============================
                     */
                    $price_unit = $revaluated_qty_total > 0 ? $subtotal / $revaluated_qty_total : 0;

                    $revaluationLog['total_subtotal'] = $subtotal;
                    $revaluationLog['final_hpp'] = $price_unit;

                    /**
                     * =============================
                     * 4️⃣ HITUNG STOCK & VALUE BARU
                     * =============================
                     */
                    $final_stock = $stock_before - $out_qty;

                    $subtotal = $price_unit * $out_qty;
                    $previous_total = $stockMutation_before->total ?? 0;
                    $total = $previous_total - $subtotal;
                    $value = $final_stock != 0 ? $total / $final_stock : 0;

                    /**
                     * =============================
                     * 5️⃣ UPDATE STOCK MUTATION
                     * =============================
                     */
                    $stockMutation->update([
                        'price_unit' => $price_unit,
                        'subtotal'   => $subtotal,
                        'total'      => $total,
                        'value'      => $value,
                        'revaluation_log' => $is_revaluated ? json_encode($revaluationLog) : null,
                    ]);

                    /**
                     * =============================
                     * 6️⃣ UPDATE DO + JOURNAL
                     * =============================
                     */
                    if (
                        $current_price_unit != $price_unit ||
                        $current_subtotal  != $subtotal ||
                        $current_total     != $total ||
                        $force
                    ) {

                        DB::table('delivery_orders')
                            ->where('id', $delivery_order_trading->id)
                            ->update([
                                'hpp' => $price_unit
                            ]);

                        $journal = Journal::where('reference_id', $delivery_order_trading->id)
                            ->where('reference_model', 'App\Models\DeliveryOrder')
                            ->first();

                        if ($journal) {

                            // Inventory (Credit)
                            JournalDetail::where('journal_id', $journal->id)
                                ->where('coa_id', $item_inventory_coa->coa_id)
                                ->update([
                                    'credit' => $subtotal,
                                    'debit' => 0,
                                    'credit_exchanged' => $subtotal,
                                    'debit_exchanged' => 0,
                                ]);

                            // HPP / Goods In Transit (Debit)
                            $hpp_detail = JournalDetail::where('journal_id', $journal->id)
                                ->whereIn('coa_id', [
                                    $item_hpp_coa->coa_id,
                                    $item_goods_in_transit_coa->coa_id
                                ])
                                ->first();

                            if ($hpp_detail) {
                                $hpp_detail->update([
                                    'debit' => $subtotal,
                                    'credit' => 0,
                                    'debit_exchanged' => $subtotal,
                                    'credit_exchanged' => 0,
                                ]);
                            }

                            // Recalculate journal total
                            $journal->update([
                                'debit_total' => $journal->journal_details()->sum('debit'),
                                'credit_total' => $journal->journal_details()->sum('credit'),
                            ]);
                        }

                        $will_update_delivery_orders->push($delivery_order_trading->id);
                    }
                }

                if ($stockMutation->type === 'delivery order general') {

                    $delivery_order_general_detail = $stockMutation->document_model::find($stockMutation->document_id);

                    if (!$delivery_order_general_detail) {
                        Log::error('reference not found', ['stock_mutation_id' => $stockMutation->id]);
                        return;
                    }

                    /**
                     * =============================
                     * INIT REVALUATION LOG
                     * =============================
                     */
                    $revaluationLog = [];

                    $delivery_order_general = $delivery_order_general_detail->delivery_order_general;

                    /**
                     * =============================
                     * AMBIL STOCK SEBELUM DO
                     * =============================
                     */
                    $stockMutation_before = StockMutation::where('item_id', $stockMutation->item_id)
                        ->where('ordering', '<', $stockMutation->ordering)
                        ->orderBy('ordering', 'desc')
                        ->first();

                    $stock_before = $stockMutation->stockBefore();
                    $out_qty = $stockMutation->out;

                    $subtotal = 0;
                    $used_qty_total = 0;
                    $revaluated_qty_total = 0;

                    /**
                     * =============================
                     * 1️⃣ PAKAI STOCK LAMA (JIKA ADA)
                     * =============================
                     */
                    if ($stock_before > 0 && $stockMutation_before) {

                        $qty_from_old_stock = min($stock_before, $out_qty);
                        $old_price = $stockMutation_before->value ?? 0;
                        $old_subtotal = $qty_from_old_stock * $old_price;

                        $subtotal += $old_subtotal;
                        $used_qty_total += $qty_from_old_stock;
                        $revaluated_qty_total += $qty_from_old_stock;

                        $revaluationLog['old_stock'] = [
                            'qty' => $qty_from_old_stock,
                            'price' => $old_price,
                            'subtotal' => $old_subtotal,
                        ];
                    }

                    /**
                     * =============================
                     * 2️⃣ TAMBAL DARI LPB SETELAHNYA
                     * (HANYA JIKA STOK MINUS)
                     * =============================
                     */
                    $remaining_qty = $out_qty - $used_qty_total;

                    $is_revaluated = false;
                    if ($remaining_qty > 0) {

                        $is_revaluated = true;
                        $revaluationLog['stock_mutation_id'] = $stockMutation->id;
                        $revaluationLog['item_id'] = $stockMutation->item_id;
                        $revaluationLog['do_qty'] = $stockMutation->out;
                        $revaluationLog['lpbs'] = [];
                        $revaluationLog['total_subtotal'] = 0;
                        $revaluationLog['final_hpp'] = 0;

                        $future_lpbs = $stockMutationsLpb
                            ->where('item_id', $stockMutation->item_id)
                            ->filter(function ($mutation) use ($stockMutation) {
                                return $mutation->ordering > $stockMutation->ordering
                                    && $mutation->in > 0
                                    && $mutation->available_qty > 0;
                            })
                            ->sortBy('ordering');

                        foreach ($future_lpbs as $lpb) {

                            if ($remaining_qty <= 0) break;

                            $qty_before = $lpb->available_qty;
                            $used_qty = min($qty_before, $remaining_qty);

                            $revaluated_qty = $used_qty;
                            if ($qty_before > $remaining_qty) {
                                $revaluated_qty = $qty_before;
                            } else {
                                $revaluated_qty = $used_qty;
                            }

                            $lpb_subtotal = $revaluated_qty * $lpb->price_unit;

                            $subtotal += $lpb_subtotal;
                            $remaining_qty -= $used_qty;
                            $revaluated_qty_total += $revaluated_qty;

                            // kurangi available qty LPB
                            DB::table('stock_mutations')
                                ->where('id', $lpb->id)
                                ->update([
                                    'available_qty' => $qty_before - $used_qty
                                ]);

                            $stockMutationsLpb->where('id', $lpb->id)->first()->available_qty = $qty_before - $used_qty;

                            /**
                             * LOG LPB
                             */
                            $revaluationLog['lpbs'][] = [
                                'lpb_id' => $lpb->id,
                                'lpb_document_code' => $lpb->document_code,
                                'ordering' => $lpb->ordering,
                                'price_unit' => $lpb->price_unit,
                                'qty_before' => $qty_before,
                                'qty_used' => $used_qty,
                                'revaluated_qty' => $revaluated_qty,
                                'qty_after' => $qty_before - $used_qty,
                                'subtotal' => $lpb_subtotal,
                            ];
                        }

                        if ($remaining_qty > 0) {
                            Log::error('Revaluasi gagal: LPB tidak cukup menutup DO General', ['stock_mutation_id' => $stockMutation->id]);
                        }
                    }

                    /**
                     * =============================
                     * 3️⃣ HITUNG HPP FINAL DO
                     * =============================
                     */
                    $price_unit = $revaluated_qty_total > 0 ? $subtotal / $revaluated_qty_total : 0;

                    $revaluationLog['total_subtotal'] = $subtotal;
                    $revaluationLog['final_hpp'] = $price_unit;

                    /**
                     * =============================
                     * 4️⃣ HITUNG STOCK & VALUE BARU
                     * =============================
                     */

                    $final_stock = $stock_before - $out_qty;

                    $subtotal = $price_unit * $out_qty;
                    $previous_total = $stockMutation_before->total ?? 0;
                    $total = $previous_total - $subtotal;
                    $value = $final_stock != 0 ? $total / $final_stock : 0;

                    /**
                     * =============================
                     * 5️⃣ UPDATE STOCK MUTATION
                     * =============================
                     */
                    $stockMutation->update([
                        'price_unit' => $price_unit,
                        'subtotal'   => $subtotal,
                        'total'      => $total,
                        'value'      => $value,
                        'revaluation_log' => $is_revaluated ? json_encode($revaluationLog) : null,
                    ]);

                    /**
                     * =============================
                     * 6️⃣ UPDATE DO + JOURNAL
                     * =============================
                     */
                    if (
                        $current_price_unit != $price_unit ||
                        $current_subtotal  != $subtotal ||
                        $current_total     != $total ||
                        $force
                    ) {

                        /**
                         * Update HPP DO Detail
                         */
                        $delivery_order_general_detail->update([
                            'hpp' => $price_unit
                        ]);

                        /**
                         * Generate Journal DO
                         */
                        $journal = new \App\Http\Helpers\JournalHelpers(
                            'delivery-order-general',
                            $delivery_order_general->id
                        );
                        $journal->generate();

                        /**
                         * Generate Losses Journal
                         */
                        $losses_journal = new \App\Http\Helpers\JournalHelpers(
                            'delivery-order-general-losses',
                            $delivery_order_general->id
                        );
                        $losses_journal->generate();

                        /**
                         * =============================
                         * 7️⃣ REGENERATE INVOICE JOURNAL
                         * =============================
                         */
                        $invoice_generals = InvoiceGeneral::whereHas(
                            'invoice_general_details',
                            function ($query) use ($delivery_order_general) {
                                $query->whereHas(
                                    'delivery_order_general_detail',
                                    function ($query) use ($delivery_order_general) {
                                        $query->where(
                                            'delivery_order_general_id',
                                            $delivery_order_general->id
                                        );
                                    }
                                );
                            }
                        )->get();

                        $invoice_journals = Journal::whereIn(
                            'reference_id',
                            $invoice_generals->pluck('id')->toArray()
                        )
                            ->where('reference_model', 'App\Models\InvoiceGeneral')
                            ->get();

                        foreach ($invoice_generals as $invoice_general) {

                            if ($invoice_general->status !== 'approve') {
                                continue;
                            }

                            $invoice_journal = $invoice_journals
                                ->where('reference_id', $invoice_general->id)
                                ->first();

                            if ($invoice_journal) {
                                $new_invoice_journal = new \App\Http\Helpers\JournalHelpers(
                                    'invoice-general',
                                    $invoice_general->id
                                );
                                $new_invoice_journal->generate();
                            }
                        }
                    }
                }

                if ($stockMutation->type == 'purchase return') {
                    $purchase_return_detail = $stockMutation->document_model::find($stockMutation->document_id);
                    if ($purchase_return_detail) {
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
                        $price_unit  = $stockMutation_before->value ?? 0;
                        $subtotal = $price_unit * $stockMutation->out;
                        $total = ($stockMutation_before->total ?? 0) - $subtotal;
                        $stock_before = $stockMutation->stockBefore();
                        $final_stock = $stock_before - $stockMutation->out;
                        if ($final_stock != 0) {
                            $value = $total / $final_stock;
                        } else {
                            $value = 0;
                        }

                        $stockMutation->price_unit = $price_unit;
                        $stockMutation->subtotal = $subtotal;
                        $stockMutation->total = $total;
                        $stockMutation->value = $value;
                        $stockMutation->save();

                        if ($current_price_unit != $price_unit || $subtotal != $current_subtotal || $total != $current_total || $force) {
                            $purchase_return_detail->price = $original_price_unit;
                            $purchase_return_detail->subtotal = $original_price_unit * $purchase_return_detail->qty;
                            $purchase_return_detail->save();

                            $journal = new JournalHelpers('purchase-return', $purchase_return->id);
                            $journal->generate();
                        }
                    } else {
                        Log::error('reference not found ' . $stockMutation);
                    }
                }

                if ($stockMutation->type == 'invoice return') {
                    $invoice_return_detail = $stockMutation->document_model::find($stockMutation->document_id);
                    if ($invoice_return_detail) {
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
                        if ($final_stock != 0) {
                            $value = $total / $final_stock;
                        } else {
                            $value = 0;
                        }

                        $stockMutation->price_unit = $price_unit;
                        $stockMutation->subtotal = $subtotal;
                        $stockMutation->total = $total;
                        $stockMutation->value = $value;
                        $stockMutation->save();

                        if ($current_price_unit != $price_unit || $subtotal != $current_subtotal || $total != $current_total || $force) {
                            $invoice_return_detail->hpp = $price_unit;
                            $invoice_return_detail->hpp_total = $price_unit * $invoice_return_detail->qty;
                            $invoice_return_detail->price = $price;
                            $invoice_return_detail->subtotal = $price * $invoice_return_detail->qty;
                            $invoice_return_detail->save();

                            $journal = new JournalHelpers('invoice-return', $invoice_return->id);
                            $journal->generate();
                        }
                    } else {
                        Log::error('reference not found ' . $stockMutation);
                    }
                }

                if ($stockMutation->type == 'stock opname') {
                    $stock_opname = $stockMutation->document_model::find($stockMutation->document_id);
                    if ($stock_opname) {
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
                        if ($final_stock != 0) {
                            $value = $total / $final_stock;
                        } else {
                            $value = 0;
                        }

                        $stockMutation->price_unit = $price_unit;
                        $stockMutation->subtotal = $subtotal;
                        $stockMutation->total = $total;
                        $stockMutation->value = $value;
                        $stockMutation->save();

                        if ($current_price_unit != $price_unit || $subtotal != $current_subtotal || $total != $current_total || $force) {
                            $stock_opname_detail->value = $price_unit * $stock_opname_detail->difference;
                            $stock_opname_detail->save();

                            $journal = new \App\Http\Helpers\JournalHelpers("stock-opname", $stock_opname->id);
                            $journal->generate();
                        }
                    } else {
                        Log::error('reference not found ' . $stockMutation);
                    }
                }

                if ($stockMutation->type == 'delivery order trading losses') {
                    $delivery_order_trading_losses = $stockMutation->document_model::find($stockMutation->document_id);
                    if ($delivery_order_trading_losses) {
                        $stockMutation_before = StockMutation::where('item_id', $stockMutation->item_id)
                            ->where('ordering', '<', $stockMutation->ordering)
                            ->orderBy('ordering', 'desc')
                            ->first();

                        $price_unit  = $delivery_order_trading_losses->deliveryOrder->hpp ?? 0;
                        $subtotal = $price_unit * $stockMutation->out;
                        $total = ($stockMutation_before->total ?? 0) - $subtotal;

                        $stock_before = $stockMutation->stockBefore();
                        $final_stock = $stock_before - $stockMutation->out;
                        if ($final_stock != 0) {
                            $value = $total / $final_stock;
                        } else {
                            $value = 0;
                        }

                        $stockMutation->price_unit = $price_unit;
                        $stockMutation->subtotal = $subtotal;
                        $stockMutation->total = $total;
                        $stockMutation->value = $value;
                        $stockMutation->save();

                        if ($current_price_unit != $price_unit || $subtotal != $current_subtotal || $total != $current_total || $force) {
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
                    } else {
                        Log::error('reference not found ' . $stockMutation);
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
                    if ($final_stock != 0) {
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
                    if ($stock_usage_detail) {
                        $stock_usage = $stock_usage_detail->stock_usage;

                        $stockMutation_before = StockMutation::where('item_id', $stockMutation->item_id)
                            ->where('ordering', '<', $stockMutation->ordering)
                            ->orderBy('ordering', 'desc')
                            ->first();

                        $price_unit  = $stockMutation_before->value ?? 0;
                        $subtotal = $price_unit * $stockMutation->out;
                        $total = ($stockMutation_before->total ?? 0) - $subtotal;
                        $stock_before = $stockMutation->stockBefore();
                        $final_stock = $stock_before - $stockMutation->out;
                        if ($final_stock != 0) {
                            $value = $total / $final_stock;
                        } else {
                            $value = 0;
                        }

                        $stockMutation->price_unit = $price_unit;
                        $stockMutation->subtotal = $subtotal;
                        $stockMutation->total = $total;
                        $stockMutation->value = $value;
                        $stockMutation->save();

                        if ($current_price_unit != $price_unit || $subtotal != $current_subtotal || $total != $current_total || $force) {
                            $stock_usage_detail->price_unit = $price_unit;
                            $stock_usage_detail->save();

                            $journal = new JournalHelpers("stock-usage", $stock_usage->id);
                            $journal->generate();
                        }
                    } else {
                        Log::error('reference not found ' . $stockMutation);
                    }
                }

                if ($stockMutation->type == 'stock transfer') {
                    $stock_transfer = $stockMutation->document_model::find($stockMutation->document_id);
                    if ($stock_transfer) {
                        $stock_transfer_mutations = StockMutation::where('document_id', $stock_transfer->id)
                            ->where('item_id', $stockMutation->item_id)
                            ->where('document_model', StockTransfer::class)
                            ->orderBy('ordering', 'asc')
                            ->get();

                        $first_mutation = $stock_transfer_mutations->first();

                        if ($stockMutation->id == $first_mutation->id) {
                            // ISSUE 1: Missing logic to update journal if values change
                            $stockMutation_before = StockMutation::where('item_id', $stockMutation->item_id)
                                ->where('ordering', '<', $stockMutation->ordering)
                                ->orderBy('ordering', 'desc')
                                ->first();

                            $price_unit = $stockMutation_before->value ?? 0;
                            $stock_before = $stockMutation->stockBefore();

                            if ($first_mutation->in != 0 && $first_mutation->in != null) {
                                $subtotal = $price_unit * $stockMutation->in;
                                $total = ($stockMutation_before->total ?? 0) + $subtotal;
                                $final_stock = $stock_before + $stockMutation->in;
                            } else {
                                $subtotal = $price_unit * $stockMutation->out;
                                $total = ($stockMutation_before->total ?? 0) - $subtotal;
                                $final_stock = $stock_before - $stockMutation->out;
                            }

                            $value = $final_stock != 0 ? $total / $final_stock : 0;

                            $stockMutation->price_unit = $price_unit;
                            $stockMutation->subtotal = $subtotal;
                            $stockMutation->total = $total;
                            $stockMutation->value = $value;
                            $stockMutation->save();

                            // ISSUE 2: Second mutation uses wrong price_unit
                            // Should use $stockMutation->value (the first mutation's value), not $second_stockMutation->value
                            $second_stockMutation = $stock_transfer_mutations->where('id', '!=', $stockMutation->id)->first();
                            $second_stockMutation_before = StockMutation::where('item_id', $second_stockMutation->item_id)
                                ->where('ordering', '<', $second_stockMutation->ordering)
                                ->orderBy('ordering', 'desc')
                                ->first();

                            // ISSUE 3: Should use $stockMutation->value (from first mutation), not $second_stockMutation->value
                            $price_unit = $stockMutation->price_unit; // FIX: Use first mutation's value
                            $stock_before = $second_stockMutation->stockBefore();

                            if ($first_mutation->in != 0 && $first_mutation->in != null) {
                                $subtotal = $price_unit * $second_stockMutation->out;
                                $total = ($second_stockMutation_before->total ?? 0) - $subtotal;
                                $final_stock = $stock_before - $second_stockMutation->out;
                            } else {
                                $subtotal = $price_unit * $second_stockMutation->in;
                                $total = ($second_stockMutation_before->total ?? 0) + $subtotal;
                                $final_stock = $stock_before + $second_stockMutation->in;
                            }

                            $value = $final_stock != 0 ? $total / $final_stock : 0;

                            $second_stockMutation->price_unit = $price_unit;
                            $second_stockMutation->subtotal = $subtotal;
                            $second_stockMutation->total = $total;
                            $second_stockMutation->value = $value;
                            $second_stockMutation->save();
                        }
                    } else {
                        Log::error('reference not found ' . $stockMutation);
                    }
                }
            }

            // UPDATE DELIVERY ORDER TRADING HPP
            $will_update_delivery_orders = $will_update_delivery_orders->unique();
            $invoice_tradings = InvoiceTrading::whereHas('invoice_trading_details', function ($query) use ($will_update_delivery_orders) {
                $query->whereHas('delivery_order', function ($query) use ($will_update_delivery_orders) {
                    $query->whereIn('delivery_orders.id', $will_update_delivery_orders->toArray());
                });
            })
                ->get();

            foreach ($invoice_tradings as $invoice_trading) {
                $invoice_journal = Journal::where('reference_id', $invoice_trading->id)
                    ->where('reference_model', InvoiceTrading::class)
                    ->first();

                if ($invoice_journal && ($invoice_journal->status ?? '') == 'approve') {
                    $new_invoice_journal = new JournalHelpers('invoice-trading', $invoice_trading->id);
                    $new_invoice_journal->generate();
                }
            }

            DB::commit();

            RefreshStockLog::create([
                'user_id' => $this->user_id,
                'status' => 'success',
                'period' => $period ?? Carbon::now(),
                'message' => 'daily stock refresh'
            ]);

            Log::info('Refresh Stock Mutation Success for period' . ($period ? ': ' . $period : ''));
        } catch (\Throwable $th) {
            DB::rollBack();

            RefreshStockLog::create([
                'user_id' => $this->user_id,
                'status' => 'failed',
                'period' => $period ?? Carbon::now(),
                'message' => 'Refresh Stock Mutation Failed: ' . $th->getMessage() . ' at ' . $th->getFile() . ':' . $th->getLine()
            ]);

            Log::info('Refresh Stock Mutation Failed ' . $th);
        }
    }
}
