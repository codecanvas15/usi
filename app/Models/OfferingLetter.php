<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferingLetter extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'labor_application_id',
        'created_by',
        'reference',
        'start_work_date',
        'work_location',
        'employment_status',
        'nik',
        'salary',
        'allowance_salary',
        'leave_day',
        'holiday_allowance',
        'to_email',
        'due_date',
        'offering_letter',
        'applicant_status',
        'applicant_status_reason',
        'applicant_status_at',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    public function laborApplication()
    {
        return $this->belongsTo(LaborApplication::class)->withTrashed();
    }

    public function created_by_data()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->start_work_date);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
