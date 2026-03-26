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

class ReceivablesPayment extends Model
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
        'code',
        'branch_id',
        'coa_id',
        'date',
        'customer_id',
        'project_id',
        'currency_id',
        'invoice_currency_id',
        'exchange_rate',
        'receive_payment_id',
        'reference',
        'vendor_id'
    ];

    protected $append = [
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
        $activity_logs = ActivityLog::where('subject_type', ReceivablesPayment::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', ReceivablesPayment::class)
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
            'branch_id' => 'required',
            'date' => 'required',
            'customer_id' => 'required',
            'project_id' => 'required',
            'currency_id' => 'required',
            'exchange_rate' => 'required',
            'reference' => 'required',
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

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!checkAvailableDate($model->date)) {
                throw new \Exception('Tanggal tidak tersedia / sudah tutup buku');
            }

            $branch = Branch::find($model->branch_id);
            $model->code = generate_code(ReceivablesPayment::class, 'code', 'date', 'RVP', branch_sort: $branch->sort ?? null, date: $model->date);
            $model->status = "pending";
            $model->created_by = Auth::user()->id;
        });

        static::created(function ($model) {});

        static::updated(function ($model) {
            if ($model->getOriginal('status') != $model->status) {
                if ($model->status == 'approve') {
                    $journal = new JournalHelpers('receivables-payment', $model->id);
                    $journal->generate();

                    foreach ($model->receivables_payment_details as $key => $detail) {
                        //CREATE OR UPDATE INVOICE PAYMENT
                        InvoicePayment::updateOrCreate([
                            'model' => ReceivablesPaymentDetail::class,
                            'reference_id' => $detail->id,
                        ], [
                            'model' => ReceivablesPaymentDetail::class,
                            'reference_id' => $detail->id,
                            'invoice_model' => $detail->invoice_parent->model_reference,
                            'invoice_id' => $detail->invoice_parent->reference_id,
                            'currency_id' => $model->invoice_currency_id,
                            'exchange_rate' => $detail->invoice_parent->exchange_rate,
                            'date' => $model->date,
                            'amount_to_receive' => 0,
                            'receive_amount' => $detail->total_foreign,
                            'note' => "Receivables Payment - $model->code / $detail->note",
                        ]);

                        $model->checkInvoice($detail->invoice_parent);
                    }

                    foreach ($model->receivables_payment_vendors as $key => $detail) {
                        if (count($detail->receivables_payment_vendor_lpbs ?? []) > 0) {
                            foreach ($detail->receivables_payment_vendor_lpbs as $key => $lpb) {
                                $pay_amount = $lpb->amount_foreign;
                                if ($lpb->amount_foreign > 0) {
                                    if ($detail->is_clearing && $detail->amount_gap_foreign != 0) {
                                        $lpb_gap = $lpb->outstanding - $lpb->amount_foreign;
                                    }

                                    //LPB Payment
                                    SupplierInvoicePayment::create([
                                        'model' => ReceivablesPaymentVendor::class,
                                        'reference_id' => $detail->id,
                                        'item_receiving_report_id' => $lpb->item_receiving_report_id,
                                        'supplier_invoice_model' => $detail->supplier_invoice_parent->model_reference,
                                        'supplier_invoice_id' => $detail->supplier_invoice_parent->reference_id,
                                        'currency_id' => $model->invoice_currency_id,
                                        'exchange_rate' => $detail->supplier_invoice_parent->exchange_rate,
                                        'date' => $model->date,
                                        'amount_to_pay' => 0,
                                        'pay_amount' => $pay_amount,
                                        'note' => "Account Payable / $detail->note",
                                    ]);

                                    if ($detail->is_clearing && $detail->amount_gap_foreign != 0 && $lpb_gap != 0) {
                                        if ($lpb_gap > 0) {
                                            $note = "Kurang Bayar / $detail->clearing_note";
                                        } else {
                                            $note = "Lebih Bayar / $detail->clearing_note";
                                        }
                                        //CREATE OR UPDATE INVOICE PAYMENT
                                        SupplierInvoicePayment::create([
                                            'model' => ReceivablesPaymentVendor::class,
                                            'reference_id' => $detail->id,
                                            'item_receiving_report_id' => $lpb->item_receiving_report_id,
                                            'supplier_invoice_model' => $detail->supplier_invoice_parent->model_reference,
                                            'supplier_invoice_id' => $detail->supplier_invoice_parent->reference_id,
                                            'currency_id' => $model->invoice_currency_id,
                                            'exchange_rate' => $model->exchange_rate,
                                            'date' => $model->date,
                                            'amount_to_pay' => 0,
                                            'pay_amount' => $lpb_gap,
                                            'note' => $note,
                                        ]);
                                    }
                                }
                            }
                        } else {
                            //CREATE OR UPDATE INVOICE PAYMENT
                            SupplierInvoicePayment::create([
                                'model' => ReceivablesPaymentVendor::class,
                                'reference_id' => $detail->id,
                                'supplier_invoice_model' => $detail->supplier_invoice_parent->model_reference,
                                'supplier_invoice_id' => $detail->supplier_invoice_parent->reference_id,
                                'currency_id' => $model->invoice_currency_id,
                                'exchange_rate' => $detail->supplier_invoice_parent->exchange_rate,
                                'date' => $model->date,
                                'amount_to_pay' => 0,
                                'pay_amount' => $detail->amount_foreign,
                                'note' => "Account Payable - $model->code / $detail->note",
                            ]);

                            if ($detail->is_clearing && $detail->amount_gap_foreign != 0) {
                                $gap = $detail->outstanding_amount - $detail->amount_foreign;
                            }

                            if ($detail->is_clearing && $detail->amount_gap_foreign != 0 && $gap != 0) {
                                if ($gap > 0) {
                                    $note = "Kurang Bayar / $detail->clearing_note";
                                } else {
                                    $note = "Lebih Bayar / $detail->clearing_note";
                                }
                                //CREATE OR UPDATE INVOICE PAYMENT
                                SupplierInvoicePayment::create([
                                    'model' => ReceivablesPaymentVendor::class,
                                    'reference_id' => $detail->id,
                                    'supplier_invoice_model' => $detail->supplier_invoice_parent->model_reference,
                                    'supplier_invoice_id' => $detail->supplier_invoice_parent->reference_id,
                                    'currency_id' => $model->invoice_currency_id,
                                    'exchange_rate' => $model->exchange_rate,
                                    'date' => $model->date,
                                    'amount_to_pay' => 0,
                                    'pay_amount' => $gap,
                                    'note' => $note,
                                ]);
                            }
                        }

                        $model->checkSupplierInvoice($detail->supplier_invoice_parent);
                    }

                    foreach ($model->receivables_payment_invoice_returns as $key => $detail) {
                        InvoiceReturnHistory::create([
                            'invoice_return_id' => $detail->invoice_return_id,
                            'date' => $model->date,
                            'reference_parent_model' => ReceivablesPayment::class,
                            'reference_parent_id' => $model->id,
                            'reference_model' => ReceivablesPaymentInvoiceReturn::class,
                            'reference_id' => $detail->id,
                            'amount' => $detail->amount_foreign,
                            'status' => 'approve',
                        ]);
                    }

                    if ($model->receive_payment) {
                        $receive_payment = $model->receive_payment;
                        $receive_payment->realization_date = Carbon::parse($model->date);
                        $receive_payment->save();
                    }
                } else {
                    $journals = Journal::where('reference_id', $model->id)
                        ->where('reference_model', ReceivablesPayment::class)
                        ->get();

                    foreach ($journals as $journal) {
                        $journal->delete();
                        $journal->journal_details->each(function ($detail) {
                            $detail->delete();
                        });
                    }

                    InvoiceTax::where('reference_model', ReceivablesPayment::class)
                        ->where('reference_id', $model->id)
                        ->delete();

                    foreach ($model->receivables_payment_details as $key => $receivables_payment_detail) {
                        InvoicePayment::where('model', ReceivablesPaymentDetail::class)
                            ->where('reference_id', $receivables_payment_detail->id)
                            ->delete();

                        $model->checkInvoice($receivables_payment_detail->invoice_parent);
                    }

                    foreach ($model->receivables_payment_vendors as $key => $receivables_payment_vendor) {
                        SupplierInvoicePayment::where('model', ReceivablesPaymentVendor::class)
                            ->where('reference_id', $receivables_payment_vendor->id)
                            ->delete();

                        $model->checkSupplierInvoice($receivables_payment_vendor->supplier_invoice_parent);
                    }

                    InvoiceReturnHistory::where('reference_parent_model', ReceivablesPayment::class)
                        ->where('reference_parent_id', $model->id)
                        ->delete();

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
                ->where('reference_model', ReceivablesPayment::class)
                ->get();

            foreach ($journals as $journal) {
                $journal->delete();
                $journal->journal_details->each(function ($detail) {
                    $detail->delete();
                });
            }

            SupplierInvoicePayment::where('model', ReceivablesPaymentVendor::class)
                ->whereIn('reference_id', $model->receivables_payment_vendors->pluck('id'))
                ->delete();

            foreach ($model->receivables_payment_vendors as $key => $receivables_payment_vendor) {
                $model->checkSupplierInvoice($receivables_payment_vendor->supplier_invoice_parent);
            }

            InvoicePayment::where('model', ReceivablesPaymentDetail::class)
                ->whereIn('reference_id', $model->receivables_payment_details->pluck('id'))
                ->delete();

            foreach ($model->receivables_payment_details as $key => $receivables_payment_detail) {
                $model->checkInvoice($receivables_payment_detail->supplier_invoice_parent);
            }

            BankCodeMutation::where('ref_model', ReceivablesPayment::class)
                ->where('ref_id', $model->id)
                ->delete();

            InvoiceReturnHistory::where('reference_parent_model', ReceivablesPayment::class)
                ->delete();

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

    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    public function project()
    {
        return $this->belongsTo(Project::class)->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    public function invoice_currency()
    {
        return $this->belongsTo(Currency::class, 'invoice_currency_id');
    }

    public function receivables_payment_details()
    {
        return $this->hasMany(ReceivablesPaymentDetail::class, 'receivables_payment_id');
    }

    public function receivables_payment_others()
    {
        return $this->hasMany(ReceivablesPaymentOther::class, 'receivables_payment_id');
    }

    public function receive_payment()
    {
        return $this->belongsTo(ReceivePayment::class);
    }

    public function receivables_payment_vendors()
    {
        return $this->hasMany(ReceivablesPaymentVendor::class, 'receivables_payment_id');
    }

    public function receivables_payment_invoice_returns()
    {
        return $this->hasMany(ReceivablesPaymentInvoiceReturn::class, 'receivables_payment_id');
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function checkInvoice($invoice_parent)
    {
        if ($invoice_parent) {
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
    }

    public function getBankCodeMutationAttribute()
    {
        return BankCodeMutation::where('ref_model', ReceivablesPayment::class)
            ->where('ref_id', $this->id)
            ->first()->code ?? '';
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class)->withTrashed();
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
