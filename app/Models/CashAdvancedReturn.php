<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CashAdvancedReturn extends Model
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
        'currency_id',
        'invoice_currency_id',
        'reference_id',
        'reference_model',
        'type',
        'date',
        'code',
        'status',
        'exchange_rate',
        'amount_total',
        'cash_advance_total',
        'invoice_total',
        'other_total',
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

            if (is_null($model->status)) {
                $model->status = 'pending';
            }

            if (is_null($model->date)) {
                $model->date = \Carbon\Carbon::now();
            }

            if (!checkAvailableDate($model->date)) {
                throw new \Exception('Tanggal tidak tersedia / sudah tutup buku');
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('status')) {
                if ($model->status == 'approve') {
                    if ($model->type == 'customer') {
                        foreach ($model->cashAdvancedReturnInvoices as $key => $detail) {
                            $receive_amount = $detail->amount_to_paid_or_return;

                            //CREATE OR UPDATE INVOICE PAYMENT
                            InvoicePayment::updateOrCreate([
                                'model' => CashAdvancedReturnInvoice::class,
                                'reference_id' => $detail->id,
                            ], [
                                'model' => CashAdvancedReturnInvoice::class,
                                'reference_id' => $detail->id,
                                'invoice_model' => $detail->reference->model_reference,
                                'invoice_id' => $detail->reference->reference_id,
                                'currency_id' => $detail->reference->currency_id,
                                'exchange_rate' => $detail->exchange_rate,
                                'date' => $model->date,
                                'amount_to_receive' => 0,
                                'receive_amount' => $receive_amount,
                                'note' => "Pengembalian Uang Muka - $model->code / $detail->note",
                            ]);

                            $model->checkInvoice($detail->reference);
                        }
                    }

                    if ($model->type == 'vendor') {
                        foreach ($model->cashAdvancedReturnInvoices as $key => $detail) {
                            // * EACH INVOICE HAS MANY ITEM RECEIVING REPORT
                            foreach ($detail->cash_advanced_return_invoice_details as $key => $supplier_invoice) {
                                // * CONVERT TO LOCAL CURRENCY
                                $receive_amount = $supplier_invoice->amount;

                                $lpb = $supplier_invoice->item_receiving_report;

                                // * CREATE OR UPDATE INVOICE PAYMENT
                                SupplierInvoicePayment::updateOrCreate([
                                    'model' => CashAdvancedReturnInvoice::class,
                                    'reference_id' => $detail->id,
                                    'item_receiving_report_id' => $supplier_invoice->item_receiving_report_id,
                                ], [
                                    'item_receiving_report_id' => $supplier_invoice->item_receiving_report_id,
                                    'model' => CashAdvancedReturnInvoice::class,
                                    'reference_id' => $detail->id,
                                    'supplier_invoice_model' => $detail->reference->model_reference,
                                    'supplier_invoice_id' => $detail->reference->reference_id,
                                    'currency_id' => $detail->reference->currency_id,
                                    'exchange_rate' => $detail->exchange_rate,
                                    'date' => $model->date,
                                    'amount_to_pay' => 0,
                                    'pay_amount' => $receive_amount,
                                    'note' => "Pengembalian Uang Muka - $model->code / $lpb->kode / $detail->note",
                                ]);

                                $model->checkSupplierInvoice($detail->reference);
                            }
                        }
                    }

                    // UPDATE CASH ADVANCE RETURNED AMOUNT
                    foreach ($model->cashAdvancedReturnDetails as $key => $detail) {
                        $cash_advance = $detail->reference;

                        if ($cash_advance->returned_amount == 0) {
                            $cash_advance->returned_amount = $detail->amount_to_return;
                        } else {
                            $cash_advance->returned_amount += $detail->amount_to_return;
                        }

                        $cash_advance->save();
                    }
                }
            }
        });

        static::updated(function ($model) {
            if ($model->isDirty('status')) {
                if ($model->status == 'approve') {
                    $journal = new \App\Http\Helpers\JournalHelpers('cash-advance-return', $model->id);
                    $journal->generate();
                }

                if ($model->status == 'void') {
                    $journal = Journal::where('reference_id', $model->id)->where('reference_model', self::class)->update([
                        'status' => 'void',
                    ]);
                }

                if ($model->status == 'revert') {
                    $journal = Journal::where('reference_id', $model->id)->where('reference_model', self::class)->delete();
                }

                if ($model->status == 'pending') {
                    $journal = Journal::where('reference_id', $model->id)->where('reference_model', self::class)->delete();
                }

                if (in_array($model->status, ['void', 'revert', 'approve'])) {
                    if (in_array($model->status, ['void', 'revert'])) {
                        if ($model->type == "customer") {
                            foreach ($model->cashAdvancedReturnInvoices as $key => $detail) {
                                InvoicePayment::where('model', CashAdvancedReturnInvoice::class)
                                    ->where('reference_id', $detail->id)
                                    ->delete();

                                $model->checkInvoice($detail->reference);
                            }
                        }
                        if ($model->type == "vendor") {
                            foreach ($model->cashAdvancedReturnInvoices as $key => $detail) {
                                SupplierInvoicePayment::where('model', CashAdvancedReturnInvoice::class)
                                    ->where('reference_id', $detail->id)
                                    ->delete();

                                $model->checkSupplierInvoice($detail->reference);
                            }
                        }
                    }

                    $returned_amounts = CashAdvancedReturnDetail::whereHas('cash_advanced_return', function ($query) use ($model) {
                        $query->where('type', $model->type)
                            ->where('status', 'approve');
                    })
                        ->whereIn('reference_id', $model->cashAdvancedReturnDetails->pluck('reference_id'))
                        ->get();

                    foreach ($model->cashAdvancedReturnDetails as $key => $detail) {
                        $cash_advance = $detail->reference;

                        $returned_amount = $returned_amounts->where('reference_id', $detail->reference_id)
                            ->where('reference_model', $detail->reference_model)
                            ->sum('amount_to_return');

                        $cash_advance->returned_amount = $returned_amount;
                        $cash_advance->save();
                    }
                }
            }
        });
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
     * getCheckAvailableDateAttribute for generated journal
     *
     * @return bool
     */
    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->date);
    }

    /**
     * get total
     *
     * @return float
     */
    public function getTotalAttribute()
    {
        return $this->cashAdvancedReturnDetails->sum('amount_to_return');
    }

    public function getCurrencyInvoiceAttribute()
    {
        return $this->cashAdvancedReturnInvoices->first()->currency;
    }

    /**
     * Get the branch that owns the CashAdvancedReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    /**
     * Get the project that owns the CashAdvancedReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class)->withTrashed();
    }

    /**
     * Get the currency that owns the CashAdvancedReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    /**
     * Get the invoiceCurrency that owns the CashAdvancedReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice_currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'invoice_currency_id')->withTrashed();
    }

    /**
     * Get the reference that owns the CashAdvancedReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo($this->reference_model, 'reference_id')->withTrashed();
    }

    /**
     * Get all of the cashAdvancedReturnDetails for the CashAdvancedReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cashAdvancedReturnDetails(): HasMany
    {
        return $this->hasMany(CashAdvancedReturnDetail::class);
    }

    /**
     * Get all of the cashAdvancedReturnInvoices for the CashAdvancedReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cashAdvancedReturnInvoices(): HasMany
    {
        return $this->hasMany(CashAdvancedReturnInvoice::class);
    }

    /**
     * Get all of the cashAdvancedReturnTransactions for the CashAdvancedReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cashAdvancedReturnTransactions(): HasMany
    {
        return $this->hasMany(CashAdvancedReturnTransaction::class);
    }

    public function checkInvoice($invoice_parent)
    {
        $total = InvoicePayment::where('invoice_model', $invoice_parent->model_reference)
            ->where('invoice_id', $invoice_parent->reference_id)
            ->sum('amount_to_receive');

        $paid = InvoicePayment::where('invoice_model', $invoice_parent->model_reference)
            ->where('invoice_id', $invoice_parent->reference_id)
            ->sum('receive_amount');

        $invoice = $invoice_parent->reference_model::find($invoice_parent->reference_id);

        $total = number_format($total, 2, '.', '');
        $paid = number_format($paid, 2, '.', '');

        if ($paid == 0) {
            $invoice->payment_status = "unpaid";
        } elseif ($paid > 0 && $paid < $total) {
            $invoice->payment_status = "partial-paid";
        } elseif ($paid  == $total) {
            $invoice->payment_status = "paid";
        } else {
            $invoice->payment_status = "partial-paid";
        }

        $invoice->save();
    }

    public function checkSupplierInvoice($supplier_invoice_parent)
    {
        $total = SupplierInvoicePayment::where('supplier_invoice_model', $supplier_invoice_parent->model_reference)
            ->where('supplier_invoice_id', $supplier_invoice_parent->reference_id)
            ->sum('amount_to_pay');

        $paid = SupplierInvoicePayment::where('supplier_invoice_model', $supplier_invoice_parent->model_reference)
            ->where('supplier_invoice_id', $supplier_invoice_parent->reference_id)
            ->sum('pay_amount');

        $supplier_invoice = $supplier_invoice_parent->model_reference::find($supplier_invoice_parent->reference_id);

        $total = number_format($total, 2, '.', '');
        $paid = number_format($paid, 2, '.', '');

        if ($paid == 0) {
            $supplier_invoice->payment_status = "unpaid";
        } elseif ($paid > 0 && $paid < $total) {
            $supplier_invoice->payment_status = "partial-paid";
        } elseif ($paid == $total) {
            $supplier_invoice->payment_status = "paid";
        } else {
            $supplier_invoice->payment_status = "unpaid";
        }

        $supplier_invoice->save();
    }
}
