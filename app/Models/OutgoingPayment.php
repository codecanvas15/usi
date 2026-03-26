<?php

namespace App\Models;

use App\Http\Helpers\JournalHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class OutgoingPayment extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'fund_submission_id',
        'cash_advance_receive_id',
        'coa_id',
        'project_id',
        'code',
        'date',
        'total',
        'status',
        'change_bank_reason',
        'created_by',
    ];

    protected $append = [
        'debit_total',
        'local_debit_total',
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
        $activity_logs = ActivityLog::where('subject_type', OutgoingPayment::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', OutgoingPayment::class)
            ->where('reference_id', $this->id)
            ->orderByDesc('created_at')
            ->get();

        return compact('status_logs', 'activity_logs');
    }

    public function fund_submission()
    {
        return $this->belongsTo(FundSubmission::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
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

    public function outgoing_payment_details()
    {
        return $this->hasMany(OutgoingPaymentDetail::class);
    }

    public function cash_advance_receive()
    {
        return $this->belongsTo(CashAdvanceReceive::class);
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

        static::updated(function ($model) {
            if ($model->getOriginal('status') != $model->status) {
                if ($model->status == 'approve') {
                    $invoice_return_payment = OutgoingPaymentDetail::where('outgoing_payment_id', $model->id)
                        ->where('invoice_return_id', '!=', null)
                        ->first();

                    if ($invoice_return_payment) {
                        // create invoice history
                        invoiceReturnHistory::updateOrCreate([
                            'invoice_return_id' => $invoice_return_payment->invoice_return_id,
                            'reference_parent_model' => OutgoingPayment::class,
                            'reference_parent_id' => $model->id,
                        ], [
                            'invoice_return_id' => $invoice_return_payment->invoice_return_id,
                            'reference_parent_model' => OutgoingPayment::class,
                            'reference_parent_id' => $model->id,
                            'date' => $model->date,
                            'amount' => $invoice_return_payment->debit,
                            'date' => $model->date,
                            'total' => $model->total,
                            'status' => 'approve',
                        ]);
                    }

                    $journal = new JournalHelpers('outgoing-payment', $model->id);
                    $journal->generate();

                    if ($model->fund_submission) {
                        $model->fund_submission->update([
                            'is_used' => 1,
                        ]);
                        if ($model->fund_submission->send_payment ?? null) {
                            $model->fund_submission->send_payment->update([
                                'status' => 'approve',
                                'realization_date' => $model->date,
                            ]);
                        }
                    }

                    if ($model->cash_advance_receive) {
                        $cash_advance_amount = OutgoingPaymentDetail::where('outgoing_payment_id', $model->id)
                            ->where('type', 'cash_advance')
                            ->first();

                        $model->cash_advance_receive->increment('returned_amount', $cash_advance_amount->debit ?? 0);
                    }
                } else {
                    $journals = Journal::where('reference_id', $model->id)
                        ->where('reference_model', OutgoingPayment::class)->get();
                    foreach ($journals as $journal) {
                        $journal->delete();
                        $journal->journal_details->each(function ($detail) {
                            $detail->delete();
                        });
                    }
                    if ($model->fund_submission) {
                        $model->fund_submission->update([
                            'is_used' => 0,
                        ]);
                    }
                    if ($model->fund_submission->send_payment ?? null) {
                        $model->fund_submission->send_payment->update([
                            'status' => 'pending',
                            'realization_date' => null,
                        ]);
                    }

                    InvoiceReturnHistory::where('reference_parent_model', OutgoingPayment::class)
                        ->where('reference_parent_id', $model->id)
                        ->delete();

                    if ($model->cash_advance_receive) {
                        $cash_advance_amount = OutgoingPaymentDetail::where('outgoing_payment_id', $model->id)
                            ->where('type', 'cash_advance')
                            ->first();

                        $model->cash_advance_receive->decrement('returned_amount', $cash_advance_amount->debit);
                    }
                }
            }
        });

        static::deleting(function ($model) {
            if ($model->cash_advance_receive) {
                $cash_advance_amount = OutgoingPaymentDetail::where('outgoing_payment_id', $model->id)
                    ->where('type', 'cash_advance')
                    ->first();

                $model->cash_advance_receive->increment('returned_amount', $cash_advance_amount->debit ?? 0);
            }
        });

        static::deleted(function ($model) {
            $journals = Journal::where('reference_id', $model->id)
                ->where('reference_model', OutgoingPayment::class)->get();

            foreach ($journals as $journal) {
                $journal->delete();
                $journal->journal_details->each(function ($detail) {
                    $detail->delete();
                });
            }
            BankCodeMutation::where('ref_model', OutgoingPayment::class)
                ->where('ref_id', $model->id)
                ->delete();

            InvoiceReturnHistory::where('reference_parent_model', OutgoingPayment::class)
                ->where('reference_parent_id', $model->id)
                ->delete();

            if ($model->fund_submission) {
                $model->fund_submission->update([
                    'is_used' => 0,
                ]);
                if ($model->fund_submission->send_payment ?? null) {
                    $model->fund_submission->send_payment->update([
                        'status' => 'pending',
                        'realization_date' => null,
                    ]);
                }
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

    public function getDebitTotalAttribute()
    {
        return $this->outgoing_payment_details()->sum('debit');
    }

    public function getLocalDebitTotalAttribute()
    {
        return $this->outgoing_payment_details()->sum('debit') * $this->exchange_rate;
    }

    public function getBankCodeMutationAttribute()
    {
        return BankCodeMutation::where('ref_model', OutgoingPayment::class)
            ->where('ref_id', $this->id)
            ->first()->code ?? '';
    }

    public function invoice_return()
    {
        return $this->belongsTo(InvoiceReturn::class)->withTrashed();
    }
}
