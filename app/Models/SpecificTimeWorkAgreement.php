<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SpecificTimeWorkAgreement extends Model
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
        'employee_id',
        'division_id',
        'position_id',
        'reference_id',
        'reference_model',
        'second_employee_type',
        'second_division_id',
        'second_position_id',
        'created_by',
        'approved_by',
        'code',
        'date',
        'title',
        'work_agreement_type',
        'status',
        'description',
        'attachment',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            // If date is null, then set it to today
            if (is_null($model->date)) {
                $model->date = \Carbon\Carbon::now()->format('Y-m-d');
            }

            // If code is null, then generate code
            if (is_null($model->code)) {
                $branch = Branch::find($model->branch_id);
                $model->code = generate_code(self::class, 'code', 'date', 'PKWT', branch_sort: $branch->sort ?? null, date: $model->date);
            }

            // If status is null, then set it to pending
            if (is_null($model->status)) {
                $model->status = 'pending';
            }

            // If work_agreement_type is null, then set it to Undefined
            if (is_null($model->work_agreement_type)) {
                $model->work_agreement_type = 'Undefined';
            }

            // If created_by is null, then set it to current user
            if (is_null($model->created_by)) {
                $model->created_by = auth()->user()->id;
            }

            // set employee
            if ($model->employee) {
                if (is_null($model->division_id)) {
                    $model->division_id = $model->employee->division_id;
                }

                if (is_null($model->position_id)) {
                    $model->position_id = $model->employee->position_id;
                }
            }

            // set second employee
            if ($model->second_employee) {
                if (is_null($model->second_division_id)) {
                    $model->second_division_id = $model->second_employee->division_id;
                }

                if (is_null($model->second_position_id)) {
                    $model->second_position_id = $model->second_employee->position_id;
                }
            }
        });

        static::updating(function ($model) {
            // if employee is changed, then set division and position
            if ($model->isDirty('employee_id')) {
                if (is_null($model->division_id)) {
                    $model->division_id = $model->employee->division_id;
                }

                if (is_null($model->position_id)) {
                    $model->position_id = $model->employee->position_id;
                }
            }

            // if second employee is changed, then set division and position
            if ($model->isDirty('second_employee_id')) {
                if (is_null($model->second_division_id)) {
                    $model->second_division_id = $model->second_employee->division_id;
                }

                if (is_null($model->second_position_id)) {
                    $model->second_position_id = $model->second_employee->position_id;
                }
            }

            // If status is changed to approved, then set approved_by to current user
            if ($model->isDirty('status') && $model->status == 'approve') {
                $model->approved_by = auth()->user()->id;
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
     * Get the branch that owns the SpecificTimeWorkAgreement
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    /**
     * Get the employee that owns the SpecificTimeWorkAgreement
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    /**
     * Get the division that owns the SpecificTimeWorkAgreement
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class)->withTrashed();
    }

    /**
     * Get the position that owns the SpecificTimeWorkAgreement
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class)->withTrashed();
    }

    /**
     * Get the second_division that owns the SpecificTimeWorkAgreement
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function second_division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'second_division_id')->withTrashed();
    }

    /**
     * Get the second_position that owns the SpecificTimeWorkAgreement
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function second_position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'second_position_id')->withTrashed();
    }

    /**
     * Get the created_by that owns the SpecificTimeWorkAgreement
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_by_data(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Get the approved_by that owns the SpecificTimeWorkAgreement
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approved_by_data(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->date);
    }
}
