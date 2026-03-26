<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceReturnHistory extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_return_id',
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

    public function invoice_return()
    {
        return $this->belongsTo(InvoiceReturn::class, 'invoice_return_id');
    }
}
