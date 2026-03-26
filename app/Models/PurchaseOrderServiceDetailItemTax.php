<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderServiceDetailItemTax extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_order_service_detail_item_id',
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
     * Get the purchase order service detail item that owns the PurchaseOrderServiceDetailItemTax
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase_order_service_detail_item(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderServiceDetailItem::class);
    }

    /**
     * Get the tax that owns the PurchaseOrderServiceDetailItemTax
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class)->withTrashed();
    }
}
