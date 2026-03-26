<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrdAssessmentDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'master_hrd_assessment_id',
        'notes',
        'rating',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "hrd_assessment_details";

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function masterHrdAssessment()
    {
        return $this->belongsTo(MasterHrdAssessment::class)->withTrashed();
    }
}
