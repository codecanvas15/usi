<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundSubmissionCashAdvance extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'fund_submission_id',
        'coa_id',
        'purchase_id',
        'note',
        'type',
        'debit',
        'credit',
    ];

    protected $append = ['local_debit', 'local_credit'];

    public function fund_submission()
    {
        return $this->belongsTo(FundSubmission::class);
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function getLocalDebitAttribute()
    {
        return $this->debit * $this->fund_submission->exchange_rate;
    }

    public function getLocalCreditAttribute()
    {
        return $this->credit * $this->fund_submission->exchange_rate;
    }
}
