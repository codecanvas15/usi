<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ModelAuthorization extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'model_id',
        'division_id',
        'user_id',
        'level',
        'minimum_value',
        'role',
    ];

    /**
     * init activity logs
     *
     * @return LogsOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly(['created_at', 'updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "This data has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }

    /**
     * getLogsData
     *
     * @return array
     */
    public function getLogsDataAttribute()
    {
        $activity_logs = ActivityLog::where('subject_type', self::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', self::class)
            ->where('reference_id', $this->id)
            ->orderByDesc('created_at')
            ->get();

        return compact('status_logs', 'activity_logs');
    }


    public function model()
    {
        return $this->belongsTo(ModelTable::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model_authorization_branches()
    {
        return $this->hasMany(ModelAuthorizationBranch::class);
    }

    public function model_authorization_divisions()
    {
        return $this->hasMany(ModelAuthorizationDivision::class);
    }
}
