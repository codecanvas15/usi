<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ReceivePayment extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id',
        'pay_from',
        'customer_id',
        'from_name',
        'currency_id',
        'exchange_rate',
        'code',
        'date',
        'due_date',
        'realization_date',
        'cheque_no',
        'from_bank',
        'realization_bank',
        'amount',
        'status',
        'reject_reason',
        'created_by',
    ];

    protected $appends = [
        'due_status',
        'outstanding_amount'
    ];

    /**
     * init activity logs
     *
     * @return LogsOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly(['created_at', 'updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "This data has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }

    /**
     * getLogsData
     *
     * @return array
     */
    public function getLogsDataAttribute()
    {
        $activity_logs = ActivityLog::where('subject_type', ReceivePayment::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', ReceivePayment::class)
            ->where('reference_id', $this->id)
            ->orderByDesc('created_at')
            ->get();

        return compact('status_logs', 'activity_logs');
    }

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
            'branch_id' => 'required',
            'pay_from' => 'required',
            'customer_id' => 'nullable',
            'from_name' => 'nullable',
            'currency_id' => 'required',
            'exchange_rate' => 'required',
            'date' => 'required',
            'due_date' => 'required',
            'cheque_no' => 'required',
            'from_bank' => 'required',
            'realization_bank' => 'required',
            'amount' => 'required',
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
            $model->code = generate_code(ReceivePayment::class, 'code', 'date', 'GRM', branch_sort: $branch->sort ?? null, date: $model->date);
            $model->status = "pending";
            $model->created_by = Auth::user()->id;
        });

        static::created(function ($model) {});
    }

    /**
     * getCheckAvailableAttribute for geneated CashAdvance
     *
     * @return bool
     */
    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->date);
    }

    /**
     * Get the branch that owns the data.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function receivables_payments()
    {
        return $this->hasMany(ReceivablesPayment::class)
            ->whereNotIn('status', ['reject', 'void']);
    }

    public function incoming_payments()
    {
        return $this->hasMany(IncomingPayment::class)
            ->whereNotIn('status', ['reject', 'void']);
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

    public function getOutstandingAmountAttribute()
    {
        return $this->amount - $this->receivables_payments->sum('total');
    }
}
