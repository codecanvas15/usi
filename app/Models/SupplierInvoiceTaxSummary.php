<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInvoiceTaxSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_invoice_id',
        'tax_id',
        'sub_total',
        'tax_value',
        'tax_amount'
    ];

    public function supplier_invoice()
    {
        return $this->belongsTo(SupplierInvoice::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id')->withTrashed();
    }
}
