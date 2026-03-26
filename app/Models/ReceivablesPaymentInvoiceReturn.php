<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceivablesPaymentInvoiceReturn extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'receivables_payment_id',
        'invoice_return_id',
        'exchange_rate',
        'outstanding_amount',
        'amount',
        'amount_foreign',
        'exchange_rate_gap',
        'exchange_rate_gap_idr',
        'exchange_rate_gap_foreign',
    ];

    public function receivables_payment()
    {
        return $this->belongsTo(ReceivablesPayment::class);
    }

    public function invoice_return()
    {
        return $this->belongsTo(InvoiceReturn::class);
    }
}
