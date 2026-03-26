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

class CashAdvanceReceive extends Model
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
        'code',
        'customer_id',
        'date',
        'reference',
        'currency_id',
        'exchange_rate',
        'keterangan',
        'status',
        'reject_reason',
        'returned_amount',
    ];

    protected $append = [
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
        $activity_logs = ActivityLog::where('subject_type', CashAdvanceReceive::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', CashAdvanceReceive::class)
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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

    public function cash_advance_receive_details()
    {
        return $this->hasMany(CashAdvanceReceiveDetail::class);
    }

    public function model_reference()
    {
        return $this->belongsTo($this->to_model, 'to_id');
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
            if (!$model->date) {
                $model->date = date('Y-m-d', strtotime($model->date));
            }
            if (is_null($model->code)) {
                $branch = Branch::find($model->branch_id);
                $model->code = generate_code(CashAdvanceReceive::class, 'code', 'date', 'UMM', branch_sort: $branch->sort ?? null, date: $model->date);
            }
            $model->status = "pending";
            $model->created_by = Auth::user()->id;
        });

        static::created(function ($model) {});

        static::updated(function ($model) {
            if ($model->getOriginal('status') != $model->status) {
                if ($model->status == 'approve') {
                    $journal = new JournalHelpers('cash-advance-receive', $model->id);
                    $journal->generate();

                    if ($model->tax && ($model->tax->type ?? '') == 'ppn') {
                        $cash_advance_detail_tax = $model->cash_advance_receive_details()->where('type', 'tax')->first();
                        $dpp = $model->cash_advance_receive_details()->where('type', 'cash_advance')->first();
                        $invoice_tax = new InvoiceTax();
                        $invoice_tax->loadModel(
                            [
                                'reference_model' => get_class($cash_advance_detail_tax),
                                'reference_id' => $cash_advance_detail_tax->id,
                                'reference_parent_model' => CashAdvanceReceive::class,
                                'reference_parent_id' => $model->id,
                                'date' => Carbon::parse($model->date),
                                'customer_id' => $model->customer_id,
                                'tax_id' => $model->tax_id,
                                'dpp' => ($dpp->credit * $model->exchange_rate),
                                'value' => $model->tax->value,
                                'amount' => ($dpp->credit * $model->exchange_rate) * $model->tax->value,
                            ]
                        );
                        $invoice_tax->save();
                    }
                } else {
                    $journals = Journal::where('reference_id', $model->id)
                        ->where('reference_model', CashAdvanceReceive::class)->get();
                    foreach ($journals as $journal) {
                        $journal->delete();
                        $journal->journal_details->each(function ($detail) {
                            $detail->delete();
                        });
                    }

                    InvoiceTax::where('reference_parent_model', CashAdvanceReceive::class)
                        ->where('reference_parent_id', $model->id)
                        ->delete();
                }

                $notification = new NotificationHelper();
                $notification->send_notification(
                    branch_id: $model->branch_id,
                    user_id: $model->created_by,
                    roles: [],
                    permissions: [],
                    title: "UANG MUKA CUSTOMER " . strtoupper($model->status),
                    body: $model->code . ' - ' . $model->customer->nama,
                    reference_model: get_class($model),
                    reference_id: $model->id,
                    link: route('admin.cash-advance-receive.show', $model),
                );
            }
        });

        static::deleted(function ($model) {
            $journals = Journal::where('reference_id', $model->id)
                ->where('reference_model', CashAdvanceReceive::class)->get();
            foreach ($journals as $journal) {
                $journal->delete();
                $journal->journal_details->each(function ($detail) {
                    $detail->delete();
                });
            }
            BankCodeMutation::where('ref_model', CashAdvanceReceive::class)
                ->where('ref_id', $model->id)
                ->delete();

            InvoiceTax::where('reference_model', CashAdvanceReceive::class)
                ->where('reference_id', $model->id)
                ->delete();
        });
    }

    public function getCashAdvanceCashBankAttribute()
    {
        return $this->cash_advance_receive_details()->where('type', 'cash_bank')->first();
    }

    public function getCashAdvanceCashAdvanceAttribute()
    {
        return $this->cash_advance_receive_details()->where('type', 'cash_advance')->first();
    }

    public function getCashAdvanceOthersAttribute()
    {
        return $this->cash_advance_receive_details()
            ->whereIn('type', ['other', 'tax'])
            ->get();
    }

    public function getCashAdvanceDebitTotalAttribute()
    {
        return $this->cash_advance_receive_details()->sum('debit') * $this->exchange_rate;
    }

    public function getCashAdvanceCreditTotalAttribute()
    {
        return $this->cash_advance_receive_details()->sum('credit') * $this->exchange_rate;
    }

    public function getOutstandingAmountAttribute()
    {
        return $this->getCashAdvanceCashAdvanceAttribute()->credit - $this->returned_amount;
    }

    public function getBankCodeMutationAttribute()
    {
        return BankCodeMutation::where('ref_model', CashAdvanceReceive::class)
            ->where('ref_id', $this->id)
            ->first()->code ?? '';
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class)->withTrashed();
    }

    /**
     * getCheckAvailableAttribute for geneated journal
     *
     * @return bool
     */
    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->date);
    }
}
