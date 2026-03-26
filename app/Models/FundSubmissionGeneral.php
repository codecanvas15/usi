<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundSubmissionGeneral extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'fund_submission_id',
        'coa_id',
        'note',
        'debit',
    ];

    protected $append = ['local_debit'];

    public function fund_submission()
    {
        return $this->belongsTo(FundSubmission::class);
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    public function getLocalDebitAttribute()
    {
        return $this->debit * $this->fund_submission->exchange_rate;
    }

    public function invoice_return()
    {
        return $this->belongsTo(InvoiceReturn::class)->withTrashed();
    }
}
