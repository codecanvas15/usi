<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractExtension extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'division_id',
        'from_date',
        'to_date',
        'code',
        'submission_status',
        'status',
        'created_by',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if ($model->branch_id == null) {
                $employee = Employee::find($model->employee_id);
                $model->branch_id = $employee->branch_id;
            }
            if ($model->code == null) {
                $branch = Branch::find($model->branch_id);
                $branch_sort = $branch->sort ?? null;
                $code = generate_code(self::class, 'code', 'created_at', 'CE', $branch_sort);
                $isExists = DB::table('contract_extensions')->where('code', $code)->exists();
                if ($isExists) {
                    $code = $model->generateNewCode($branch_sort);
                }
                $model->code = $code;
            }
            if ($model->status == null) {
                $model->status = 'pending';
            }
            if ($model->created_by == null) {
                $model->created_by = Auth::user()->id;
            }
        });
    }

    public static function rules($method = 'create', $id = null)
    {
        $validate = [
            'employee_id' => 'required',
            'division_id' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'assesment' => 'required|array',
            'assesment.*' => 'required',
            'note' => 'required|array',
            'note.*' => 'required|string|max:255',
        ];

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

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withTrashed();
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id')->withTrashed();
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id', 'id')->withTrashed();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id')->withTrashed();
    }

    public function assesment()
    {
        return $this->hasMany(AssesmentContractExtension::class);
    }

    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->from_date);
    }

    public function generateNewCode($branch_sort = null, $count = 0)
    {
        $count += 1;
        $new_code = generate_code(self::class, 'code', 'created_at', 'CE', $branch_sort, null, $count);

        $isExists = DB::table('contract_extensions')->where('code', $new_code)->exists();
        if ($isExists) {
            return $this->generateNewCode($branch_sort, $count);
        }

        return $new_code;
    }
}
