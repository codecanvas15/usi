<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterLoyaltys extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
