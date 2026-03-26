<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClosingPeriodCurrency extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'closing_period_id',
        'currency_id',
        'exchange_rate'
    ];

    /**
     * Get the closing period that owns the ClosingPeriodCurrency
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function closingPeriod(): BelongsTo
    {
        return $this->belongsTo(ClosingPeriod::class, 'closing_period_id', 'id');
    }

    /**
     * Get the currency that owns the ClosingPeriodCurrency
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }
}
