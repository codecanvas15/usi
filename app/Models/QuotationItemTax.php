<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItemTax extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_item_id',
        'tax_id',
        'value',
        'total'
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

    public function tax()
    {
        return $this->belongsTo(Tax::class)->withTrashed();
    }
}
