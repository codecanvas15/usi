<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemReceivingReportPurchaseTransport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_receiving_report_id',
        'price_id',
        'item_id',
        'sended',
        'received',
        'price',
        'sub_total',
        'tax_total',
        'total',
        'loss_tolerance',
        'tax_option',
        'sub_total_by_po',
        'lost_discount',
    ];
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
     * Get the item that owns the ItemReceivingReportPurchaseTransport
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the item_receiving_report that owns the ItemReceivingReportPurchaseTransport
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item_receiving_report(): BelongsTo
    {
        return $this->belongsTo(ItemReceivingReport::class);
    }

    /**
     * Get all of the item_receiving_report_transport_details for the ItemReceivingReportPurchaseTransport
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function item_receiving_report_purchase_transport_details(): HasMany
    {
        return $this->hasMany(ItemReceivingReportPurchaseTransportDetail::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
