<?php

namespace App\Models;

use App\Http\Helpers\JournalHelpers;
use App\Http\Helpers\NotificationHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CashAdvancePayment extends Model
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
        'project_id',
        'fund_submission_id',
        'code',
        'to_model',
        'to_id',
        'to_name',
        'date',
        'reference',
        'currency_id',
        'exchange_rate',
        'keterangan',
        'status',
        'reject_reason',
        'returned_amount',
        'purchase_id',

    ];

    protected $appends = [
        'cash_advance_debit_total',
        'cash_advance_credit_total',
        'cash_advance_cash_bank',
        'cash_advance_cash_advance',
        'cash_advance_others',
        'outstanding_amount',
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
        $activity_logs = ActivityLog::where('subject_type', CashAdvancePayment::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', CashAdvancePayment::class)
            ->where('reference_id', $this->id)
            ->orderByDesc('created_at')
            ->get();

        return compact('status_logs', 'activity_logs');
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
            'nama' => 'required|max:50|string|unique:banks,id,' . $id,
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

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function fund_submission()
    {
        return $this->belongsTo(FundSubmission::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class)->withTrashed();
    }

    public function cash_advance_payment_details()
    {
        return $this->hasMany(CashAdvancePaymentDetail::class);
    }

    public function model_reference()
    {
        return $this->belongsTo($this->to_model, 'to_id');
    }

    public function created_by_data()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (is_null($model->code)) {
                $branch = Branch::find($model->branch_id);
                $model->code = generate_code(CashAdvancePayment::class, 'code', 'date', 'BBK', branch_sort: $branch->sort ?? null);
            }
            $model->status = "pending";
            $model->created_by = Auth::user()->id;
        });

        static::created(function ($model) {});

        static::updated(function ($model) {
            if ($model->getOriginal('status') != $model->status) {
                $notification = new NotificationHelper();
                $notification->send_notification(
                    title: "PEMBAYARAN DEPOSIT " . strtoupper($model->status),
                    body: $model->code,
                    reference_model: get_class($model),
                    reference_id: $model->id,
                    branch_id: $model->branch_id,
                    user_id: $model->created_by,
                    roles: [],
                    permissions: [],
                    link: route('admin.cash-advance-payment.show', ['cash_advance_payment' => $model->id]),
                );

                if ($model->status == 'approve') {
                    $journal = new JournalHelpers('cash-advance-payment', $model->id);
                    $journal->generate();

                    if ($model->fund_submission) {
                        $model->fund_submission->update([
                            'is_used' => 1,
                        ]);
                        if ($model->fund_submission->send_payment) {
                            $model->fund_submission->send_payment->update([
                                'status' => 'approve',
                                'realization_date' => $model->date,
                            ]);
                        }
                    }

                    if ($model->tax && ($model->tax->type ?? '') == 'ppn') {
                        $cash_advance_detail_tax = $model->cash_advance_payment_details()->where('type', 'tax')->first();
                        $dpp = $model->cash_advance_payment_details()->where('type', 'cash_advance')->first();
                        $supplier_tax = new ItemReceivingReportTax();
                        $supplier_tax->loadModel(
                            [
                                'reference_model' => get_class($cash_advance_detail_tax),
                                'reference_id' => $cash_advance_detail_tax->id,
                                'reference_parent_model' => CashAdvancePayment::class,
                                'reference_parent_id' => $model->id,
                                'date' => Carbon::parse($model->date),
                                'vendor_id' => $model->to_id,
                                'tax_id' => $model->tax_id,
                                'dpp' => ($dpp->debit * $model->exchange_rate),
                                'value' => $model->tax->value,
                                'amount' => ($dpp->debit * $model->exchange_rate) * $model->tax->value,
                            ]
                        );
                        $supplier_tax->save();
                    }
                } else {
                    $journals = Journal::where('reference_id', $model->id)
                        ->where('reference_model', CashAdvancePayment::class)->get();
                    foreach ($journals as $journal) {
                        $journal->delete();
                        $journal->journal_details->each(function ($detail) {
                            $detail->delete();
                        });
                    }

                    ItemReceivingReportTax::where('reference_parent_model', CashAdvancePayment::class)
                        ->where('reference_parent_id', $model->id)
                        ->delete();

                    if ($model->fund_submission) {
                        $model->fund_submission->update([
                            'is_used' => 0,
                        ]);
                        if ($model->fund_submission->send_payment) {
                            $model->fund_submission->send_payment->update([
                                'status' => 'pending',
                                'realization_date' => null,
                            ]);
                        }
                    }
                }
            }
        });

        static::deleted(function ($model) {
            $journals = Journal::where('reference_id', $model->id)
                ->where('reference_model', CashAdvancePayment::class)->get();
            foreach ($journals as $journal) {
                $journal->delete();
                $journal->journal_details->each(function ($detail) {
                    $detail->delete();
                });
            }

            BankCodeMutation::where('ref_model', CashAdvancePayment::class)
                ->where('ref_id', $model->id)
                ->delete();

            if ($model->fund_submission) {
                $model->fund_submission->update([
                    'is_used' => 0,
                ]);
                if ($model->fund_submission->send_payment) {
                    $model->fund_submission->send_payment->update([
                        'status' => 'pending',
                        'realization_date' => 0,
                    ]);
                }
            }

            ItemReceivingReportTax::where('reference_parent_model', CashAdvancePayment::class)
                ->where('reference_parent_id', $model->id)
                ->delete();
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

    public function getCashAdvanceCashBankAttribute()
    {
        return $this->cash_advance_payment_details()->where('type', 'cash_bank')->first();
    }

    public function getCashAdvanceCashAdvanceAttribute()
    {
        return $this->cash_advance_payment_details()->where('type', 'cash_advance')->first();
    }

    public function getCashAdvanceOthersAttribute()
    {
        return $this->cash_advance_payment_details()
            ->whereIn('type', ['other', 'tax'])
            ->get();
    }

    public function getCashAdvanceDebitTotalAttribute()
    {
        return $this->cash_advance_payment_details()->sum('debit') * $this->exchange_rate;
    }

    public function getCashAdvanceCreditTotalAttribute()
    {
        return $this->cash_advance_payment_details()->sum('credit') * $this->exchange_rate;
    }

    public function getOutstandingAmountAttribute()
    {
        $debit = $this->getCashAdvanceCashAdvanceAttribute()->debit;

        if ($this->tax) {
            $tax = $this->cash_advance_payment_details()->where('type', 'tax')->first();
            if ($tax) {
                $debit += $tax->debit;
            }
        }

        return $debit - $this->returned_amount;
    }

    public function getBankCodeMutationAttribute()
    {
        return BankCodeMutation::where('ref_model', CashAdvancePayment::class)
            ->where('ref_id', $this->id)
            ->first()->code ?? '';
    }

    public function supplier_invoice_down_payments()
    {
        return $this->hasMany(SupplierInvoiceDownPayment::class);
    }

    public function cash_advanced_return_details()
    {
        return $this->morphMany(CashAdvancedReturnDetail::class, 'cash_advanced_return_detailable', 'reference_model', 'reference_id');
    }
}
