<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundSubmissionPurchaseReturn extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'fund_submission_id',
        'purchase_return_id',
        'exchange_rate',
        'outstanding_amount',
        'amount',
        'amount_foreign',
        'exchange_rate_gap',
        'exchange_rate_gap_idr',
        'exchange_rate_gap_foreign',
    ];

    public function fund_submission()
    {
        return $this->belongsTo(FundSubmission::class);
    }

    public function purchase_return()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }
}
