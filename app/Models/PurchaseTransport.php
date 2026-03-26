<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PurchaseTransport extends Model
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
        'branch_id',
        'target_delivery',
        'purchase_id',
        'purchase_request_id',
        'so_trading_id',
        'vendor_id',
        'ware_house_id',
        'item_id',
        'currency_id',
        'purchase_transport_id',
        'exchange_rate',
        'kode',
        'status',
        'type',
        'harga',
        'sub_total',
        'total',
        'created_by',
        'approved_by',
        'delivery_destination',
        'send_from',
        'po_trading_id',
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
            ->setDescriptionForEvent(fn (string $eventName) => "This data has been {$eventName}")
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
        $validate = [
            'purchase_request_id' => 'nullable|exists:purchase_requests,id',
            'so_trading_id' => 'required|exists:sale_orders,id',
        ];

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
            if ($model->kode == null) {
                $branch = Branch::find($model->branch_id);
                $model->kode = generate_code(self::class, 'kode', 'target_delivery', 'POTP', branch_sort: $branch->sort ?? null, date: $model->target_delivery,);
            }

            $model->status = 'pending';
        });

        static::created(function ($model) {
            $purchase = $model->purchase;
            $purchase->status = 'pending';
            $purchase->save();
        });

        static::updating(function ($model) {
            if ($model->isDirty('status')) {
                $purchase = $model->purchase;
                $purchase->status = $model->status;
                $purchase->save();

                if ($model->status == 'void' || $model->status == 'reject') {

                    // delete all delivery orders
                    $model->delivery_orders()->each(function ($item) {
                        // $item->delete();
                    });

                    // if purchase transport is double handling and belongs to purchase transport
                    if ($model->type == 'double_handling' && is_null($model->purchase_transport_id) && $model->purchase_transport_has_one) {
                        $purchase_transport = $model->purchase_transport_has_one;
                        $purchase_transport->status = $model->status;
                        $purchase_transport->save();
                    }
                }

                if ($model->status == 'approve') {
                    if ($model->delivery_destination != 'to_warehouse') {
                        // if the purchase transport is already have delivery order
                        if ($model->delivery_orders->count() == 0) {
                            if ($model->type == 'double_handling' && is_null($model->purchase_transport_id) && $model->purchase_transport_has_one) {
                                // * create first purchase transport delivery order

                                foreach ($model->purchase_transport_details as $key => $value) {
                                    $jumlah = $value->jumlah;

                                    for ($i = 0; $i < $value->jumlah_do; $i++) {
                                        $do_one = new DeliveryOrder();
                                        $do_one->loadModel([
                                            'so_trading_id' => $model->so_trading_id,
                                            'purchase_transport_id' => $model->id,
                                            'purchase_transport_detail_id' => $value->id,
                                            'delivery_order_ship_id' => $model->delivery_order_ship_id,
                                            'ware_house_id' => $model->ware_house_id,
                                            'sh_number_id' => $model->so_trading->sh_number_id,
                                            'hpp' => $model->so_trading->so_trading_detail->item->getCurrentValue(),
                                            'target_delivery' => $model->target_delivery,
                                            'load_quantity' => $jumlah,
                                            'load_quantity_realization' => 0,
                                            'unload_quantity' => 0,
                                            'quantity_used' => 0,
                                            'status' => 'approve',
                                            'created_by'  => $model->created_by,
                                            'approved_by'  => $model->approved_by,
                                            'type' => 'delivery-order-2',
                                        ]);

                                        try {
                                            $do_one->save();
                                        } catch (\Throwable $th) {
                                            throw $th;
                                        }
                                    }
                                }

                                // * create second purchase transport delivery order
                                $purchase_transport = $model->purchase_transport_has_one;
                                $purchase_transport->status = 'approve';
                                $purchase_transport->save();

                                // foreach ($purchase_transport->purchase_transport_details as $key => $value) {
                                //     $jumlah = $value->jumlah;

                                //     for ($i = 0; $i < $value->jumlah_do; $i++) {
                                //         $do = new DeliveryOrder();
                                //         $do->loadModel([
                                //             'so_trading_id' => $purchase_transport->so_trading_id,
                                //             'purchase_transport_id' => $purchase_transport->id,
                                //             'purchase_transport_detail_id' => $value->id,
                                //             'delivery_order_ship_id' => $purchase_transport->delivery_order_ship_id,
                                //             'ware_house_id' => $model->ware_house_id,
                                //             'delivery_order_id' => $do_one->id,
                                //             'sh_number_id' => $purchase_transport->so_trading->sh_number_id,
                                //             'hpp' => $purchase_transport->so_trading->so_trading_detail->item->getCurrentValue(),
                                //             'target_delivery' => $purchase_transport->target_delivery,
                                //             'load_quantity' => $jumlah,
                                //             'load_quantity_realization' => 0,
                                //             'unload_quantity' => 0,
                                //             'quantity_used' => 0,
                                //             'status' => 'approve',
                                //             'created_by'  => $purchase_transport->created_by,
                                //             'approved_by'  => $purchase_transport->approved_by,
                                //             'type' => 'delivery-order',
                                //         ]);

                                //         try {
                                //             $do->save();
                                //         } catch (\Throwable $th) {
                                //             throw $th;
                                //         }
                                //     }
                                // }
                            } elseif ($model->type == 'not_double_handling') {
                                if (!in_array($model->delivery_destination, ['to_customer'])) {
                                    foreach ($model->purchase_transport_details as $key => $value) {
                                        $jumlah = $value->jumlah;

                                        for ($i = 0; $i < $value->jumlah_do; $i++) {
                                            $do = new DeliveryOrder();
                                            $do->loadModel([
                                                'so_trading_id' => $model->so_trading_id,
                                                'purchase_transport_id' => $model->id,
                                                'purchase_transport_detail_id' => $value->id,
                                                'delivery_order_ship_id' => $model->delivery_order_ship_id,
                                                'delivery_order_id' => $model->delivery_order_id,
                                                'ware_house_id' => $model->ware_house_id,
                                                'sh_number_id' => $model->so_trading->sh_number_id,
                                                'hpp' => $model->so_trading->so_trading_detail->item->getCurrentValue(),
                                                'target_delivery' => $model->target_delivery,
                                                'load_quantity' => $jumlah,
                                                'load_quantity_realization' => 0,
                                                'unload_quantity' => 0,
                                                'quantity_used' => 0,
                                                'status' => 'approve',
                                                'created_by'  => $model->created_by,
                                                'approved_by'  => $model->approved_by,
                                                'type' => 'delivery-order',
                                            ]);

                                            try {
                                                $do->save();
                                            } catch (\Throwable $th) {
                                                throw $th;
                                            }
                                        }
                                    }
                                }
                            }

                            if ($model->type == 'double_handling') {
                                // if using delivery order increase quantity used
                                if ($model->delivery_order_id) {
                                    $delivery_order = DeliveryOrder::findOrFail($model->delivery_order_id);
                                    $delivery_order->is_double_handling += true;
                                    $delivery_order->save();
                                }
                            }

                            $sale_order = SoTrading::findOrfail($model->so_trading_id);
                            if (in_array($sale_order->status, ['do_not_created', 'ready'])) {
                                $sale_order->status = 'not_yet_send';
                                $sale_order->save();
                            }
                        }
                    }
                }

                if ($model->status == 'reject') {
                    if ($model->type == 'double_handling' && is_null($model->purchase_transport_id) && $model->purchase_transport_has_one) {
                        $purchase_transport = $model->purchase_transport_has_one;
                        $purchase_transport->status = 'reject';
                        $purchase_transport->save();
                    }
                }
            }
        });

        static::deleting(function ($model) {
            $model->delivery_orders->delete();

            $purchase = $model->purchase;
            $purchase->delete();

            $model->purchase_request->status = 'approve';
            $model->purchase_request->save();
            create_activity_status_log_not_trait(PurchaseRequest::class, $model->purchase_request->id, 'your purchase was deleted / void', 'done', 'approve');
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
        $activity_logs = ActivityLog::where('subject_type', PurchaseTransport::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);

        $status_logs = ActivityStatusLog::where('reference_model', PurchaseTransport::class)
            ->where('reference_id', $this->id)
            ->orderByDesc('created_at')
            ->get();

        return compact('status_logs', 'activity_logs');
    }

    /**
     * getSubTotal
     *
     * @return mixed
     */
    public function getSubTotalAttribute()
    {
        $total = 0;
        foreach ($this->purchase_transport_details as $key => $value) {
            $total += $value->jumlah * $value->jumlah_do;
        }

        return $total * $this->harga;
    }

    /**
     * getCheckAvailableDateAttribute for generate journal
     *
     * @return bool
     */
    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->target_delivery);
    }

    /**
     * is have any delivery order to request print
     *
     * @return mixed
     */
    public function getIsHaveAnyToRequestPrintAttribute()
    {
        $count = 0;
        foreach ($this->purchase_transport_details as $detail) {
            $count += $detail->delivery_orders->where('status_print', false)->where('status', 'request-print')->count();
        }

        return $count > 0;
    }

    /**
     * Get the item that owns the PurchaseTransport
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    public function purchase_request()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function so_trading()
    {
        return $this->belongsTo(SoTrading::class);
    }

    public function po_trading()
    {
        return $this->belongsTo(PoTrading::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class)->withTrashed();
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class)->withTrashed();
    }

    /**
     * Get the currency that owns the PurchaseTransport
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the ware_house that owns the PurchaseTransport
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ware_house(): BelongsTo
    {
        return $this->belongsTo(WareHouse::class)->withTrashed();
    }

    /**
     * Get the branch that owns the data.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the purchase_transport that owns the PurchaseTransport
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase_transport(): BelongsTo
    {
        return $this->belongsTo(PurchaseTransport::class);
    }

    /**
     * Get the purchase_transport_has_one associated with the PurchaseTransport
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function purchase_transport_has_one(): HasOne
    {
        return $this->hasOne(PurchaseTransport::class);
    }

    public function purchase_transport_details()
    {
        return $this->hasMany(PurchaseTransportDetail::class);
    }

    public function delivery_orders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    public function create()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function approve()
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    /**
     * Get all of the purchase_transport_taxes for the PurchaseTransport
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchase_transport_taxes(): HasMany
    {
        return $this->hasMany(PurchaseTransportTax::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
