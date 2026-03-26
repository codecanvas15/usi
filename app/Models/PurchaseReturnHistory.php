<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseReturnHistory extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_return_id',
        'date',
        'reference_model',
        'reference_id',
        'reference_parent_model',
        'reference_parent_id',
        'amount',
        'status',
    ];

    public function reference_model()
    {
        return $this->belongsTo($this->reference_model::class, 'reference_id');
    }

    public function reference_parent_model()
    {
        return $this->belongsTo($this->reference_parent_model::class, 'reference_parent_id');
    }

    public function purchase_return()
    {
        return $this->belongsTo(PurchaseReturn::class, 'purchase_return_id');
    }
}
