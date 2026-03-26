<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransferDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_transfer_id',
        'item_id',
        'price_id',
        'stock',
        'qty',
        'receiving_qty',
    ];

    public function stockTransfer()
    {
        return $this->belongsTo(StockTransfer::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function price()
    {
        return $this->belongsTo(Price::class);
    }
}
