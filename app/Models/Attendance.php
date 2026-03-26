<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'branch_id',
        'date',
        'in_time',
        'out_time',
        'go_home_early',
        'late',
        'overtime',
        'work_hours',
        'attendance_hours',
        'description',
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
        });
    }

    /**
     * getSingleEmployeeInSelectedMontCount
     *
     * @param
     * @return mixed
     */
    public function getSingleEmployeeInSelectedMontCount($employee, $month)
    {
        $count = self::where('employee_id', $employee)
            ->whereMonth('date', $month)
            ->count();

        return $count;
    }

    /**
     * Get the employee that owns the Attendance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    /**
     * Get the branch that owns the Attendance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->date);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
