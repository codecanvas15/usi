<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DeliveryOrder extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'so_trading_id',
        'purchase_transport_id',
        'purchase_transport_detail_id',
        'fleet_id',
        'employee_id',
        'branch_id',
        'sh_number_id',
        'item_receiving_report_id',
        'ware_house_id',
        'delivery_order_id',
        'code',
        'target_delivery',
        'load_date',
        'unload_date',
        'load_quantity_realization',
        'load_quantity',
        'unload_quantity',
        'unload_quantity_realization',
        'quantity_used',
        'hpp',
        'file',
        'description',
        'top_seal',
        'bottom_seal',
        'temperature',
        'initial_meter',
        'initial_final',
        'sg_meter',
        'status',
        'status_print',
        'is_invoice_created',
        'is_item_receiving_report_created',
        'is_old_data',
        'driver_name',
        'driver_phone',
        'vehicle_information',
        'is_double_handling',
        'type',
        'created_by',
        'approved_by',
        'external_number',
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
            // $code2 = $model->ware_house?->code ?? $model->so_trading?->customer?->code ?? "DO-DO";
            $code2 = $model->so_trading?->customer;
            $model->code = generate_code_with_cus_name(
                model: self::class,
                code: 'DO',
                code2: $code2,
                date_column: 'target_delivery',
                date: $model->target_delivery ?? \Carbon\Carbon::now()->format('Y-m-d'),
                filter: [],
            );

            if (is_null($model->status)) {
                $model->status = 'pending';
            }

            if (is_null($model->type)) {
                $model->type = 'delivery-order';
            }

            if (Auth::check()) {
                if (is_null($model->branch_id)) {
                    $model->branch_id = Auth::user()->branch_id;
                }
            }

            if (is_null($model->created_by)) {
                $model->created_by = Auth::id();
            }

            if (!is_null($model->delivery_order_ship)) {
                $model->ware_house_id = $model->delivery_order_ship->ware_house_id;
            }

            $model->is_old_data = false;
        });

        static::created(function ($model) {
            // * UPDATE SALE ORDER SENDED VALUE
            if (($model->type == 'delivery-order-2' || (($model->type == 'delivery-order') && is_null($model->delivery_order_id)))) {
                $so_trading_detail = $model->so_trading->so_trading_detail;
                $sudah_dikirim = DB::table('delivery_orders')
                    ->where('type', 'delivery-order')
                    ->where('so_trading_id', $so_trading_detail->so_trading_id)
                    ->whereNotIn('status', ['reject', 'void'])
                    ->whereNull('deleted_at')
                    ->sum('load_quantity');

                $so_trading_detail->sudah_dikirim = $sudah_dikirim;

                if ($so_trading_detail->sudah_dikirim >= $so_trading_detail->jumlah) {
                    $so_trading_detail->status = 'done';
                    $so_trading_detail->save();

                    // * update status so trading
                    $model->so_trading->status = 'delivery_complete';
                    $model->so_trading->save();
                } else {
                    if ($model->so_trading->status != 'partial-sended') {
                        $model->so_trading->status = 'partial-sended';
                        $model->so_trading->save();
                    }
                }

                try {
                    $so_trading_detail->save();

                    if ($model->purchase_transport) {
                        $purchase_transport =  $model->purchase_transport;
                        $delivered_qty = $purchase_transport->delivery_orders
                            ->filter(function ($item) {
                                return !in_array($item->status, ['void', 'reject']);
                            })
                            ->sum('load_quantity');

                        DB::table('purchase_transports')
                            ->where('id', $purchase_transport->id)
                            ->update(['delivered_qty' => $delivered_qty]);
                    }
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
        });

        static::updating(function ($model) {
            $so_trading_detail = $model->so_trading->so_trading_detail;


            if ($model->status != $model->getOriginal('status')) {
                // ! DONE ===============================================================================
                if ($model->status == 'done' && $model->type == 'delivery-order' && $model->is_old_data) {

                    // * update status print
                    $model->status_print = true;
                }
                // ! END DONE ===============================================================================

                // * done approve
                if (in_array($model->status, ['done', 'approve'])) {
                    // * update status sale order

                    if ($model->type == 'delivery-order') {
                        $sale_order = $model->so_trading;
                        if (!in_array($sale_order->status, ['done', 'delivery_complete', 'partial_sent'])) {
                            if ($sale_order->so_trading_detail->sudah_dikirim == $sale_order->so_trading_detail->jumlah) {
                                $sale_order->status = 'done';
                            } else {
                                $sale_order->status = 'partial_sent';
                            }

                            try {
                                $sale_order->save();
                            } catch (\Throwable $th) {
                                throw $th;
                            }
                        }

                        if ($model->delivery_order_id) {
                            $delivery_order = DeliveryOrder::find($model->delivery_order_id);
                            $delivery_order->calculateQtyUsed();
                        }
                    }

                    // * update purchase transport if have it
                    if ($model->purchase_transport_id) {
                        $purchase_transport = PurchaseTransport::find($model->purchase_transport_id);

                        if ($purchase_transport and !in_array($purchase_transport->status, ['reject', 'revert', 'cancel', 'done', 'partial-sent'])) {
                            $purchase_transport->status = 'partial-sent';
                        }

                        $purchase_transport->save();
                    }
                }
                // * done approve

                if ($model->status == 'approve') {
                    $model->approved_by = Auth::id();
                }

                // ! VOID ===============================================================================
                if (in_array($model->status, ['void', 'reject'])) {
                    $stock_mutation = StockMutation::where('document_model', DeliveryOrder::class)
                        ->where('document_id', $model->id)
                        ->delete();

                    // * UPDATE SALE ORDER SENDED VALUE
                    if (!$model->is_old_data) {
                        $so_trading_detail = $model->so_trading->so_trading_detail;
                        $sudah_dikirim = DB::table('delivery_orders')
                            ->where('type', 'delivery-order')
                            ->where('so_trading_id', $so_trading_detail->id)
                            ->whereNotIn('status', ['reject', 'void'])
                            ->whereNull('deleted_at')
                            ->sum('load_quantity');

                        $so_trading_detail->sudah_dikirim = $sudah_dikirim;

                        if ($so_trading_detail->sudah_dikirim >= $so_trading_detail->jumlah) {
                            $so_trading_detail->status = 'done';
                            $so_trading_detail->save();

                            // * update status so trading
                            $model->so_trading->status = 'delivery_complete';
                            $model->so_trading->save();
                        } else {

                            if ($so_trading_detail->sudah_dikirim == 0) {
                                $so_trading_detail->status = 'pairing';
                                $so_trading_detail->save();

                                // * update status so trading
                                $model->so_trading->status = 'ready';
                                $model->so_trading->save();
                            } else {
                                if ($model->so_trading->status != 'partial-sended') {
                                    $model->so_trading->status = 'partial-sended';
                                    $model->so_trading->save();
                                }
                            }
                        }

                        try {
                            $so_trading_detail->save();
                        } catch (\Throwable $th) {
                            throw $th;
                        }

                        // update the purchase transport
                        if ($model->purchase_transport_id) {
                            $purchase_transport = PurchaseTransport::find($model->purchase_transport_id);

                            if ($purchase_transport and !in_array($purchase_transport->status, ['reject', 'revert', 'cancel', 'done', 'partial-sent'])) {
                                $purchase_transport->status = 'partial-sent';
                            }

                            // check if all delivery order is void or reject make purchase transport status to approve
                            $delivery_orders = DeliveryOrder::where('purchase_transport_id', $model->purchase_transport_id)
                                ->where('id', '!=', $model->id)
                                ->get();

                            $is_all_void = true;
                            foreach ($delivery_orders as $delivery_order) {
                                if (!in_array($delivery_order->status, ['void', 'reject'])) {
                                    $is_all_void = false;
                                }
                            }

                            if ($is_all_void) {
                                $purchase_transport->status = 'approve';
                            }

                            $purchase_transport->save();
                        }
                    }
                }
                // ! VOID ===============================================================================
            }

            if (($model->isDirty('status') and $model->status == 'done')) {
                $is_purchase_item = $model->so_trading->so_trading_detail->item->item_category->item_type->nama == 'purchase item';
                if ($is_purchase_item) {
                    if (($model->type == 'delivery-order' && is_null($model->delivery_order_id)) or ($model->type == 'delivery-order-2' && is_null($model->delivery_order_id))) {
                        // * get last stock mutation
                        $last_stock = StockMutation::orderBy('id', 'desc')
                            ->where('item_id', $so_trading_detail->item_id)
                            ->first();

                        // * CREATE STOCK MUTATION
                        $stock_mutation = StockMutation::where('document_model', DeliveryOrder::class)
                            ->where('document_id', $model->id)
                            ->first();

                        if (!$stock_mutation) {
                            $stock_mutation = new StockMutation();
                        }

                        $branch_id = $model->ware_house?->branch_id;

                        $warehouse_id = $model->ware_house_id;
                        if (!$warehouse_id) {
                            $warehouse_id = $model->delivery_order->ware_house_id;
                        }

                        $stock_mutation_qty = $model->load_quantity_realization;
                        if ($model->type == 'delivery-order-2' && is_null($model->delivery_order_id)) {
                            $stock_mutation_qty = $model->unload_quantity_realization;
                        }
                        $stock_mutation->loadModel([
                            'ware_house_id' => $model->ware_house_id,
                            'branch_id' => $branch_id ?? get_current_branch_id(),
                            'item_id' => $last_stock->item_id,
                            'price_id' => $last_stock->price_id,
                            'document_model' => DeliveryOrder::class,
                            'document_id' => $model->id,
                            'document_code' => "{$model->code} - {$model->so_trading->nomor_so}",
                            'date' => $model->load_date ?? $model->target_delivery,
                            'vendor_model' => Vendor::class,
                            'vendor_id' => 1,
                            'type' => 'delivery order trading',
                            'out' => $stock_mutation_qty,
                            'note' => "Delivery order trading",
                            'price_unit' => $model->hpp,
                            'subtotal' => $model->hpp * $stock_mutation_qty,
                        ]);

                        try {
                            $stock_mutation->save();
                        } catch (\Throwable $th) {
                            throw $th;
                        }

                        $journal = Journal::where('reference_model', DeliveryOrder::class)
                            ->where('reference_id', $model->id)
                            ->delete();

                        $journal = new \App\Http\Helpers\JournalHelpers('delivery-order-trading', $model->id);
                        $journal->generate();
                    }
                }
            }

            // * is status change to from close to approve
            if ($model->isDirty('status') and $model->status == 'approve' and $model->getOriginal('status') == 'done') {
                // delete all stock mutation and journal
                $stock_mutation = StockMutation::where('document_model', DeliveryOrder::class)
                    ->where('document_id', $model->id)
                    ->delete();

                $journal = Journal::where('reference_model', DeliveryOrder::class)
                    ->where('reference_id', $model->id)
                    ->delete();

                // if model is double handling and has delivery order id
                // then update quantity used in delivery order
                if ($model->type == 'delivery-order') {
                    if ($model->delivery_order_id) {
                        $delivery_order = DeliveryOrder::find($model->delivery_order_id);
                        $delivery_order->calculateQtyUsed();
                    }
                }

                // update the purchase transport
                if ($model->purchase_transport_id) {
                    $purchase_transport = PurchaseTransport::find($model->purchase_transport_id);

                    if ($purchase_transport and !in_array($purchase_transport->status, ['reject', 'revert', 'cancel', 'done', 'partial-sent'])) {
                        $purchase_transport->status = 'partial-sent';
                    }

                    // check if all delivery order is void or reject make purchase transport status to approve
                    $delivery_orders = DeliveryOrder::where('purchase_transport_id', $model->purchase_transport_id)
                        ->where('id', '!=', $model->id)
                        ->get();

                    $is_all_void = true;
                    foreach ($delivery_orders as $delivery_order) {
                        if (!in_array($delivery_order->status, ['void', 'reject'])) {
                            $is_all_void = false;
                        }
                    }

                    if ($is_all_void) {
                        $purchase_transport->status = 'approve';
                    }

                    $purchase_transport->save();
                }
            }

            // * is load quantity realization or load quantity updated and model status is done recreate stock mutation
            if ($model->getOriginal('done') && ($model->isDirty('load_quantity') or $model->isDirty('load_quantity_realization'))) {
                throw new \Throwable('Dilarang mengubah kuantias muat / realisasi kuantitas muat ketika sudah di close / done');
            }
        });

        static::updated(function ($model) {
            try {
                if ($model->type == 'delivery-order') {
                    // * UPDATE SALE ORDER SENDED VALUE
                    $so_trading_detail = $model->so_trading->so_trading_detail;
                    $sudah_dikirim = DB::table('delivery_orders')
                        ->where('type', 'delivery-order')
                        ->where('so_trading_id', $so_trading_detail->so_trading_id)
                        ->whereNotIn('status', ['reject', 'void'])
                        ->whereNull('deleted_at')
                        ->sum('load_quantity');

                    $so_trading_detail->sudah_dikirim = $sudah_dikirim;

                    if ($so_trading_detail->sudah_dikirim >= $so_trading_detail->jumlah) {
                        $so_trading_detail->status = 'done';
                        $so_trading_detail->save();

                        // * update status so trading
                        $model->so_trading->status = 'delivery_complete';
                        $model->so_trading->save();
                    } else {
                        if ($model->so_trading->status != 'partial-sended') {
                            $model->so_trading->status = 'partial-sended';
                            $model->so_trading->save();
                        }
                    }

                    $so_trading_detail->save();
                }

                if ($model->purchase_transport) {
                    $purchase_transport =  $model->purchase_transport;
                    $delivered_qty = $purchase_transport->delivery_orders
                        ->where('type', 'delivery-order')
                        ->filter(function ($item) {
                            return !in_array($item->status, ['void', 'reject']);
                        })
                        ->sum('load_quantity');

                    DB::table('purchase_transports')
                        ->where('id', $purchase_transport->id)
                        ->update(['delivered_qty' => $delivered_qty]);
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        });

        static::deleted(function ($model) {

            $sale_order = $model->so_trading;
            $sale_order_detail = $sale_order->so_trading_detail;
            $sudah_dikirim = DB::table('delivery_orders')
                ->where('type', 'delivery-order')
                ->where('so_trading_id', $sale_order_detail->id)
                ->whereNotIn('status', ['reject', 'void'])
                ->whereNull('deleted_at')
                ->sum('load_quantity');

            $sale_order_detail->sudah_dikirim = $sudah_dikirim;

            try {
                $sale_order_detail->save();

                if ($model->purchase_transport) {
                    $purchase_transport =  $model->purchase_transport;
                    $delivered_qty = $purchase_transport->delivery_orders
                        ->filter(function ($item) {
                            return !in_array($item->status, ['void', 'reject']);
                        })
                        ->sum('load_quantity');

                    DB::table('purchase_transports')
                        ->where('id', $purchase_transport->id)
                        ->update(['delivered_qty' => $delivered_qty]);
                }

                if ($model->delivery_order_id) {
                    $delivery_order = DeliveryOrder::find($model->delivery_order_id);
                    $delivery_order->calculateQtyUsed();
                }
            } catch (\Throwable $th) {
                throw $th;
            }
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
        $activity_logs = ActivityLog::where('subject_type', DeliveryOrder::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', DeliveryOrder::class)
            ->where('reference_id', $this->id)
            ->orderByDesc('created_at')
            ->get();

        return compact('status_logs', 'activity_logs');
    }

    /**
     * Is can edit this current data
     */
    public function getIsCanEditDataAttribute(): bool
    {
        return !$this->is_invoice_created && !$this->is_item_receiving_report_created;
    }

    public function sh_number()
    {
        return $this->belongsTo(ShNumber::class)->withTrashed();
    }

    public function so_trading()
    {
        return $this->belongsTo(SoTrading::class);
    }

    public function purchase_transport()
    {
        return $this->belongsTo(PurchaseTransport::class);
    }

    public function purchase_transport_detail()
    {
        return $this->belongsTo(PurchaseTransportDetail::class);
    }

    /**
     * Get the item_receiving_report that owns the DeliveryOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item_receiving_report(): BelongsTo
    {
        return $this->belongsTo(ItemReceivingReport::class);
    }

    /**
     * Get the delivery_order that owns the DeliveryOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function delivery_order(): BelongsTo
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    /**
     * Get the fleet that owns the DeliveryOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fleet(): BelongsTo
    {
        return $this->belongsTo(Fleet::class);
    }

    /**
     * Get the branch that owns the data.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the employee that owns the DeliveryOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function approved_by_user()
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    /**
     * Get the ware_house that owns the DeliveryOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ware_house(): BelongsTo
    {
        return $this->belongsTo(WareHouse::class)->withTrashed();
    }

    /**
     * Get the delivery_order_has_one associated with the DeliveryOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function delivery_order_has_one(): HasOne
    {
        return $this->hasOne(DeliveryOrder::class);
    }


    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function invoice_trading_detail()
    {
        return $this->hasOne(InvoiceTradingDetail::class);
    }

    /**
     * getCheckAvailableDateAttribute for generated journal
     *
     * @return bool
     */
    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->target_delivery);
    }

    public function calculateQtyUsed()
    {
        $qty_used = 0;
        $delivery_orders = DeliveryOrder::whereNotIn('status', ['void', 'reject', 'revert', 'pending'])
            ->where('delivery_order_id', $this->id)
            ->get();

        foreach ($delivery_orders as $delivery_order) {
            $qty_used += $delivery_order->load_quantity_realization;
        }

        $this->quantity_used = $qty_used;
        $this->save();
    }

    public function delivery_orders()
    {
        return $this->hasMany(DeliveryOrder::class, 'delivery_order_id');
    }
}
