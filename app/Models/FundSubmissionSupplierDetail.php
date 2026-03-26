<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundSubmissionSupplierDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'fund_submission_id',
        'model',
        'reference_id',
        'exchange_rate',
        'outstanding_amount',
        'amount',
        'note',
    ];

    public function fund_submission()
    {
        return $this->belongsTo(FundSubmission::class);
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    public function supplier_invoice_parent()
    {
        return $this->belongsTo(SupplierInvoiceParent::class);
    }

    public function fund_submission_supplier_lpbs()
    {
        return $this->hasMany(FundSubmissionSupplierLpb::class);
    }

    public function getOriginalOutstandingAttribute()
    {
        $supplier_invoice_parent = $this->supplier_invoice_parent;
        $payment = SupplierInvoicePayment::where('supplier_invoice_model', $supplier_invoice_parent->model_reference)
            ->where('supplier_invoice_id', $supplier_invoice_parent->reference_id)
            ->where('model', '!=', AccountPayableDetail::class)
            ->get()
            ->filter(function ($item) {
                return Carbon::parse($item->created_at)->lessThan(Carbon::parse($this->fund_submission->created_at));
            })
            ->sum('pay_amount');

        $payment_before = FundSubmissionSupplierDetail::where('supplier_invoice_parent_id', $this->supplier_invoice_parent_id)
            ->join('fund_submissions', function ($j) {
                $j->on('fund_submissions.id', 'fund_submission_supplier_details.fund_submission_id')
                    ->whereNull('fund_submissions.deleted_at')
                    ->whereIn('fund_submissions.status', ['pending', 'revert', 'approve']);
            })
            ->where('fund_submission_id', '<', $this->fund_submission_id)
            ->when($this->outstanding_amount < 0, function ($query) {
                $query->where('total_foreign', '<', 0);
            })
            ->selectRaw('supplier_invoice_parent_id, total_foreign')
            ->get();

        $payment_before = $payment_before->where('is_used', 0)->sum('total_foreign');
        $outstanding = $supplier_invoice_parent->total - $payment;

        return $outstanding - $payment_before;
    }

    public function getLpbReferenceAttribute()
    {
        $item_receiving_reports = ItemReceivingReport::whereIn('id', $this->fund_submission_supplier_lpbs->pluck('item_receiving_report_id')->toArray())
            ->pluck('kode')->toArray();

        return $item_receiving_reports;
    }
}
