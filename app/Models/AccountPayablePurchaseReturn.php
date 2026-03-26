<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPayablePurchaseReturn extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'account_payable_id',
        'purchase_return_id',
        'exchange_rate',
        'outstanding_amount',
        'amount',
        'amount_foreign',
        'exchange_rate_gap',
        'exchange_rate_gap_idr',
        'exchange_rate_gap_foreign',
    ];

    public function account_payable()
    {
        return $this->belongsTo(AccountPayable::class);
    }

    public function purchase_return()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    // booted function
    public static function booted()
    {
        static::creating(function ($model) {
        });
    }
}
