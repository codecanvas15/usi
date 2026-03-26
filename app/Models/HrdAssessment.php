<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrdAssessment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'interviewer',
        'candidate',
        'position',
        'reference',
        'assessment_date',
        'assessment_status',
        'notes',
        'approved_by',
        'approval_status',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "hrd_assessments";

    public static function rules($method = 'create', $id = null)
    {
        $validate = [
            'assessment_date' => 'required|date',
        ];

        // * if has unique validation or diff rules when create or update
        if ($method == 'create') {
            $validate = array_merge($validate, []);
        } else {
            $validate = array_merge($validate, []);
        }

        return $validate;
    }

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
     * getCheckAvailableDateAttribute for generated journal
     *
     * @return bool
     */
    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->assessment_date);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function interviewer_data()
    {
        return $this->belongsTo(Employee::class, 'interviewer')->withTrashed();
    }

    public function candidate_data()
    {
        return $this->belongsTo(LaborApplication::class, 'candidate')->withTrashed();
    }

    public function position_data()
    {
        return $this->belongsTo(Position::class, 'position')->withTrashed();
    }

    public function detail()
    {
        return $this->hasMany(HrdAssessmentDetail::class)->withTrashed();
    }
}
