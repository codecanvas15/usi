<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpbTaxSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_receiving_report_id',
        'tax_id',
        'sub_total',
        'tax_value',
        'tax_amount'
    ];

    public function item_receiving_report()
    {
        return $this->belongsTo(ItemReceivingReport::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id')->withTrashed();
    }
}
