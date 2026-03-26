<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashBondReturnOther extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cash_bond_return',
        'coa_id',
        'amount',
        'description'
    ];

    /**
     * Get the cash_bond_return that owns the CashBondReturnOther
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cash_bond_return(): BelongsTo
    {
        return $this->belongsTo(CashBondReturn::class)->withTrashed();
    }

    /**
     * Get the coa that owns the CashBondReturnOther
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }
}
