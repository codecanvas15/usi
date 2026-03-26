<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DownPaymentInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_parent_id',
        'invoice_down_payment_id',
    ];

    public function invoice_parent()
    {
        return $this->belongsTo(InvoiceParent::class);
    }

    public function invoice_down_payment()
    {
        return $this->belongsTo(InvoiceDownPayment::class);
    }
}
