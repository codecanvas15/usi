<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInvoiceDownPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_invoice_id',
        'cash_advance_payment_id',
    ];

    public function supplier_invoice()
    {
        return $this->belongsTo(SupplierInvoice::class);
    }

    public function cash_advance_payment()
    {
        return $this->belongsTo(CashAdvancePayment::class);
    }
}
