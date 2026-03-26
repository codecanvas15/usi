<?php

namespace App\Models;

use App\Http\Helpers\JournalHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class IncomingPayment extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'fund_submission_id',
        'receive_payment_id',
        'cash_advance_payment_id',
        'coa_id',
        'project_id',
        'code',
        'date',
        'total',
        'status',
        'created_by',
    ];

    protected $append = [
        'credit_total',
        'local_credit_total',
        'bank_code_mutation',
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
        $activity_logs = ActivityLog::where('subject_type', IncomingPayment::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', IncomingPayment::class)
            ->where('reference_id', $this->id)
            ->orderByDesc('created_at')
            ->get();

        return compact('status_logs', 'activity_logs');
    }

    public function fund_submission()
    {
        return $this->belongsTo(FundSubmission::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function incoming_payment_details()
    {
        return $this->hasMany(IncomingPaymentDetail::class);
    }

    public function receive_payment()
    {
        return $this->belongsTo(ReceivePayment::class);
    }

    public function purchase_return()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    public function cash_advance_payment()
    {
        return $this->belongsTo(CashAdvancePayment::class);
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->status = "pending";
            $model->created_by = Auth::user()->id;

            if (!checkAvailableDate($model->date)) {
                throw new \Exception('Tanggal tidak tersedia / sudah tutup buku');
            }
        });

        static::created(function ($model) {});

        static::updated(function ($model) {
            if ($model->getOriginal('status') != $model->status) {
                if ($model->status == 'approve') {
                    $purchase_return_payment = IncomingPaymentDetail::where('incoming_payment_id', $model->id)
                        ->where('purchase_return_id', '!=', null)
                        ->first();

                    if ($purchase_return_payment) {
                        // create purchase history
                        PurchaseReturnHistory::updateOrCreate([
                            'purchase_return_id' => $purchase_return_payment->purchase_return_id,
                            'reference_parent_model' => IncomingPayment::class,
                            'reference_parent_id' => $model->id,
                        ], [
                            'purchase_return_id' => $purchase_return_payment->purchase_return_id,
                            'reference_parent_model' => IncomingPayment::class,
                            'reference_parent_id' => $model->id,
                            'date' => $model->date,
                            'amount' => $purchase_return_payment->credit,
                            'date' => $model->date,
                            'total' => $model->total,
                            'status' => 'approve',
                        ]);
                    }

                    if ($model->cash_advance_payment) {
                        $cash_advance_amount = IncomingPaymentDetail::where('incoming_payment_id', $model->id)
                            ->where('type', 'cash_advance')
                            ->first();

                        $model->cash_advance_payment->increment('returned_amount', $cash_advance_amount->credit);
                    }

                    $journal = new JournalHelpers('incoming-payment', $model->id);
                    $journal->generate();

                    if ($model->receive_payment) {
                        $receive_payment = $model->receive_payment;
                        $receive_payment->realization_date = Carbon::parse($model->date);
                        $receive_payment->save();
                    }
                } else {
                    $journals = Journal::where('reference_id', $model->id)
                        ->where('reference_model', IncomingPayment::class)->get();
                    foreach ($journals as $journal) {
                        $journal->delete();
                        $journal->journal_details->each(function ($detail) {
                            $detail->delete();
                        });
                    }

                    PurchaseReturnHistory::where('reference_parent_model', IncomingPayment::class)
                        ->where('reference_parent_id', $model->id)
                        ->delete();

                    if ($model->cash_advance_payment) {
                        $cash_advance_amount = IncomingPaymentDetail::where('incoming_payment_id', $model->id)
                            ->where('type', 'cash_advance')
                            ->first();

                        $model->cash_advance_payment->decrement('returned_amount', $cash_advance_amount->credit);
                    }

                    if ($model->receive_payment) {
                        $receive_payment = $model->receive_payment;
                        $receive_payment->realization_date = null;
                        $receive_payment->save();
                    }
                }
            }
        });

        static::deleted(function ($model) {
            $journals = Journal::where('reference_id', $model->id)
                ->where('reference_model', IncomingPayment::class)->get();
            foreach ($journals as $journal) {
                $journal->delete();
                $journal->journal_details->each(function ($detail) {
                    $detail->delete();
                });
            }

            BankCodeMutation::where('ref_model', IncomingPayment::class)
                ->where('ref_id', $model->id)
                ->delete();


            PurchaseReturnHistory::where('reference_parent_model', IncomingPayment::class)
                ->where('reference_parent_id', $model->id)
                ->delete();

            if ($model->cash_advance_payment) {
                $cash_advance_amount = IncomingPaymentDetail::where('incoming_payment_id', $model->id)
                    ->where('type', 'cash_advance')
                    ->first();

                $model->cash_advance_payment->decrement('returned_amount', $cash_advance_amount->credit);
            }

            if ($model->receive_payment) {
                $receive_payment = $model->receive_payment;
                $receive_payment->realization_date = null;
                $receive_payment->save();
            }
        });
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

    public function getCreditTotalAttribute()
    {
        return $this->incoming_payment_details()->sum('credit');
    }

    public function getLocalCreditTotalAttribute()
    {
        return $this->incoming_payment_details()->sum('credit') * $this->exchange_rate;
    }

    public function getBankCodeMutationAttribute()
    {
        return BankCodeMutation::where('ref_model', IncomingPayment::class)
            ->where('ref_id', $this->id)
            ->first()->code ?? '';
    }
}
