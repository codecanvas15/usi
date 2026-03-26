<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "activity_log";

    /**
     * Get the user that owns the data.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'causer_id')->withTrashed();
    }

    /**
     * Get the referece that owns the data.
     */
    public function referece()
    {
        return $this->belongsTo($this->subject_type, 'subject_id');
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
