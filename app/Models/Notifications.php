<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $table = 'notifications';
    protected $fillable = ['id', 'type', 'menu', 'notifiable_id', 'data', 'status', 'read_at'];
    public $incrementing = false;
    protected $appends = ['formatted_date', 'time'];

    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->created_at)->translatedFormat('d F Y');
    }

    public function getTimeAttribute()
    {
        return Carbon::parse($this->created_at)->translatedFormat('H:i');
    }
}
