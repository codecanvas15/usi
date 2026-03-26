<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MassLeaveDetail extends Model
{
    protected $fillable = [
        'mass_leave_id',
        'leave_id',
        'employee_id',
    ];

    public function mass_leave()
    {
        return $this->belongsTo(MassLeave::class);
    }

    public function leave()
    {
        return $this->belongsTo(Leave::class);
    }
}
