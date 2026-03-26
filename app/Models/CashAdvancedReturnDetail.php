<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashAdvancedReturnDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cash_advanced_return_id',
        'coa_id',
        'currency_id',
        'reference_id',
        'reference_model',
        'date',
        'transaction_code',
        'type',
        'exchange_rate',
        'amount',
        'amount_to_return',
        'outstanding_amount',
        'balance',
    ];

    /**
     * Get the cash advanced return that owns the CashAdvancedReturnDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cash_advanced_return(): BelongsTo
    {
        return $this->belongsTo(CashAdvancedReturn::class);
    }

    /**
     * Get the coa that owns the CashAdvancedReturnDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    /**
     * Get the currency that owns the CashAdvancedReturnDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    /**
     * Get the reference that owns the CashAdvancedReturnDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo($this->reference_model, 'reference_id');
    }

    public function cash_advanced_return_detailable()
    {
        return $this->morphTo();
    }
}
