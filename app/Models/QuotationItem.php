<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'item_id',
        'item_type',
        'type',
        'price',
        'quantity',
        'sub_total',
        'sub_total_after_tax',
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

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id')->withTrashed();
    }


    public function price()
    {
        return $this->belongsTo(Price::class)->withTrashed();
    }

    public function itemTax()
    {
        return $this->hasMany(QuotationItemTax::class, 'quotation_item_id', 'id');
    }
}
