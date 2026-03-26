<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\LogOptions;

class DeliveryOrderGeneral extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \Spatie\Activitylog\Traits\LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id',
        'sale_order_general_id',
        'customer_id',
        'vendor_id',
        'code',
        'external_code',
        'date',
        'date_send',
        'date_receive',
        'target_delivery',
        'supply',
        'drop',
        'description',
        'status',
        'is_invoice_created',
        'is_old_data',
        'ware_house_id',
        'created_by',
        'approved_by'
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
    public static function rules($method, $id = null)
    {
        $rules = [];

        return $rules;
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        self::creating(function ($model) {
            if (is_null($model->date)) {
                $model->date = Carbon::now()->format('Y-m-d');
            }

            if (is_null($model->status)) {
                $model->status = 'pending';
            }

            if (is_null($model->branch_id)) {
                $model->branch_id = auth()->user()?->branch_id;
            }

            if (is_null($model->code)) {
                $last_code = self::orderByDesc('id')
                    ->withTrashed()
                    ->whereMonth('date_send', Carbon::parse($model->date_send))
                    ->whereYear('date_send', Carbon::parse($model->date_send))
                    ->first();

                $model->code = generate_code_transaction('DOG', $last_code->code ?? null, date: $model->date_send);
            }

            if (!checkAvailableDate($model->date)) {
                throw new \Exception('Tanggal tidak tersedia / sudah tutup buku');
            }

            $model->is_old_data = false;
        });

        self::created(function ($model) {
            // ? update sale order general detail qty received
            Log::error('Message');

            $model->delivery_order_general_details->each(function ($detail) use ($model) {
                try {
                    Log::error('Message');
                    $detail->sale_order_general_detail->update([
                        'sended' => $detail->sale_order_general_detail->sended + $detail->quantity,
                    ]);
                } catch (\Throwable $th) {
                    throw $th;
                }
            });
        });

        self::updating(function ($model) {
            // * Status updated ###########################
            if ($model->status != $model->getOriginal('status')) {
                // * validate status when done
                if ($model->status == 'done') {
                    if (is_null($model->date_receive)) {
                        throw new \Exception("Date Received cannot be empty");
                    }

                    // * validate quantity received
                    foreach ($model->delivery_order_general_details as $key => $value) {
                        if (is_null($value->quantity_received) or $value->quantity_received <= 0) {
                            throw new \Exception("Quantity Received cannot be 0.");
                        }
                    }

                    // * validate warehouse
                    if (is_null($model->ware_house_id)) {
                        throw new \Exception("Warehouse cannot be empty.");
                    }
                }
            }
            // * end Status updated ###########################
        });

        self::updated(function ($model) {
            // * Status updated ###########################

            if ($model->status != $model->getOriginal('status')) {
                // ! Approve ================================================
                if ($model->status == 'approve') {
                    $item_stocks = DB::table('stock_mutations')
                        ->whereNull('deleted_at')
                        ->whereIn('item_id', $model->delivery_order_general_details->pluck('item_id')->toArray())
                        ->where('ware_house_id', $model->ware_house_id)
                        ->selectRaw(
                            'COALESCE(SUM(stock_mutations.in), 0) - COALESCE(SUM(stock_mutations.out), 0) AS stock, item_id'
                        )
                        ->groupBy('item_id')
                        ->get();


                    // ? create stock mutation out and update sale order general detail qty received
                    $model->delivery_order_general_details->each(function ($detail) use ($model, $item_stocks) {
                        // ? create price
                        $price = Price::create([
                            'item_id' => $detail->item_id,
                            'harga_beli' => $detail->sale_order_general_detail?->price ?? 0,
                            'harga_jual' => $detail->sale_order_general_detail?->price ?? 0,
                        ]);

                        if ($detail->item->item_category->item_type->nama == "purchase item") {
                            $item_stock = $item_stocks->where('item_id', $detail->item_id)->first();

                            if (!$item_stock or ($item_stock->stock ?? 0) < $detail->quantity) {
                                $stock = $item_stock ? $item_stock->stock : 0;
                                throw new \Exception("Stock item {$detail->item->nama} tidak cukup untuk melakukan pengiriman. Stock: {$stock}, Quantity: {$detail->quantity}");
                            }

                            // ? create stock mutation
                            $stock_mutation = new StockMutation();
                            $stock_mutation->loadModel([
                                'ware_house_id' => $model->ware_house_id,
                                'item_id' => $detail->item_id,
                                'price_id' => $price->id,
                                'document_model' => DeliveryOrderGeneralDetail::class,
                                'document_id' => $detail->id,
                                'date' => $model->date_send,
                                'document_code' => $model->code,
                                'type' => 'delivery order general',
                                'out' => $detail->quantity,
                                'note' => "Delivery Order General",
                                'price_unit' => $detail->hpp,
                                'subtotal' => $detail->hpp * $detail->quantity,
                            ]);

                            // ? save
                            try {
                                $stock_mutation->save();
                            } catch (\Throwable $th) {
                                throw $th;
                            }
                        }

                        if ($model->is_old_data) {
                            try {
                                // ? update sale order general detail qty received
                                $detail->sale_order_general_detail->update([
                                    'sended' => $detail->sale_order_general_detail->sended + $detail->quantity,
                                ]);
                            } catch (\Throwable $th) {
                                throw $th;
                            }
                        }
                    });

                    // * CREATE journal
                    $journal = new \App\Http\Helpers\JournalHelpers('delivery-order-general', $model->id);
                    $journal->generate();
                }
                // ! end Approve ================================================

                // ! reject void cancel ==================================
                if (in_array($model->status, ['reject', 'void', 'cancel'])) {
                    // ? update sale order general detail qty received
                    $model->delivery_order_general_details->each(function ($detail) use ($model) {
                        try {
                            // !! delete journal
                            Journal::where('reference_model', DeliveryOrderGeneral::class)
                                ->where('reference_id', $model->id)
                                ->delete();

                            // !! delete stock mutation
                            StockMutation::where('document_model', DeliveryOrderGeneralDetail::class)
                                ->where('document_id', $detail->id)
                                ->delete();

                            // ? update sale order general detail qty received
                            $update_so_detail = SaleOrderGeneralDetail::findOrfail($detail->sale_order_general_detail_id);
                            $update_so_detail->sended = $update_so_detail->sended - $detail->quantity;
                            $update_so_detail->save();
                        } catch (\Throwable $th) {
                            throw $th;
                        }
                    });
                }
                // ! reject void cancel ==================================
            }
            // * End Status updated ###########################
        });
    }

    /**
     * set attributes model
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
        $activity_logs = ActivityLog::where('subject_type', self::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', self::class)
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
        return checkAvailableDate($this->date);
    }

    /**
     * Get the branch that owns the DeliveryOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the sale_order_general that owns the DeliveryOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale_order_general(): BelongsTo
    {
        return $this->belongsTo(SaleOrderGeneral::class);
    }

    /**
     * Get the customer that owns the DeliveryOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    /**
     * Get the vendor that owns the DeliveryOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the ware_house that owns the DeliveryOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ware_house(): BelongsTo
    {
        return $this->belongsTo(WareHouse::class)->withTrashed();
    }

    /**
     * Get all of the deliveryOrderGeneralDetails for the DeliveryOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function delivery_order_general_details(): HasMany
    {
        return $this->hasMany(DeliveryOrderGeneralDetail::class, 'delivery_order_general_id', 'id');
    }

    public function invoice_general(): HasOne
    {
        return $this->hasOne(InvoiceGeneral::class);
    }
    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function approved_by_user()
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
