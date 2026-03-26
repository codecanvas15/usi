<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAssessment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'reference',
        'interviewer',
        'candidate',
        'assessment_date',
        'hiring_manager',
        'behavioral_rating',
        'skill_rating',
        'total_rating',
        'recommend_status',
        'first_note',
        'second_note',
        'third_note',
        'approved_by',
        'approval_status',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "user_assessments";

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->assessment_date);
    }

    public function interviewer_data()
    {
        return $this->belongsTo(Employee::class, 'interviewer')->withTrashed();
    }

    public function candidate_data()
    {
        return $this->belongsTo(LaborApplication::class, 'candidate')->withTrashed();
    }

    public function approved_data()
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    public function detail()
    {
        return $this->hasMany(UserAssessmentDetail::class)->withTrashed();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }
}
