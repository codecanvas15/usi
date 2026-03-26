<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GpEvaluation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'employee_id',
        'created_by',
        'approved_by',
        'reference',
        'date',
        'total_score',
        'notes',
        'approval_status',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "gp_evaluations";

    public static function rules($method = 'create', $id = null)
    {
        $validate = [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:255',
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

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    public function created_by_data()
    {
        return $this->belongsTo(Employee::class, 'created_by')->withTrashed();
    }

    public function approved_by_data()
    {
        return $this->belongsTo(Employee::class, 'approved_by')->withTrashed();
    }

    public function detail()
    {
        return $this->hasMany(GpEvaluationDetail::class)->withTrashed();
    }

    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->date);
    }
}
