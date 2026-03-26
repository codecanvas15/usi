<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceivablesPaymentVendorLpb extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'receivables_payment_vendor_id',
        'item_receiving_report_id',
        'outstanding',
        'amount',
    ];


    public function receivables_payment_vendor()
    {
        return $this->belongsTo(ReceivablesPaymentVendor::class);
    }

    public function item_receiving_report()
    {
        return $this->belongsTo(ItemReceivingReport::class);
    }
}
