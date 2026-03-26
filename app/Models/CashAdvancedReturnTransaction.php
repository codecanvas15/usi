<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashAdvancedReturnTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'coa_id',
        'cash_advanced_return_id',
        'credit',
        'debit',
        'description',
    ];

    /**
     * Get the cash advanced return that owns the CashAdvancedReturnTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cash_advanced_return(): BelongsTo
    {
        return $this->belongsTo(CashAdvancedReturn::class);
    }

    /**
     * Get the coa that owns the CashAdvancedReturnTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }
}
