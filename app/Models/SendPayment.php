<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SendPayment extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id',
        'fund_submission_id',
        'code',
        'date',
        'due_date',
        'realization_date',
        'cheque_no',
        'from_bank',
        'realization_bank',
        'status',
        'reject_reason',
    ];

    protected $appends = [
        'due_status',
    ];

    /**
     * set attrbutes model
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
     * validation rules
     *
     * @param  string  $method
     * @param  int  $id
     * @return array
     */
    public static function rules($method = 'create', $id = null)
    {
        $validate = [
            'date' => 'required',
            'due_date' => 'required',
            'cheque_no' => 'required',
            'from_bank' => 'required',
            'realization_bank' => 'required',
        ];

        // * if has unique validation or diff rules when create or update
        if ($method == 'create') {
            $validate = array_merge($validate, []);
        } else {
            $validate = array_merge($validate, []);
        }

        return $validate;
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $branch = Branch::find($model->branch_id);
            $model->code = generate_code(ReceivePayment::class, 'code', 'date', 'GRM', branch_sort: $branch->sort ?? null);
        });

        static::created(function ($model) {
        });
    }

    public function fund_submission()
    {
        return $this->belongsTo(FundSubmission::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function getDueStatusAttribute()
    {
        if (Carbon::now()->lt(Carbon::parse($this->due_date))) {
            $due_status = [
                'is_due' => false,
                'message' => "giro belum jatuh tempo",
            ];
        } else {
            $due_status = [
                'is_due' => true,
                'message' => "giro sudah jatuh tempo",
            ];
        }

        return $due_status;
    }

    public function getDueStatus($date = null)
    {
        if ($date) {
            $date = Carbon::parse($date);
        } else {
            $date = Carbon::now();
        }
        if ($date->lt(Carbon::parse($this->due_date))) {
            $due_status = [
                'is_due' => false,
                'message' => "giro belum jatuh tempo",
            ];
        } else {
            $due_status = [
                'is_due' => true,
                'message' => "giro sudah jatuh tempo",
            ];
        }

        return $due_status;
    }
}
