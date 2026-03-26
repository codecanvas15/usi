<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPayableDetailLpb extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'account_payable_detail_id',
        'item_receiving_report_id',
        'outstanding',
        'amount',
    ];


    public function account_payable_detail()
    {
        return $this->belongsTo(AccountPayableDetail::class);
    }

    public function item_receiving_report()
    {
        return $this->belongsTo(ItemReceivingReport::class);
    }
}
