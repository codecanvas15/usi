<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashAdvanceReturnInvoiceDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cash_advance_return_invoice_id',
        'item_receiving_report_id',
        'amount',
        'amount_convert',
        'outstanding',
    ];

    /**
     * Get the cash advance return invoice that owns the CashAdvanceReturnInvoiceDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cash_advance_return_invoice(): BelongsTo
    {
        return $this->belongsTo(CashAdvanceReturnInvoice::class, 'cash_advance_return_invoice_id', 'id');
    }

    /**
     * Get the item receiving report that owns the CashAdvanceReturnInvoiceDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item_receiving_report(): BelongsTo
    {
        return $this->belongsTo(ItemReceivingReport::class, 'item_receiving_report_id', 'id');
    }
}
