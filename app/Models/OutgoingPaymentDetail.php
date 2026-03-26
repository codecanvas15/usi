<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutgoingPaymentDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'coa_id',
        'amount',
        'note',
    ];

    protected $append = [
        'debit_local',
    ];

    public function coa()
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    public function outgoing_payment()
    {
        return $this->belongsTo(OutgoingPayment::class);
    }

    public function getDebitLocalAttribute()
    {
        return $this->debit * $this->outgoing_payment->exchange_rate;
    }

    public function invoice_return()
    {
        return $this->belongsTo(InvoiceReturn::class)->withTrashed();
    }
}
