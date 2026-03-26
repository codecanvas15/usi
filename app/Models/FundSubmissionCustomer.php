<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundSubmissionCustomer extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function fund_submission()
    {
        return $this->belongsTo(FundSubmission::class);
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    public function invoice_parent()
    {
        return $this->belongsTo(InvoiceParent::class);
    }
}
