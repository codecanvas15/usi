<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundSubmissionSupplierOther extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fund_submission_id',
        'coa_id',
        'note',
        'debit',
        'credit',
    ];

    public function coa()
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    public function fund_submission()
    {
        return $this->belongsTo(FundSubmission::class);
    }
}
