<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemReceivingPoTradingAdditional extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_receiving_report_id',
        'purchase_order_additional_items_id',
        'outstanding_qty',
        'receive_qty',
        'subtotal',
        'tax_total',
        'total',
    ];

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

    public function item_receiving_report()
    {
        return $this->belongsTo(ItemReceivingReport::class);
    }

    public function purchase_order_additional_items()
    {
        return $this->belongsTo(PurchaseOrderAdditionalItems::class, 'purchase_order_additional_items_id');
    }
}
