<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class DeliveryOrderGeneralDetail extends Model
{
    use HasFactory;
    use \Spatie\Activitylog\Traits\LogsActivity;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'delivery_order_general_id',
        'sale_order_general_detail_id',
        'item_id',
        'unit_id',
        'quantity',
        'quantity_received',
        'quantity_returned',
        'quantity_lost',
        'quantity_damage',
        'description',
        'is_invoice_created',
        'hpp',
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
     * Get the delivery order general that owns the DeliveryOrderGeneralDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function delivery_order_general(): BelongsTo
    {
        return $this->belongsTo(DeliveryOrderGeneral::class);
    }

    /**
     * Get the sale order general detail that owns the DeliveryOrderGeneralDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale_order_general_detail(): BelongsTo
    {
        return $this->belongsTo(SaleOrderGeneralDetail::class);
    }

    /**
     * Get the item that owns the DeliveryOrderGeneralDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    /**
     * Get the unit that owns the DeliveryOrderGeneralDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function invoice_general_details()
    {
        return $this->hasMany(InvoiceGeneralDetail::class, 'delivery_order_general_detail_id');
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
