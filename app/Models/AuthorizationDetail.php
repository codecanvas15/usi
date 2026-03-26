<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuthorizationDetail extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'authorization_id',
        'user_id',
        'level',
        'status',
        'note',
        'revert_status',
        'void_status',
    ];

    public function authorization()
    {
        return $this->belongsTo(Authorization::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
