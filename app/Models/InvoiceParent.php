<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceParent extends Model
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
        'customer_id',
        'currency_id',
        'exchange_rate',
        'date',
        'due_date',
        'model_reference',
        'reference_id',
        'type',
        'code',
        'total',
        'status',
        'payment_status',
    ];

    protected $appends = [
        'paid_amount',
        'outstanding_amount',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function reference_model()
    {
        return $this->belongsTo($this->model_reference, 'reference_id');
    }

    public function down_payment_invoices()
    {
        return $this->hasMany(DownPaymentInvoice::class);
    }

    public function getOutstandingAmountAttribute()
    {
        $total = InvoicePayment::where('invoice_model', $this->model_reference)
            ->where('invoice_id', $this->reference_id)
            ->sum('amount_to_receive');

        $paid = InvoicePayment::where('invoice_model', $this->model_reference)
            ->where('invoice_id', $this->reference_id)
            ->sum('receive_amount');

        return $total - $paid;
    }

    public function getPaidAmountAttribute()
    {
        $paid = InvoicePayment::where('invoice_model', $this->model_reference)
            ->where('invoice_id', $this->reference_id)
            ->sum('receive_amount');

        return $paid;
    }
}
