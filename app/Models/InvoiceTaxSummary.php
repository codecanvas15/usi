<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceTaxSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_class',
        'model_id',
        'tax_id',
        'tax_value',
        'tax_amount'
    ];

    public function reference()
    {
        return $this->belongsTo($this->model_class, 'model_id');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id')->withTrashed();
    }
}
