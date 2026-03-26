<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderGeneralDetailItemTax extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_order_general_detail_item_id',
        'tax_id',
        'value',
        'total',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'float',
        'total' => 'float',
    ];

    /**
     * Get the purchase order general detail item that owns the PurchaseOrderGeneralDetailItemTax
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase_order_general_detail_item(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderGeneralDetailItem::class);
    }

    /**
     * Get the tax that owns the PurchaseOrderGeneralDetailItemTax
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class)->withTrashed();
    }
}
