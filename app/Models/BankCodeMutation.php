<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankCodeMutation extends Model
{
    use HasFactory;

    protected $fillable = [
        'coa_id',
        'date',
        'ref_model',
        'ref_id',
        'type',
        'code',
        'is_generate',
    ];

    public function coa()
    {
        return $this->belongsTo(Coa::class);
    }
}
