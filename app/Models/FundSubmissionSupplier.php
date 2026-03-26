<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundSubmissionSupplier extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'fund_submission_id',
        'coa_id',
        'note',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    public function fund_submission()
    {
        return $this->belongsTo(FundSubmission::class);
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }
}
