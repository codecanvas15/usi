<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceDownPaymentTax extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'invoice_down_payment_id',
        'tax_id',
        'value',
        'amount',
    ];

    public function invoice_down_payment()
    {
        return $this->belongsTo(InvoiceDownPayment::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class)->withTrashed();
    }
}
