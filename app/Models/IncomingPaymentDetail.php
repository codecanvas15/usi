<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomingPaymentDetail extends Model
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
        'credit_local',
    ];

    public function coa()
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    public function incoming_payment()
    {
        return $this->belongsTo(IncomingPayment::class);
    }

    public function purchase_return()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    public function getDebitLocalAttribute()
    {
        return $this->debit * $this->incoming_payment->exchange_rate;
    }

    public function getCreditLocalAttribute()
    {
        return $this->credit * $this->incoming_payment->exchange_rate;
    }
}
