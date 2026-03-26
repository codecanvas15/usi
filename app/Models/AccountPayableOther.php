<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPayableOther extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_payable_id',
        'coa_id',
        'type',
        'note',
        'debit',
        'credit',
    ];

    public function coa()
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    public function account_payable()
    {
        return $this->belongsTo(AccountPayable::class);
    }
}
