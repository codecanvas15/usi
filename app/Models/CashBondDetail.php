<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashBondDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cash_bond_id',
        'coa_id',
        'type',
        'credit',
        'debit',
        'note',
    ];

    /**
     * Get the cash bond that owns the CashBondDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cash_bond(): BelongsTo
    {
        return $this->belongsTo(CashBond::class);
    }

    /**
     * Get the coa that owns the CashBondDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
