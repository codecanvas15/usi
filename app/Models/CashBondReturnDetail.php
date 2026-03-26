<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashBondReturnDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_bond_return_id',
        'cash_bond_id',
        'currency_id',
        'coa_id',
        'date',
        'transaction_code',
        'type',
        'exchange_rate',
        'amount',
        'amount_to_return',
        'outstanding_amount',
        'balance',
        'note',
    ];

    /**
     * Get the cash_bond_return that owns the CashBondReturnDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cash_bond_return(): BelongsTo
    {
        return $this->belongsTo(CashBondReturn::class)->withTrashed();
    }

    /**
     * Get the cash_bond that owns the CashBondReturnDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cash_bond(): BelongsTo
    {
        return $this->belongsTo(CashBond::class)->withTrashed();
    }

    /**
     * Get the currency that owns the CashBondReturnDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    /**
     * Get the coa that owns the CashBondReturnDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }
}
