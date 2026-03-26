<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Leave extends Model
{
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
        'from_date',
        'to_date',
        'note',
        'status',
        'cause',
        'address',
        'phone_number',
        'day',
        'first_approved_by',
        'second_approved_by',
        'type',
        'necessary',
        'leave_remaining',
        'date',
        'attachment'
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
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($leave) {
            if (is_null($leave->branch_id)) {
                $leave->branch_id = auth()->user()->branch_id;
            }

            if (is_null($leave->status)) {
                $leave->status = 'pending';
            }

            if (is_null($leave->code)) {
                $leave->code = generate_code(self::class, 'code', 'from_date', 'CT', $leave->branch->sort, $leave->from_date);
            }
        });
    }

    /**
     * Get the branch that owns the Leave
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    /**
     * Get the division that owns the Leave
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class)->withTrashed();
    }

    public function employee()
    {
        return  $this->belongsTo(Employee::class)->withTrashed();
    }

    function getNecessaryAliasAttribute()
    {
        switch ($this->necessary) {
            case 'vacation':
                return 'Liburan';
                break;
            case 'illnes':
                return 'Sakit';
                break;
            case 'maternity':
                return 'Melahirkan';
                break;
            case 'others':
                return 'Lain-Lain';
                break;
            default:
                # code...
                break;
        }
    }

    public function changeFile()
    {
        return $this->hasMany(LeaveChangeFile::class);
    }

    public function mass_leave_detail()
    {
        return $this->hasOne(MassLeaveDetail::class);
    }
}
