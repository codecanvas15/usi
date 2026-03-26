<?php

namespace App\Models;

use App\Http\Helpers\NotificationHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CashBond extends Model
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
        'project_id',
        'employee_id',
        'currency_id',
        'created_by',
        'code',
        'date',
        'reference',
        'exchange_rate',
        'description',
        'status',
        'reject_reason',
        'returned_amount',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'total_returned_amount',
        'bank_code_mutation'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (is_null($model->branch_id)) {
                $model->branch_id = get_current_branch_id();
            }

            if (is_null($model->created_by)) {
                $model->created_by = auth()->user()?->id;
            }

            if (is_null($model->status)) {
                $model->status = 'pending';
            }

            if (is_null($model->date)) {
                $model->date = Carbon::now()->format('Y-m-d');
            }

            if (is_null($model->code)) {
                $branch = Branch::find($model->branch_id);
                $model->code = generate_code(self::class, 'code', 'date', "CB", branch_sort: $branch->sort ?? null, date: $model->date);
            }

            if (!checkAvailableDate($model->date)) {
                throw new \Exception('Tanggal tidak tersedia / sudah tutup buku');
            }
        });

        static::updated(function ($model) {
            if ($model->getOriginal('status') != $model->status) {
                // * generate journal
                if ($model->status == 'approve') {
                    $journal = new \App\Http\Helpers\JournalHelpers('cash-bond', $model->id);
                    $journal->generate();
                }

                if (in_array($model->status, ['revert', 'void'])) {
                    // * delete journal
                    $journal = Journal::where('journal_type', 'Cash Bond')
                        ->where('reference_id', $model->id)
                        ->delete();
                }

                $notification = new NotificationHelper();
                $notification->send_notification(
                    branch_id: $model->branch_id,
                    user_id: $model->created_by,
                    roles: [],
                    permissions: [],
                    title: "KASBON " . strtoupper($model->status),
                    body: $model->code . ' - ' . $model->employee->name,
                    reference_model: get_class($model),
                    reference_id: $model->id,
                    link: route('admin.cash-bond.show', $model),
                );
            }
        });

        static::deleting(function ($model) {
            // * delete journal
            Journal::where('journal_type', 'Cash Bond')
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
     * get total returned amount
     *
     * @return string|float|null|int
     */
    public function getTotalReturnedAmountAttribute()
    {
        $data = $this->cashBondReturnDetails()
            ->whereHas('cash_bond_return', function ($query) {
                $query->where('status', 'approve');
            })
            ->sum('amount_to_return');

        return $data;
    }

    /**
     * getCheckAvailableDateAttribute for generated journal
     *
     * @return bool
     */
    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->date);
    }

    /**
     * Get the branch that owns the CashBond
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    /**
     * Get the project that owns the CashBond
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class)->withTrashed();
    }

    /**
     * Get the employee that owns the CashBond
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    /**
     * Get the currency that owns the CashBond
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    /**
     * Get the user that owns the CashBond
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Get all of the cashBondDetails for the CashBond
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cashBondDetails(): HasMany
    {
        return $this->hasMany(CashBondDetail::class);
    }

    /**
     * Get all of the comments for the CashBond
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cashBondReturnDetails(): HasMany
    {
        return $this->hasMany(CashBondReturnDetail::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function getBankCodeMutationAttribute()
    {
        return BankCodeMutation::where('ref_model', CashBond::class)
            ->where('ref_id', $this->id)
            ->first()->code ?? '';
    }
}
