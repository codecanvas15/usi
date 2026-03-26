<?php

namespace App\Models;

use App\Http\Helpers\NotificationHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CashBondReturn extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'branch_id',
        'project_id',
        'employee_id',
        'coa_id',
        'currency_id',
        'code',
        'date',
        'description',
        'exchange_rate',
        'status',
    ];

    protected $appends = ['bank_code_mutation'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (is_null($model->branch_id)) {
                $model->branch_id = auth()->user()->branch_id;
            }

            if (is_null($model->date)) {
                $model->date = \Carbon\Carbon::now()->format('Y-m-d');
            }

            if (is_null($model->code)) {
                $branch = Branch::find($model->branch_id);
                $model->code = generate_code(self::class, 'code', 'date', "CBR", branch_sort: $branch->sort ?? null, date: $model->date);
            }

            if (is_null($model->status)) {
                $model->status = 'pending';
            }
        });

        static::updated(function ($model) {
            // * If status is changed
            if ($model->isDirty('status')) {
                // * If status is approve
                if ($model->status == 'approve') {
                    // * then create journal for cash bond return
                    $journal = new \App\Http\Helpers\JournalHelpers('cash-bond-return', $model->id);
                    $journal->generate();
                }

                if (in_array($model->status, ['revert', 'reject', 'void'])) {
                    $journal = Journal::where('journal_type', 'Cash Bond Return')
                        ->where('reference_id', $model->id)
                        ->delete();
                }

                $notification = new NotificationHelper();
                $notification->send_notification(
                    branch_id: $model->branch_id,
                    user_id: $model->created_by,
                    roles: [],
                    permissions: [],
                    title: "PENGEMBALIAN KASBON " . strtoupper($model->status),
                    body: $model->code . ' - ' . $model->employee->name,
                    reference_model: get_class($model),
                    reference_id: $model->id,
                    link: route('admin.cash-bond.show', $model),
                );
            }
        });

        static::deleting(function ($model) {
            // * delete journal
            Journal::where('journal_type', 'Cash Bond Return')
                ->where('reference_id', $model->id)
                ->delete();
        });
    }

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
        $activity_logs = ActivityLog::where('subject_type', self::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', self::class)
            ->where('reference_id', $this->id)
            ->orderByDesc('created_at')
            ->get();

        return compact('status_logs', 'activity_logs');
    }

    /**
     * Get the branch that owns the CashBondReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    /**
     * Get the project that owns the CashBondReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class)->withTrashed();
    }

    /**
     * Get the employee that owns the CashBondReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    /**
     * Get the coa that owns the CashBondReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    /**
     * Get the currency that owns the CashBondReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    /**
     * Get all of the cashBondReturnDetails for the CashBondReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cashBondReturnDetails(): HasMany
    {
        return $this->hasMany(CashBondReturnDetail::class);
    }

    /**
     * Get all of the cashBondReturnOthers for the CashBondReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cashBondReturnOthers(): HasMany
    {
        return $this->hasMany(CashBondReturnOther::class);
    }

    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->date);
    }

    public function getBankCodeMutationAttribute()
    {
        return BankCodeMutation::where('ref_model', CashBondReturn::class)
            ->where('ref_id', $this->id)
            ->first()->code ?? '';
    }
}
