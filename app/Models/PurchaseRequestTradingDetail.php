<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequestTradingDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'purchase_request_trading_id',
        'item_id',
        'qty',
        'ordered_qty',
    ];


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

    public function purchase_request_trading()
    {
        return $this->belongsTo(PurchaseRequestTrading::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
