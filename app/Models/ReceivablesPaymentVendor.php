<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceivablesPaymentVendor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'receivables_payment_id',
        'supplier_invoice_parent_id',
        'coa_id',
        'exchange_rate',
        'outstanding_amount',
        'amount',
        'amount_gap',
        'total',
        'amount_foreign',
        'amount_gap_foreign',
        'is_clearing',
        'clearing_note',
        'total_foreign',
        'exchange_rate_gap',
        'exchange_rate_gap_idr',
        'exchange_rate_gap_foreign',
        'exchange_rate_gap_note',
        'note',
    ];

    public function receivables_payment()
    {
        return $this->belongsTo(ReceivablesPayment::class);
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    public function supplier_invoice_parent()
    {
        return $this->belongsTo(SupplierInvoiceParent::class);
    }

    public function receivables_payment_vendor_lpbs()
    {
        return $this->hasMany(ReceivablesPaymentVendorLpb::class);
    }

    public function getLpbReferenceAttribute()
    {
        $item_receiving_reports = ItemReceivingReport::whereIn('id', $this->receivables_payment_vendor_lpbs->pluck('item_receiving_report_id')->toArray())
            ->pluck('kode')->toArray();

        return $item_receiving_reports;
    }
}
