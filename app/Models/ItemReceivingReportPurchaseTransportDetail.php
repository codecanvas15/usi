<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use stdClass;

class ItemReceivingReportPurchaseTransportDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_receiving_report_purchase_transport_id',
        'delivery_order_id',
        'sended',
        'received',
        'sub_total',
        'tax_total',
        'total',
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
     * Get the delivery_order that owns the ItemReceivingReportPurchaseTransportDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function delivery_order(): BelongsTo
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function get_losses()
    {
        $losses = 0;
        $losses_percentage = 0;
        if ($this->delivery_order) {
            if ($this->delivery_order->load_quantity_realization != 0 && $this->delivery_order->unload_quantity_realization) {
                $losses = $this->delivery_order->load_quantity_realization - $this->delivery_order->unload_quantity_realization;
                $losses_percentage = ($losses / $this->delivery_order->load_quantity_realization) * 100;
            }
        }
        $data = new stdClass;
        $data->losses = $losses;
        $data->losses_percentage = $losses_percentage;

        return $data;
    }
}
