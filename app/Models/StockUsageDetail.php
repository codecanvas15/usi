<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockUsageDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_receiving_report_detail_id',
        'stock_usage_id',
        'coa_detail_id',
        'item_id',
        'unit_id',
        'price_id',
        'price_unit',
        'quantity',
        'necessity',
        'stock',
    ];

    /**
     * Get the stock_usage that owns the StockUsageDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stock_usage(): BelongsTo
    {
        return $this->belongsTo(StockUsage::class)->withTrashed();
    }

    /**
     * Get the item that owns the StockUsageDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    /**
     * Get the unit that owns the StockUsageDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class)->withTrashed();
    }

    /**
     * Get the price that owns the StockUsageDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function price(): BelongsTo
    {
        return $this->belongsTo(Price::class)->withTrashed();
    }

    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'coa_detail_id')->withTrashed();
    }
}
