<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierInvoiceParent extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id',
        'vendor_id',
        'currency_id',
        'exchange_rate',
        'date',
        'due_date',
        'model_reference',
        'reference_id',
        'type',
        'code',
        'reference',
        'total',
        'status',
        'payment_status',
    ];

    protected $appends = [
        'paid_amount',
        'outstanding_amount',
        'projects',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function reference_model()
    {
        return $this->belongsTo($this->model_reference, 'reference_id');
    }

    public function getOutstandingAmountAttribute()
    {
        $total = SupplierInvoicePayment::where('supplier_invoice_model', $this->model_reference)
            ->where('supplier_invoice_id', $this->reference_id)
            ->sum('amount_to_pay');

        $paid = SupplierInvoicePayment::where('supplier_invoice_model', $this->model_reference)
            ->where('supplier_invoice_id', $this->reference_id)
            ->sum('pay_amount');

        return $total - $paid;
    }

    public function getPaidAmountAttribute()
    {
        $paid = SupplierInvoicePayment::where('supplier_invoice_model', $this->model_reference)
            ->where('supplier_invoice_id', $this->reference_id)
            ->sum('pay_amount');

        return $paid;
    }

    public function getProjectsAttribute()
    {
        $projects = [];
        if ($this->type == "general") {
            $si = $this->model_reference::find($this->reference_id);
            if ($si) {
                if ($si->project ?? null) {
                    array_push($projects, $si->project->name);
                }
            }
        }
        if ($this->type == "trading") {
            $si = $this->model_reference::find($this->reference_id);
            foreach ($si->detail as $key => $detail) {
                if ($detail->item_receiving_report->project ?? null) {
                    array_push($projects, $detail->item_receiving_report->project->name);
                }
            }
        }

        return implode(',', $projects);
    }

    public function fund_submission_supplier_details()
    {
        return $this->hasMany(FundSubmissionSupplierDetail::class);
    }

    public function cash_advanced_return_invoices()
    {
        return $this->morphMany(CashAdvancedReturnInvoice::class, 'cash_advanced_return_invoiceable', 'reference_model', 'reference_id');
    }
}
