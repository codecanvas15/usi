<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAssessmentDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_assessment_id',
        'reference',
        'interviewer',
        'candidate',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "user_assessment_details";

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function userAssessment()
    {
        return $this->belongsTo(UserAssessment::class)->withTrashed();
    }

    public function masterUserAssessment()
    {
        return $this->belongsTo(MasterUserAssessment::class)->withTrashed();
    }
}
