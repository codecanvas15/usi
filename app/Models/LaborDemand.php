<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LaborDemand extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id',
        'division_id',
        'user_id',
        'code',
        'location',
        'status',
        'approved_by_hrd',
        'approved_by_director',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (is_null($model->branch_id)) {
                $model->branch_id = auth()->user()->branch_id;
            }

            if (is_null($model->division_id)) {
                $model->division_id = auth()->user()->division_id;
            }

            if (is_null($model->user_id)) {
                $model->user_id = auth()->user()->id;
            }

            if (is_null($model->code)) {
                $branch = Branch::find($model->branch_id);
                $model->code = generate_code(self::class, 'code', 'created_at', 'LB', branch_sort: $branch->sort ?? null);
            }

            $model->status = 'pending';
        });

        static::updating(function ($model) {
            // ! status updating
            if ($model->isDirty('status')) {
                // * if status is reject or void
                if (in_array($model->status, ['reject', 'void', 'revert'])) {
                    $model->labor_demand_details()
                        ->update([
                            'status' => $model->status,
                        ]);
                }

                // * if status is approved
                if ($model->status == 'approve') {
                    $model->labor_demand_details()
                        ->where('status', '!=', 'reject')
                        ->update([
                            'status' => 'approve',
                        ]);
                }

                // * if status is done
                if ($model->status == 'done') {
                    // * update status labor demand detail
                    $model->labor_demand_details()
                        ->where('status', '!=', 'reject')
                        ->update([
                            'status' => 'done',
                        ]);

                    // * if details is approve set to void
                    $model->labor_demand_details()
                        ->where('status', 'approve')
                        ->update([
                            'status' => 'void',
                        ]);

                    // * create status log
                    create_activity_status_log_not_trait(self::class, $model->id, 'your request was completed', $model->getOriginal('status'), 'done');
                }
            }
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
     * Get the branch that owns the LaborDemand
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    /**
     * Get the division that owns the LaborDemand
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class)->withTrashed();
    }

    /**
     * Get the user that owns the LaborDemand
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get all of the labor_demand_details for the LaborDemand
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function labor_demand_details(): HasMany
    {
        return $this->hasMany(LaborDemandDetail::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
