<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class FundSubmission extends Model
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
        'coa_id',
        'to_model',
        'date',
        'to_id',
        'to_name',
        'created_by',
        'item',
        'reference',
        'currency_id',
        'exchange_rate',
        'amount',
        'keterangan',
        'status',
        'reject_reason',
        'is_giro',
        'giro_number',
        'giro_liquid_date',
        'is_used',
        'purchase_id',
        'purchase_down_payment_id',
        'customer_id',
        'invoice_return_id',
        'cash_advance_receive_id',
        'tax_id',
        'tax_number',
        'tax_attachment',
    ];


    protected $appends = [
        // 'local_general_debit_total',
        // 'local_cash_advance_debit_total',
        // 'local_cash_advance_credit_total',
        // 'cash_advance_cash_bank',
        // 'cash_advance_cash_advance',
        // 'cash_advance_others',
        'can_change_sensitive_data',
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
            ->setDescriptionForEvent(fn (string $eventName) => "This data has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }

    /**
     * getLogsData
     *
     * @return array
     */
    public function getLogsDataAttribute()
    {
        $activity_logs = ActivityLog::where('subject_type', FundSubmission::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', FundSubmission::class)
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
            'item' => 'required',
            'vendor' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'amount' => 'required',
            'keterangan' => 'required|string|max:255',
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
     * Get the branch that owns the data.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    public function fund_submission_generals()
    {
        return $this->hasMany(FundSubmissionGeneral::class);
    }

    public function fund_submission_cash_advances()
    {
        return $this->hasMany(FundSubmissionCashAdvance::class);
    }

    public function model_reference()
    {
        return $this->belongsTo($this->to_model, 'to_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class)->withTrashed();
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class)->withTrashed();
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class)->withTrashed();
    }

    public function purchase_down_payment()
    {
        return $this->belongsTo(PurchaseDownPayment::class)->withTrashed();
    }

    public function fund_submission_supplier()
    {
        return $this->hasOne(FundSubmissionSupplier::class);
    }

    public function fund_submission_supplier_details()
    {
        return $this->hasMany(FundSubmissionSupplierDetail::class);
    }

    public function fund_submission_supplier_others()
    {
        return $this->hasMany(FundSubmissionSupplierOther::class);
    }

    public function fund_submission_customers()
    {
        return $this->hasMany(FundSubmissionCustomer::class);
    }

    public function fund_submission_purchase_returns()
    {
        return $this->hasMany(FundSubmissionPurchaseReturn::class);
    }

    public function send_payments()
    {
        return $this->hasMany(SendPayment::class);
    }

    public function send_payment()
    {
        return $this->hasOne(SendPayment::class)->where('status', '!=', 'cancel');
    }

    public function invoice_return()
    {
        return $this->belongsTo(InvoiceReturn::class)->withTrashed();
    }


    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function getGeneralDebitTotalAttribute()
    {
        return $this->fund_submission_generals()->sum('debit') * $this->exchange_rate;
    }

    public function getCashAdvanceCashBankAttribute()
    {
        return $this->fund_submission_cash_advances()->where('type', 'cash_bank')->first();
    }

    public function getCashAdvanceCashAdvanceAttribute()
    {
        return $this->fund_submission_cash_advances()->where('type', 'cash_advance')->first();
    }

    public function getCashAdvanceOthersAttribute()
    {
        return $this->fund_submission_cash_advances()
            ->whereIn('type', ['other', 'tax'])
            ->get();
    }

    public function getCashAdvanceDebitTotalAttribute()
    {
        return $this->fund_submission_cash_advances()->sum('debit') * $this->exchange_rate;
    }

    public function getCashAdvanceCreditTotalAttribute()
    {
        return $this->fund_submission_cash_advances()->sum('credit') * $this->exchange_rate;
    }

    public function outgoing_payments()
    {
        return $this->hasMany(OutgoingPayment::class);
    }

    public function cash_advance_payments()
    {
        return $this->hasMany(CashAdvancePayment::class);
    }

    public function account_payables()
    {
        return $this->hasMany(AccountPayable::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function cash_advance_receive()
    {
        return $this->belongsTo(CashAdvanceReceive::class);
    }

    public function getCanChangeSensitiveDataAttribute()
    {
        $check_next_submission_data = model::where('id', '>', $this->id)
            ->whereHas('fund_submission_supplier_details', function ($query) {
                $query->whereIn('supplier_invoice_parent_id', $this->fund_submission_supplier_details->pluck('supplier_invoice_parent_id'));
            })
            ->whereIn('status', ['approve'])
            ->first();

        return $check_next_submission_data ? false : true;
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
}
