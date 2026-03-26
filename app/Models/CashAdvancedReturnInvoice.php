<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashAdvancedReturnInvoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cash_advanced_return_id',
        'currency_id',
        'reference_id',
        'reference_model',
        'date',
        'transaction_code',
        'exchange_rate',
        'outstanding_amount',
        'amount_total',
        'amount_to_paid_or_return',
        'amount_to_paid_or_return_convert',
        'exchange_rate_gap',
        'description',
    ];

    /**
     * Get the cash advanced return that owns the CashAdvancedReturnInvoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cash_advanced_return(): BelongsTo
    {
        return $this->belongsTo(CashAdvancedReturn::class);
    }

    /**
     * Get the currency that owns the CashAdvancedReturnInvoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    /**
     * Get the reference that owns the CashAdvancedReturnInvoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo($this->reference_model, 'reference_id');
    }

    /**
     * Get all of the cash_advanced_return_invoice_details for the CashAdvancedReturnInvoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cash_advanced_return_invoice_details(): HasMany
    {
        return $this->hasMany(CashAdvanceReturnInvoiceDetail::class, 'cash_advance_return_invoice_id', 'id');
    }

    public function cash_advanced_return_invoiceable()
    {
        return $this->morphTo();
    }
}
