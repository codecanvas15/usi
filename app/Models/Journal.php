<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Journal extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id',
        'vendor_id',
        'customer_id',
        'code',
        'bank_code_mutation',
        'date',
        'reference_number',
        'reference',
        'document_reference',
        'remark',
        'status',
        'exchange_rate',
        'journal_type',
        'credit_total',
        'debit_total',
        'credit_total_exchanged',
        'debit_total_exchanged',
        'currency_id',
        'created_by',
        'approved_by',
        'reference_model',
        'reference_id',
        'project_id',
        'is_generated',
        'send_payment_id',
        'receive_payment_id',
    ];

    protected $casts = [
        'reference' => 'array',
        'document_reference' => 'array',
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
     * validation rules
     *
     * @param  string  $method
     * @param  int  $id
     * @return array
     */
    public static function rules($method = 'create', $id = null)
    {
        $validate = [
            'date' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'remark' => 'nullable|string|max:65535',
            'exchange_rate' => 'required',
            'currency_id' => 'required|exists:currencies,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'customer_id' => 'nullable|exists:customers,id',
        ];

        // * if has unique validation or diff rules when create or update
        if ($method == 'create') {
            $validate = array_merge($validate, []);
        } else {
            $validate = array_merge($validate, []);
        }

        return $validate;
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if ($model->code == null) {
                $last_data = Journal::whereMonth('date', Carbon::parse($model->date))
                    ->whereYear('date', Carbon::parse($model->date))
                    ->orderBy('id', 'desc')
                    ->withTrashed()
                    ->first();

                if ($last_data) {
                    $model->code = generate_code_transaction_with_out_branch("JOUR", $last_data->code, $model->date);
                } else {
                    $model->code = generate_code_transaction_with_out_branch("JOUR", "0000-0000-0000-0000", $model->date);
                }
            }

            if ($model->journal_type == null) {
                $model->journal_type = "General";
            }

            $document_reference = $model->document_reference;

            $model->created_by = auth()->user()->id ?? $document_reference->created_by ??  1;

            if ($model->status == null) {
                $model->status = 'pending';
            }

            // * exchange rate
            if ($model->exchange_rate == null) {
                $model->exchange_rate = 1;
            }

            $model->credit_total_exchanged = $model->credit_total * $model->exchange_rate;
            $model->debit_total_exchanged = $model->debit_total * $model->exchange_rate;
        });

        static::created(function ($model) {
            if (isset($model->document_reference['model']) && isset($model->document_reference['id'])) {
                $reference_data = $model->document_reference['model']::find($model->document_reference['id']);
                if ($reference_data?->project) {
                    $model->project_id = $reference_data->project_id;
                }
            }
        });

        static::updating(function ($model) {
            // * if status change
            if ($model->getOriginal('status') != $model->status) {
                // * if approve
                if ($model->status == 'approve') {
                    $model->approved_by = auth()->user()->id;
                    // $model->save();
                }
            }

            $model->credit_total_exchanged = $model->credit_total * $model->exchange_rate;
            $model->debit_total_exchanged = $model->debit_total * $model->exchange_rate;
        });
    }

    /**
     * getLogsData
     *
     * @return array
     */
    public function getLogsDataAttribute()
    {
        $activity_logs = ActivityLog::where('subject_type', Journal::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', Journal::class)
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

    public function currency()
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    public function create()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function approve()
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    /**
     * Get the item_reveiving_report that owns the data.
     */
    public function item_reveiving_report()
    {
        return $this->belongsTo(ItemReceivingReport::class)->withTrashed();
    }

    /**
     * Get the reference_model that owns the Journal
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reference_data(): BelongsTo
    {
        return $this->belongsTo($this->reference_model, $this->reference_id)->withTrashed();
    }

    /**
     * Get the project that owns the Journal
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class)->withTrashed();
    }

    public function journal_details()
    {
        return $this->hasMany(JournalDetail::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
