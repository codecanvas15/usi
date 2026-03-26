<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LockStock extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_request_detail_id',
        'item_id',
        'quantity',
        'quantity_complete',
        'status'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (is_null($model->status)) {
                $model->status = $model->purchase_request_detail->status ?? 'pending';
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('quantity_complete')) {
                $model->status = $model->quantity_complete >= $model->quantity ? 'complete' : 'partial';
            }
        });
    }

    public function purchase_request_detail()
    {
        return  $this->belongsTo(PurchaseRequestDetail::class);
    }

    public function item()
    {
        return  $this->belongsTo(Item::class);
    }
}
