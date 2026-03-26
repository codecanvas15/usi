<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundSubmissionSupplierLpb extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'fund_submisison_supplier_lpbs';

    protected $fillable = [
        'fund_submission_supplier_detail_id',
        'item_receiving_report_id',
        'outstanding',
        'amount',
    ];

    protected $appends = [
        'is_has_cash_advance',
    ];

    public function fund_submission_supplier_detail()
    {
        return $this->belongsTo(FundSubmissionSupplierDetail::class);
    }

    public function item_receiving_report()
    {
        return $this->belongsTo(ItemReceivingReport::class);
    }

    function getIsHasCashAdvanceAttribute()
    {
        $cash_advance = $this->item_receiving_report->reference->purchase->cash_advance_payments ?? [];
        if (count($cash_advance) > 0) {
            return  true;
        } else {
            return  false;
        }
    }
}
