<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'path',
        'status',
        'type',
        'done_at',
        'downloaded_at',
    ];

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
