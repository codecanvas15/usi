<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LaborDemandDetail extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'labor_demand_id',
        'position_id',
        'degree_id',
        'education_id',
        'position_name',
        'gender',
        'min_age',
        'max_age',
        'quantity',
        'quantity_complete',
        'long_work_experience',
        'work_experience',
        'skills',
        'job_description',
        'description',
    ];

    protected $appends = ['qr_code'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            // * set default value for status
            if (is_null($model->status)) {
                $model->status = 'pending';
            }
        });

        static::updating(function ($model) {
            // if ($model->isDirty('quantity_complete')) {

            //     // * update detail data status
            //     if ($model->quantity > $model->quantity_complete && !in_array($model->status, ['partial', 'pending', 'reject', 'done', 'void'])) {
            //         $model->status = 'partial';
            //     }

            //     if ($model->quantity == $model->quantity_complete) {
            //         $model->status = 'done';
            //     }

            //     // * check the parent status
            //     $labor_demand = $model->labor_demand;
            //     $labor_demand_details_done_count = $labor_demand->labor_demand_details->where('status', 'done')->whereNotIn('status', ['void', 'reject', 'cancel'])->count();
            //     $labor_demand_details_count = $labor_demand->labor_demand_details->whereNotIn('status', ['void', 'reject', 'cancel'])->count();

            //     if ($labor_demand_details_done_count == $labor_demand_details_count) {
            //         $labor_demand->status = 'done';
            //     } else {
            //         $labor_demand->status = 'partial';
            //     }

            //     $labor_demand->save();
            // }
        });
    }

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
            ->setDescriptionForEvent(fn (string $eventName) => "This data has been {$eventName}")
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

    /**
     * Get the position that owns the LaborDemandDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class)->withTrashed();
    }

    public function degree(): BelongsTo
    {
        return $this->belongsTo(Degree::class)->withTrashed();
    }

    public function education(): BelongsTo
    {
        return $this->belongsTo(Education::class)->withTrashed();
    }

    /**
     * Get the labor demand that owns the LaborDemandDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function labor_demand(): BelongsTo
    {
        return $this->belongsTo(LaborDemand::class)->withTrashed();
    }

    public function getQrCodeAttribute()
    {
        $qr_url = route('guest.labor-application.create', ['labor_demand_detail_id' => $this->id]);
        return $qr_url;
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
