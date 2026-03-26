<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PurchaseOrderAdditionalItems extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'po_trading_id',
        'item_id',
        'jumlah',
        'harga',
        'total',
        'sub_total',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'harga' => 'float',
        'jumlah' => 'float',
        'total' => 'float',
        'sub_total' => 'float',
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
        $validate = [
            '' => ''
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

    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    /**
     * Get all of the purchase_order_additional_taxes for the PurchaseOrderAdditionalItems
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchase_order_additional_taxes(): HasMany
    {
        return $this->hasMany(PurchaseOrderAdditionalTaxs::class, 'po_additional_id');
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function item_receiving_po_trading_additionals()
    {
        return $this->hasMany(ItemReceivingPoTradingAdditional::class, 'purchase_order_additional_items_id');
    }
}
