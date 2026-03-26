<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaborTransferForm extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'employee_id',
        'reference',
        'from_company',
        'to_company',
        'from_branch',
        'to_branch',
        'from_division',
        'to_division',
        'reason',
        'submitted_by',
        'created_by',
        'approved_by',
        'approval_status',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        //
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "labor_transfer_forms";

    public static function rules($method = 'create', $id = null)
    {
        $validate = [
            'reason' => 'required|string|max:255',
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

    public function employee()
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    public function from_branch_data()
    {
        return $this->belongsTo(Branch::class, 'from_branch')->withTrashed();
    }

    public function to_branch_data()
    {
        return $this->belongsTo(Branch::class, 'to_branch')->withTrashed();
    }

    public function from_division_data()
    {
        return $this->belongsTo(Division::class, 'from_division')->withTrashed();
    }

    public function to_division_data()
    {
        return $this->belongsTo(Division::class, 'to_division')->withTrashed();
    }

    public function submitted_by_data()
    {
        return $this->belongsTo(Employee::class, 'submitted_by')->withTrashed();
    }

    public function created_by_data()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function approved_by_data()
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    /**
     * getCheckAvailableDateAttribute for LaborTransfer
     *
     * @return bool
     */
    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->created_at);
    }
}
