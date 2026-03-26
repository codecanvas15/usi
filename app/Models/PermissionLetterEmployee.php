<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PermissionLetterEmployee extends Model
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
        'employee_id',
        'branch_id',
        'letter_number',
        'letter_type',
        'letter_reason',
        'letter_date_start',
        'letter_date_end',
        'letter_status',
        'letter_note',
    ];

    protected $appends = [
        'letter_type_alias',
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
     * getCheckAvailableDateAttribute for generated Permission employee
     *
     * @return bool
     */
    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->created_at);
    }

    /**
     * validation rules
     *
     * @param  string  $method
     * @param  int  $id
     * @return array
     */
    public static function rules($method = 'create', $id = null)
    {
        // $letter_type = ["came too late","leave during working hours","leave early","not work"];

        $validate = [
            'employee_id' => 'required|integer',
            // 'letter_number' => 'required|string|max:32',
            'letter_type' => 'nullable|string|max:60',
            'letter_reason' => 'required|string|max:255',
            'letter_date_start' => 'nullable|date_format:H:i',
            'letter_date_end' => 'nullable',
            // 'letter_status' => 'required|string|max:32',
            'letter_note' => 'nullable|string',
            'file' => 'nullable|mimes:jpg,jpeg,png,pdf|max:6144',
        ];

        // * if has unique validation or diff rules when create or update
        if ($method == 'create') {
            $validate = array_merge($validate, []);
        } else {
            $validate = array_merge($validate, []);
        }

        return $validate;
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->branch_id = get_current_branch_id();
            if ($model->letter_number == null) {
                $types = [
                    'came too late' => 'T',
                    'leave during working hours' => 'K',
                    'leave early' => 'P',
                    'not work' => 'N'
                ];

                $last_model = PermissionLetterEmployee::where('letter_type', $model->letter_type)
                    ->where('branch_id', get_current_branch_id())
                    ->orderByDesc('created_at')
                    ->first();
                $model->letter_number = generate_code_transaction("PRO-HRD-{$types[$model->letter_type]}", $last_model->letter_number ?? "0000-0000-0000-0000");
            }

            $model->letter_status = 'pending';
        });

        static::updating(function ($model) {
            if ($model->getOriginal('letter_status') != $model->letter_status) {
                $late = null;
                $go_home_early = null;
                $description = '';
                if ($model->letter_type == 'came too late') {
                    $description = 'DATANG TERLAMBAT.';
                    $late = $model->letter_date_start;
                } elseif ($model->letter_type == 'leave early') {
                    $description = 'PULANG LEBIH AWAL.';
                    $go_home_early = $model->letter_date_end;
                }
                $description .= " $model->letter_reason";

                if ($model->letter_status == 'approve') {
                    Attendance::updateOrCreate([
                        'employee_id' => $model->employee_id,
                        'date' => Carbon::parse($late ?? $go_home_early)->format('Y-m-d'),
                    ], [
                        'employee_id' => $model->employee_id,
                        'date' => Carbon::parse($late ?? $go_home_early)->format('Y-m-d'),
                        'in_time' => $late ? Carbon::parse($late)->format('H:i:s') : null,
                        'out_time' => $go_home_early ? Carbon::parse($go_home_early)->format('H:i:s') : null,
                        'late' => $late ? Carbon::parse($late)->format('H:i:s') : null,
                        'go_home_early' => $go_home_early ? Carbon::parse($go_home_early)->format('H:i:s') : null,
                        'description' => $description,
                    ]);
                } else if (in_array($model->letter_status, ['revert', 'void'])) {
                    Attendance::where('employee_id', $model->employee_id)
                        ->whereDate('date', Carbon::parse($late ?? $go_home_early))
                        ->delete();
                }
            }
        });

        static::deleting(function ($model) {
            $late = null;
            $go_home_early = null;
            if ($model->letter_type == 'came too late') {
                $late = $model->letter_date_start;
            } elseif ($model->letter_type == 'leave early') {
                $go_home_early = $model->letter_date_end;
            }

            Attendance::where('employee_id', $model->employee_id)
                ->whereDate('date', Carbon::parse($late ?? $go_home_early))
                ->delete();
        });
    }

    /**
     * set attributes model
     *
     * @param $request
     */
    public function loadModel($request)
    {
        foreach ($this->fillable as $key_field) {
            foreach ($request as $key_request => $value) {
                if ($key_field == $key_request) {
                    $this->setAttribute($key_field, $value);
                }
            }
        }
    }

    /**
     * getLogsData
     *
     * @return array
     */
    public function getLogsDataAttribute()
    {
        $activity_logs = ActivityLog::where('subject_type', PermissionLetterEmployee::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', PermissionLetterEmployee::class)
            ->where('reference_id', $this->id)
            ->orderByDesc('created_at')
            ->get();

        return compact('status_logs', 'activity_logs');
    }

    /**
     * Get the employee that owns the PermissionLetterEmployee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    function getLetterTypeAliasAttribute()
    {
        $letter_type = [
            'came too late' => 'Datang Terlambat',
            'leave during working hours' => 'Izin Pada Jam Kerja',
            'leave early' => 'Pulang Lebih Awal',
            'not work' => 'Tidak Masuk Kerja'
        ];

        return $letter_type[$this->letter_type];
    }

    function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
