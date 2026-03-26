<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use App\Http\Helpers\JournalHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class ItemReceivingReport extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_receiving_report_id',
        'kode',
        'tipe',
        'reference_model',
        'reference_id',
        'ware_house_id',
        'vendor_id',
        'currency_id',
        'status',
        'sub_total',
        'tax_total',
        'total',
        'exchange_rate',
        'date_receive',
        'date_receive_time',
        'branch_id',
        'price_id',
        'project_id',
        'created_by',
        'do_code_external',
    ];

    /**
     * init activity logs
     *
     * @return LogsOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly(['created_at', 'updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "This data has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }

    /**
     * validation rules
     *
     * @param  string  $method
     * @param  int  $id
     * @return array
     */
    public static function rules($method = 'create', $id = null)
    {
        $validate = [];

        // * if has unique validation or diff rules when create or update
        if ($method == 'create') {
            $validate = array_merge($validate, []);
        } else {
            $validate = array_merge($validate, []);
        }

        return $validate;
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (!checkAvailableDate($model->date_receive)) {
                throw new \Exception('Tanggal tidak tersedia / sudah tutup buku');
            }

            if ($model->date_receive == null) {
                $model->date_receive = Carbon::now()->format('Y-m-d');
            }

            if ($model->date_receive_time == null) {
                $model->date_receive_time = Carbon::now()->format('H:i:s');
            }

            if (is_null($model->status)) {
                $model->status = 'pending';
            }

            if (is_null($model->branch_id)) {
                $model->branch_id = get_current_branch_id();
            }

            $model->created_by = Auth::user()->id;
        });

        static::created(function ($model) {
            if ($model->reference->project) {
                $model->project_id = $model->reference->project->id;
            }
            $model->purchase_id = $model->reference->purchase_id ?? null;
            $model->save();
        });

        static::updated(function ($model) {
            if ($model->getOriginal("status") != $model->status) {
                $route_bind = [
                    'general' => 'general',
                    'jasa' => 'service',
                    'trading' => 'trading',
                    'transport' => 'transport',
                ];

                // ! TRANSPORT =====================================================================================================================================
                if ($model->tipe == 'transport') {
                    if (in_array($model->status, ['reject', 'void', 'revert', 'cancel'])) {
                        foreach ($model->item_receiving_report_purchase_transport->item_receiving_report_purchase_transport_details ?? [] as $key => $value) {
                            if ($value->delivery_order) {
                                $value->delivery_order->is_item_receiving_report_created = false;
                                $value->delivery_order->save();
                            }
                        }

                        if (in_array($model->status, ['reject', 'void', 'cancel'])) {
                            $model->rollbackObserverAfterCreate();
                        }
                    }
                }
                // ! END TRANSPORT =====================================================================================================================================

                // ! TRADING =====================================================================================================================================
                if ($model->tipe == 'trading') {
                    $purchase_order = $model->reference;
                    if ($model->getOriginal('status') != $model->status) {
                        $po_trading_detail = $purchase_order->po_trading_detail;

                        $jumlah_lpbs = ItemReceivingPoTrading::whereHas('item_receiving_report', function ($query) use ($purchase_order) {
                            $query->where('reference_id', $purchase_order->id)
                                ->where('reference_model', PoTrading::class)
                                ->whereIn('status', ['approve', 'done']);
                        })->sum('liter_obs');

                        $po_trading_detail->jumlah_lpbs = $jumlah_lpbs;
                        $po_trading_detail->save();

                        if ($jumlah_lpbs == 0) {
                            $purchase_order->status = 'approve';
                            $purchase_order->pairing_status = 'pending';
                            $purchase_order->save();
                        }
                    }
                }
                // ! END TRADING =====================================================================================================================================

                // ! GENERAL + SERVICE =====================================================================================================================================
                if ($model->tipe == 'general' || $model->tipe == 'jasa') {
                    if (in_array($model->status, ['reject', 'void'])) {
                        // * decrease jumlah diterima di purchase order generals
                        foreach ($model->item_receiving_report_details as $key => $value) {
                            $purchase_order_detail = $value->reference;
                            $purchase_order_detail->quantity_received -= $value->jumlah_diterima;

                            if ($purchase_order_detail->quantity_received == $purchase_order_detail->quantity_received) {
                                $purchase_order_detail->status = 'done';
                            } else {
                                $purchase_order_detail->status = 'partial';
                            }

                            try {
                                $purchase_order_detail->save();
                            } catch (\Throwable $th) {
                                throw $th;
                            }

                            // * trigger purchase request detail lock stock
                            $purchase_request_detail = $purchase_order_detail->purchase_request_detail;
                            $lock_stock = $purchase_request_detail->lock_stock ?? null;

                            if ($lock_stock) {
                                $lock_stock->quantity_complete -= $value->jumlah_diterima;
                                $lock_stock->save();
                            }
                        }
                    }
                }
                // ! END GENERAL + SERVICE =====================================================================================================================================

                // ! APPROVE CREATE JOURNAL ========================================================================
                if ($model->status == 'approve') {
                    $journal = new JournalHelpers($model->tipe, $model->id);
                    $journal->generate();
                }
                // ! APPROVE CREATE JOURNAL ========================================================================

                // ! REVERT / VOID DELETE JOURNAL ========================================================================
                if (in_array($model->status, ['reject', 'revert', 'void'])) {
                    Journal::where('reference_model', self::class)
                        ->where('reference_id', $model->id)
                        ->delete();

                    self::deleteGeneratedData($model->id);
                }
                // ! REVERT / VOID DELETE JOURNAL ========================================================================
            }
        });

        static::deleted(function ($model) {
            // * if not in status revert all quantity from purchase
            if (!in_array($model->status, ['revert'])) {
                if ($model->tipe == 'trading') {
                } elseif ($model->tipe == 'transport') {
                    if (in_array($model->status, ['reject', 'void', 'revert', 'cancel', 'pending'])) {
                        foreach ($model->item_receiving_report_purchase_transport->item_receiving_report_purchase_transport_details ?? [] as $key => $value) {
                            if ($value->delivery_order) {
                                $value->delivery_order->is_item_receiving_report_created = false;
                                $value->delivery_order->save();
                            }
                        }
                    }

                    $purchase_order = $model->reference;
                    if ($purchase_order->status == 'done') {
                        $purchase_order->status = 'partial-sent';
                        $purchase_order->save();
                    }
                } else {
                    // foreach ($model->item_receiving_report_details as $key => $value) {
                    //     if ($value->reference->quantity_received > 0) {
                    //         $value->reference->quantity_received -= $value->jumlah_diterima;
                    //         $value->reference->save();
                    //     }
                    // }
                }
            }

            $journals = Journal::where('reference_id', $model->id)
                ->where('reference_model', ItemReceivingReport::class)->get();
            foreach ($journals as $journal) {
                $journal->journal_details->each(function ($detail) {
                    $detail->delete();
                });
                $journal->delete();
            }

            // if type trading then decrease jumlah_lpbs
            if ($model->tipe == 'trading' && $model->status != 'revert') {
                $purchase_order = $model->reference;
                $po_trading_detail = $purchase_order->po_trading_detail;

                $jumlah_lpbs = ItemReceivingPoTrading::whereHas('item_receiving_report', function ($query) use ($purchase_order) {
                    $query->where('reference_id', $purchase_order->id)
                        ->where('reference_model', PoTrading::class)
                        ->whereIn('status', ['approve', 'done']);
                })->sum('liter_obs');

                $po_trading_detail->jumlah_lpbs = $jumlah_lpbs;
                $po_trading_detail->save();
            }
            self::deleteGeneratedData($model->id);
        });
    }

    /**
     * set attrbutes model
     *
     * @param $request
     */
    public function loadModel($request)
    {
        foreach ($this->fillable as $key_field) {
            foreach ($request as $key_request => $value) {
                if ($key_field == $key_request) {
                    $this->setAttribute($key_field, $value);
                }
            }
        }
    }

    /**
     * getLogsData
     *
     * @return array
     */
    public function getLogsDataAttribute()
    {
        $activity_logs = ActivityLog::where('subject_type', ItemReceivingReport::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', ItemReceivingReport::class)
            ->where('reference_id', $this->id)
            ->orderByDesc('created_at')
            ->get();

        return compact('status_logs', 'activity_logs');
    }

    /**
     * getCheckAvailableDateAttribute for generated journal
     *
     * @return bool
     */
    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->date_receive);
    }

    /**
     * this function delete all generated data like stock mutation when data is revert, void, reject etc
     *
     * @param $id
     * @return void
     */
    protected static function deleteGeneratedData($id): void
    {
        $model = self::withTrashed()->find($id);

        // !GENERAL
        if (in_array($model->tipe, ['general', 'jasa'])) {
            foreach ($model->item_receiving_report_details as $key => $value) {
                $lpb_detail = \App\Models\ItemReceivingReportDetail::find($value->id);

                if ($lpb_detail->item->item_category->item_type->nama == "asset") {
                    // * DELETE DATA ASSET
                    $assets = \App\Models\Asset::where('item_receiving_report_detail_id', $lpb_detail->id)->get();
                    foreach ($assets as $key => $asset) {
                        if ($asset->status == 'active') {
                            // when asset status is active throw exception
                            throw new \Exception("Tidak dapat menghapus data, karena aset sudah aktif");
                        }
                        $asset->delete();
                    }
                } elseif ($lpb_detail->item->item_category->item_type->nama == "biaya dibayar dimuka") {
                    // * DELETE DATA LEASE
                    $leases = \App\Models\Lease::where('item_receiving_report_detail_id', $lpb_detail->id)->get();
                    foreach ($leases as $key => $lease) {
                        if ($lease->status == 'active') {
                            // when lease status is active throw exception
                            throw new \Exception("Tidak dapat menghapus data, karena sewa sudah aktif");
                        }
                        $lease->delete();
                    }
                } else {
                    // * DELETE DATA STOCK MUTATION
                    \App\Models\StockMutation::where('document_model', self::class)
                        ->where('document_id', $model->id)
                        ->where('item_id', $lpb_detail->item_id)
                        ->where('vendor_model', Vendor::class)
                        ->where('vendor_id', $model->reference->vendor_id)
                        ->delete();
                }
            }
        }

        // !TRADING
        if (in_array($model->tipe, ['trading'])) {
            $item = $model->item_receiving_report_po_trading;
            // * DELETE DATA STOCK MUTATION
            \App\Models\StockMutation::where('document_model', self::class)
                ->where('document_id', $model->id)
                ->where('item_id', $item->item_id)
                ->where('vendor_model', Vendor::class)
                ->where('vendor_id', $model->reference->vendor_id)
                ->delete();
        }
    }

    public function observerAfterCreate()
    {
        $model = $this;
        // ! INCREASE PURCHASE DETAIL RECEiVING

        // * TRADING
        try {
            if ($model->tipe == 'trading') {
                $purchase_order = $model->reference;
                $po_trading_detail = $purchase_order->po_trading_detail;

                $jumlah_lpbs = ItemReceivingPoTrading::whereHas('item_receiving_report', function ($query) use ($purchase_order) {
                    $query->where('reference_id', $purchase_order->id)
                        ->where('reference_model', PoTrading::class)
                        ->whereIn('status', ['approve', 'done']);
                })->sum('liter_obs');

                $po_trading_detail->jumlah_lpbs = $jumlah_lpbs;
                $po_trading_detail->save();

                // ! CREATING ITEM RECEIVING REPORT CALCULATION
                $total_tax  = 0;
                $model->reference->purchase_order_taxes->map(function ($tax) use (&$total_tax, $po_trading_detail, $model) {
                    // tax ppn
                    if ($tax->tax_trading_id) {
                        $price = $po_trading_detail->harga - $po_trading_detail->discount_per_liter;
                        $total_tax += $tax->value * ($price * $model->item_receiving_report_po_trading->liter_15);
                    } else {
                        $price = $po_trading_detail->harga;
                        $total_tax += $tax->value * ($price * $model->item_receiving_report_po_trading->liter_15);
                    }
                });

                $item_receiving_report_po_trading = $model->item_receiving_report_po_trading;

                $price = $po_trading_detail->harga - $po_trading_detail->discount_per_liter;
                $item_receiving_report_po_trading->sub_total = $item_receiving_report_po_trading->liter_15 * $price;
                $item_receiving_report_po_trading->tax_total = $total_tax;
                $item_receiving_report_po_trading->total = $item_receiving_report_po_trading->sub_total + $item_receiving_report_po_trading->tax_total;
                $item_receiving_report_po_trading->save();

                // ! CALCULATE ADDITIONAL
                $additional_taxes = PurchaseOrderAdditionalTaxs::whereIn('po_additional_id', $model->item_receiving_po_trading_additionals->pluck('purchase_order_additional_items_id')->toArray())->get();
                foreach ($model->item_receiving_po_trading_additionals as $key => $value) {
                    $taxes = $additional_taxes->where('po_additional_id', $value->purchase_order_additional_items_id);
                    $subtotal = $value->receive_qty * $value->purchase_order_additional_items->harga;
                    $tax_total = $taxes->map(function ($tax) use ($subtotal) {
                        return $tax->value * $subtotal;
                    })
                        ->sum();
                    $total = $subtotal + $tax_total;

                    $value->subtotal = $subtotal;
                    $value->tax_total = $tax_total;
                    $value->total = $total;
                    $value->save();
                }

                // ! CREATING ITEM RECEIVING REPORT CALCULATION
                $model->exchange_rate = $model->reference->exchange_rate;
                $model->sub_total = $item_receiving_report_po_trading->sub_total + $model->item_receiving_po_trading_additionals->sum('subtotal');
                $model->tax_total = $total_tax + $model->item_receiving_po_trading_additionals->sum('tax_total');
                $model->total = $item_receiving_report_po_trading->total + $model->item_receiving_po_trading_additionals->sum('total');

                $model->save();

                // TAX SUMMARY
                $item_receiving_report_trading_coas = $model->item_receiving_report_coas->filter(function ($item_receiving_report_coa) {
                    return strtolower($item_receiving_report_coa->type) == 'tax';
                })->values();

                $item_receiving_report_trading_coas = $item_receiving_report_trading_coas->map(function ($item_receiving_report_trading_coa) use ($model) {
                    $item_receiving_report_po_trading = $model->item_receiving_report_po_trading;
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

                $group_lpb_coas = $item_receiving_report_trading_coas->groupBy('tax_id')
                    ->map(function ($item) {
                        return $item->groupBy('tax_value');
                    });

                // dd($group_lpb_coas);
                foreach ($group_lpb_coas as $key => $group_lpb_coa) {
                    foreach ($group_lpb_coa as $key2 => $group_lpb_coa2) {
                        LpbTaxSummary::updateOrCreate(
                            [
                                'item_receiving_report_id' => $model->id,
                                'tax_id' => $key,
                            ],
                            [
                                'item_receiving_report_id' => $model->id,
                                'tax_id' => $key,
                                'tax_value' => $group_lpb_coa2->first()->tax_value,
                                'sub_total' => $group_lpb_coa2->sum('sub_total'),
                                'tax_amount' => $group_lpb_coa2->sum('tax_amount'),
                            ]
                        );
                    }
                }
            }

            // * GENERAL & SERVICE
            if ($model->tipe == 'general' || $model->tipe == 'jasa') {
                // * increase jumlah diterima di purchase order generals
                foreach ($model->item_receiving_report_details as $key => $value) {
                    $purchase_order_detail = $value->reference;
                    $purchase_order_detail->quantity_received += $value->jumlah_diterima;

                    if ($purchase_order_detail->quantity < $purchase_order_detail->quantity_received) {
                        throw new \Exception('Quantity received is over than quantity');
                    }

                    if ($purchase_order_detail->quantity == $purchase_order_detail->quantity_received) {
                        $purchase_order_detail->status = 'done';
                    } else {
                        $purchase_order_detail->status = 'partial';
                    }

                    try {
                        $purchase_order_detail->save();
                    } catch (\Throwable $th) {
                        throw $th;
                    }

                    // * TRIGGER TO LOCK STOCK PURCHASE REQUEST DETAIL
                    if ($purchase_order_detail->purchase_request_detail) {
                        $purchase_request_detail = $purchase_order_detail->purchase_request_detail;

                        $lock_stock = $purchase_request_detail->lock_stock;
                        if ($lock_stock) {
                            $lock_stock->quantity_complete += $value->jumlah_diterima;
                            $lock_stock->save();
                        }
                    }
                }

                // ! CALCULATE ITEM RECEIVING REPORT
                if ($model->tipe == 'general') {
                    $sub_total = 0;
                    $total_tax = 0;

                    $model->item_receiving_report_details->map(function ($detail) use ($model, &$sub_total, &$total_tax) {
                        $reference = $detail->reference;

                        $single_sub_total = $detail->jumlah_diterima * $reference->price;
                        $single_total_tax = 0;

                        $reference->purchase_order_general_detail_item_taxes->map(function ($tax) use (&$single_total_tax, $detail) {
                            $single_total_tax += $tax->value * ($detail->jumlah_diterima * $tax->purchase_order_general_detail_item->price);
                        });

                        $single_total_tax = $single_total_tax;
                        $detail->sub_total = $single_sub_total;
                        $detail->tax_total = $single_total_tax;
                        $detail->total = $single_sub_total + $single_total_tax;
                        $detail->save();

                        $sub_total += $single_sub_total;
                        $total_tax += $single_total_tax;
                    });

                    $model->sub_total = $sub_total;
                    $model->tax_total = $total_tax;
                    $model->total = $sub_total + $total_tax;

                    $model->save();
                }

                if ($model->tipe == 'jasa') {
                    $sub_total = 0;
                    $total_tax = 0;

                    $model->item_receiving_report_details->map(function ($detail) use ($model, &$sub_total, &$total_tax) {
                        $reference = $detail->reference;

                        $single_sub_total = $detail->jumlah_diterima * $reference->price;
                        $single_total_tax = 0;

                        $reference->purchase_order_service_detail_item_taxes->map(function ($tax) use (&$single_total_tax, $detail) {
                            $single_total_tax += $tax->value * ($detail->jumlah_diterima * $tax->purchase_order_service_detail_item->price);
                        });

                        $single_total_tax = $single_total_tax;
                        $detail->sub_total = $single_sub_total;
                        $detail->tax_total = $single_total_tax;
                        $detail->total = $single_sub_total + $single_total_tax;
                        $detail->save();

                        $sub_total += $single_sub_total;
                        $total_tax += $single_total_tax;
                    });

                    $model->sub_total = $sub_total;
                    $model->tax_total = $total_tax;
                    $model->total = $sub_total + $total_tax;

                    $model->save();
                }


                // SUMMARY TAX
                $item_receiving_report_general_coas = $model->item_receiving_report_coas->filter(function ($item_receiving_report_coa) {
                    return strtolower($item_receiving_report_coa->type) == 'tax';
                });

                $item_receiving_report_general_details = $model->item_receiving_report_details;

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

                $group_lpb_coas = $item_receiving_report_general_coas->groupBy('tax_id')
                    ->map(function ($item) {
                        return $item->groupBy('tax_value');
                    });

                foreach ($group_lpb_coas as $key => $group_lpb_coa) {
                    foreach ($group_lpb_coa as $key2 => $group_lpb_coa2) {
                        LpbTaxSummary::updateOrCreate([
                            'item_receiving_report_id' => $model->id,
                            'tax_id' => $key,
                        ], [
                            'item_receiving_report_id' => $model->id,
                            'tax_id' => $key,
                            'tax_value' => $group_lpb_coa2->first()->tax_value,
                            'sub_total' => $group_lpb_coa2->sum('sub_total'),
                            'tax_amount' => $group_lpb_coa2->sum('tax_amount'),
                        ]);
                    }
                }
            }

            // * TRANSPORT
            if ($model->tipe == 'transport') {
                // ! CALCULATE ITEM RECEIVING REPORT
                $sub_total = 0;
                $subtotal_by_po = 0;
                $total_tax = 0;

                $reference = $model->reference;
                $detail = $model->item_receiving_report_purchase_transport;
                $taxes = $reference->purchase_transport_taxes;

                $countReceivedDeliveryOrder = DeliveryOrder::where('purchase_transport_id', $model->purchase_transport_id)
                    ->where('is_item_receiving_report_created', true)
                    ->where('status', 'done')
                    ->count();

                $model->item_receiving_report_purchase_transport->item_receiving_report_purchase_transport_details->map(function ($detail_transport) use (&$sub_total, &$total_tax, $reference, $taxes, &$countReceivedDeliveryOrder, $detail, &$subtotal_by_po, $model) {
                    $value = $detail_transport->sended * $reference->harga;

                    if ($detail_transport->delivery_order) {
                        $value_po = $detail_transport->delivery_order->load_quantity * $reference->harga;
                        $detail_transport->delivery_order->is_item_receiving_report_created = true;
                        $detail_transport->delivery_order->save();

                        $loss_tolerance = $detail->loss_tolerance;
                        if ($loss_tolerance) {
                            $fuel_price = $detail_transport->delivery_order->so_trading->so_trading_detail->harga;
                            $loss_tolerance_qty = $detail_transport->sended * $loss_tolerance / 100;
                            $loss_qty = $detail_transport->sended - $detail_transport->received;

                            $loss_oat = 0;
                            $loss_fuel = 0;
                            if ($loss_qty > $loss_tolerance_qty) {
                                $loss_oat = ($loss_qty - $loss_tolerance_qty) * $reference->harga;
                                $loss_fuel = ($loss_qty - $loss_tolerance_qty) * $fuel_price;

                                $value = $value - $loss_oat - $loss_fuel;
                            }
                        }
                    } else {
                        $value_po = $detail_transport->sended * $reference->harga;
                    }

                    $tax = 0;

                    $taxes->map(function ($taxes) use (&$tax, $value) {
                        $tax += $taxes->value * $value;
                    });

                    $detail_transport->sub_total = $value;
                    $detail_transport->tax_total = $tax;
                    $detail_transport->total = $value + $tax;
                    $detail_transport->save();

                    $subtotal_by_po += $value_po;
                    $sub_total += $value;
                    $total_tax += $tax;
                    $countReceivedDeliveryOrder++;
                });


                $sub_total += $detail->lost_discount;

                $total_tax = 0;
                $taxes->map(function ($taxes) use (&$total_tax, $sub_total, $model, $subtotal_by_po) {
                    $sub_total_for_tax = $sub_total;
                    if ($model->item_receiving_report_purchase_transport->tax_option == 'full') {
                        $sub_total_for_tax -= $item_receiving_report->item_receiving_report_purchase_transport->lost_discount ?? 0;
                    } else if ($model->item_receiving_report_purchase_transport->tax_option == 'by_po') {
                        $sub_total_for_tax = $subtotal_by_po;
                    }
                    $total_tax += $taxes->value * $sub_total_for_tax;
                });

                $detail->subtotal_by_po = $subtotal_by_po;
                $detail->sub_total = $sub_total;
                $detail->tax_total = $total_tax;
                $detail->total = $sub_total + $total_tax;
                $detail->save();

                // * trigger to purchase transport status

                $purchaseTransportCount = $reference
                    ->purchase_transport_details()
                    ->sum('jumlah_do');

                $deliveryOrderRejectVoid = DeliveryOrder::where('purchase_transport_id', $reference->id)
                    ->whereIn('status', ['void', 'reject'])
                    ->count();

                if ($purchaseTransportCount - $deliveryOrderRejectVoid == $countReceivedDeliveryOrder) {
                    $reference->status = 'done';
                    $reference->save();
                }

                $model->sub_total = $sub_total;
                $model->tax_total = $total_tax;
                $model->total = $sub_total + $total_tax;

                $model->save();


                // TAX SUMMARY
                $item_receiving_report_transport_coas = $model->item_receiving_report_coas->filter(function ($item_receiving_report_coa) {
                    return strtolower($item_receiving_report_coa->type) == 'tax';
                });

                $item_receiving_report_transport_coas = $item_receiving_report_transport_coas->map(function ($item_receiving_report_transport_coa) use ($model) {
                    $item_receiving_report_transport = $item_receiving_report_transport_coa->item_receiving_report;
                    if ($item_receiving_report_transport_coa->reference) {
                        $sub_total_for_tax = $item_receiving_report_transport->sub_total;
                        if ($model->item_receiving_report_purchase_transport->tax_option == 'full') {
                            $sub_total_for_tax -= $item_receiving_report_transport->item_receiving_report_purchase_transport->lost_discount ?? 0;
                        } else if ($model->item_receiving_report_purchase_transport->tax_option == 'by_po') {
                            $sub_total_for_tax = $item_receiving_report_transport->item_receiving_report_purchase_transport->subtotal_by_po;
                        }

                        $item_receiving_report_transport_coa->tax_id = $item_receiving_report_transport_coa->reference->tax_id;
                        $item_receiving_report_transport_coa->tax_value = $item_receiving_report_transport_coa->reference->value;
                        $item_receiving_report_transport_coa->sub_total = $sub_total_for_tax;
                        $item_receiving_report_transport_coa->tax_amount = $sub_total_for_tax * $item_receiving_report_transport_coa->tax_value;
                    }

                    return $item_receiving_report_transport_coa;
                });

                $group_lpb_coas = $item_receiving_report_transport_coas->groupBy('tax_id')
                    ->map(function ($item) {
                        return $item->groupBy('tax_value');
                    });

                foreach ($group_lpb_coas as $key => $group_lpb_coa) {
                    foreach ($group_lpb_coa as $key2 => $group_lpb_coa2) {
                        LpbTaxSummary::updateOrCreate(
                            [
                                'item_receiving_report_id' => $model->id,
                                'tax_id' => $key,
                            ],
                            [
                                'item_receiving_report_id' => $model->id,
                                'tax_id' => $key,
                                'tax_value' => $group_lpb_coa2->first()->tax_value,
                                'sub_total' => $group_lpb_coa2->sum('sub_total'),
                                'tax_amount' => $group_lpb_coa2->sum('tax_amount'),
                            ]
                        );
                    }
                }
            }

            return $model;
        } catch (\Throwable $th) {
            throw $th;
        }

        // ! INCREASE PURCHASE DETAIL RECEiVING
    }

    public function rollbackObserverAfterCreate()
    {
        $model = $this;
        // ! INCREASE PURCHASE DETAIL RECEiVING

        // * TRADING
        if ($model->tipe == 'trading') {
        }

        // * GENERAL & SERVICE
        if ($model->tipe == 'general' || $model->tipe == 'jasa') {
            // * increase jumlah diterima di purchase order generals
            foreach ($model->item_receiving_report_details as $key => $value) {
                $purchase_order_detail = $value->reference;
                $purchase_order_detail->quantity_received -= $value->jumlah_diterima;

                if ($purchase_order_detail->quantity == $purchase_order_detail->quantity_received) {
                    $purchase_order_detail->status = 'done';
                } elseif ($purchase_order_detail->quantity_received == 0) {
                    $purchase_order_detail->status = 'approve';
                } else {
                    $purchase_order_detail->status = 'partial';
                }

                try {
                    $purchase_order_detail->save();
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
        }

        // * transport
        if ($model->tipe == 'transport') {
            $purchase_order = $model->reference;
            if ($purchase_order->status == 'done') {
                $purchase_order->status = 'partial-sent';
                $purchase_order->save();
            }
        }

        LpbTaxSummary::where('item_receiving_report_id', $model->id)->delete();
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class)->withTrashed();
    }

    public function reference()
    {
        return $this->belongsTo($this->reference_model, 'reference_id')->withTrashed();
    }

    /**
     * Get the ware_house that owns the data.
     */
    public function ware_house()
    {
        return $this->belongsTo(WareHouse::class)->withTrashed();
    }

    /**
     * Get the branch that owns the ItemReceivingReport
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    /**
     * Get the project that owns the ItemReceivingReport
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class)->withTrashed();
    }

    public function item_receiving_report_details()
    {
        return $this->hasMany(ItemReceivingReportDetail::class);
    }

    public function item_receiving_report_po_trading()
    {
        return $this->hasOne(ItemReceivingPoTrading::class);
    }

    public function item_receiving_po_trading_additionals()
    {
        return $this->hasMany(ItemReceivingPoTradingAdditional::class);
    }

    /**
     * Get the item_receiving_report_purchase_transport associated with the ItemReceivingReport
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function item_receiving_report_purchase_transport(): HasOne
    {
        return $this->hasOne(ItemReceivingReportPurchaseTransport::class);
    }

    /**
     * Get the item_receiving_report_coas for the current model.
     */
    public function item_receiving_report_coas()
    {
        return $this->hasMany(ItemReceivingReportCoa::class);
    }

    /**
     * Get the item_receiving_report_coas for the current model.
     */
    public function supplier_invoice_detail()
    {
        return $this->hasOne(SupplierInvoiceDetail::class);
    }

    /**
     * Get the supplier invoice payments for the current model.
     */
    public function supplier_invoice_payments()
    {
        return $this->hasMany(SupplierInvoicePayment::class);
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getOutstandingAttribute()
    {
        return $this->total - $this->supplier_invoice_payments->sum('pay_amount');
    }

    public function purchase_returns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function stock_usages()
    {
        return $this->hasMany(StockUsage::class, 'item_receiving_report_id');
    }
}
