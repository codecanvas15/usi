<?php

namespace App\Models;

use App\Http\Helpers\JournalHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class AccountPayable extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id',
        'fund_submission_id',
        'project_id',
        'vendor_id',
        'coa_id',
        'currency_id',
        'supplier_invoice_currency_id',
        'exchange_rate',
        'code',
        'date',
        'total',
        'exchange_rate_gap_total',
        'status',
        'reject_reason',
        'created_by',
        'note',
        'change_bank_reason',
        'customer_id',
    ];

    protected $appends = ['bank_code_mutation'];

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
            'fund_submission_id' => 'required',
            'vendor_id' => 'required',
            'coa_id' => 'required',
            'currency_id' => 'required',
            'supplier_invoice_currency_id' => 'required',
            'exchange_rate' => 'required',
            'date' => 'required',
            'total' => 'required',
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
            if (is_null($model->code)) {
                $branch = Branch::find($model->branch_id);
                $model->code = generate_code(AccountPayable::class, 'code', 'date', 'AP', branch_sort: $branch->sort ?? null);
            }
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
                    $journal = new JournalHelpers('account-payable', $model->id);
                    $journal->generate();

                    foreach ($model->account_payable_details as $key => $detail) {
                        if (count($detail->account_payable_detail_lpbs ?? []) > 0) {
                            foreach ($detail->account_payable_detail_lpbs as $key => $lpb) {
                                $pay_amount = $lpb->amount_foreign;
                                if ($lpb->amount_foreign > 0 || $detail->amount_gap_foreign != 0) {
                                    if ($detail->is_clearing && $detail->amount_gap_foreign != 0) {
                                        $lpb_gap = $lpb->outstanding - $lpb->amount_foreign;
                                    }

                                    //LPB Payment
                                    if ($pay_amount != 0) {
                                        SupplierInvoicePayment::create([
                                            'model' => AccountPayableDetail::class,
                                            'reference_id' => $detail->id,
                                            'item_receiving_report_id' => $lpb->item_receiving_report_id,
                                            'supplier_invoice_model' => $detail->supplier_invoice_parent->model_reference,
                                            'supplier_invoice_id' => $detail->supplier_invoice_parent->reference_id,
                                            'currency_id' => $model->supplier_invoice_currency_id,
                                            'exchange_rate' => $detail->supplier_invoice_parent->exchange_rate,
                                            'date' => $model->date,
                                            'amount_to_pay' => 0,
                                            'pay_amount' => $pay_amount,
                                            'note' => "Account Payable / $detail->note",
                                        ]);
                                    }

                                    if ($detail->is_clearing && $detail->amount_gap_foreign != 0 && $lpb_gap != 0) {
                                        if ($lpb_gap > 0) {
                                            $note = "Kurang Bayar / $detail->clearing_note";
                                        } else {
                                            $note = "Lebih Bayar / $detail->clearing_note";
                                        }
                                        //CREATE OR UPDATE INVOICE PAYMENT
                                        SupplierInvoicePayment::create([
                                            'model' => AccountPayableDetail::class,
                                            'reference_id' => $detail->id,
                                            'item_receiving_report_id' => $lpb->item_receiving_report_id,
                                            'supplier_invoice_model' => $detail->supplier_invoice_parent->model_reference,
                                            'supplier_invoice_id' => $detail->supplier_invoice_parent->reference_id,
                                            'currency_id' => $model->supplier_invoice_currency_id,
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
                                'model' => AccountPayableDetail::class,
                                'reference_id' => $detail->id,
                                'supplier_invoice_model' => $detail->supplier_invoice_parent->model_reference,
                                'supplier_invoice_id' => $detail->supplier_invoice_parent->reference_id,
                                'currency_id' => $model->supplier_invoice_currency_id,
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
                                    'model' => AccountPayableDetail::class,
                                    'reference_id' => $detail->id,
                                    'supplier_invoice_model' => $detail->supplier_invoice_parent->model_reference,
                                    'supplier_invoice_id' => $detail->supplier_invoice_parent->reference_id,
                                    'currency_id' => $model->supplier_invoice_currency_id,
                                    'exchange_rate' => $model->exchange_rate,
                                    'date' => $model->date,
                                    'amount_to_pay' => 0,
                                    'pay_amount' => $gap,
                                    'note' => $note,
                                ]);
                            }
                        }
                    }

                    foreach ($model->account_payable_customers as $key => $detail) {
                        //CREATE OR UPDATE INVOICE PAYMENT
                        InvoicePayment::updateOrCreate([
                            'model' => AccountPayableCustomer::class,
                            'reference_id' => $detail->id,
                        ], [
                            'model' => AccountPayableCustomer::class,
                            'reference_id' => $detail->id,
                            'invoice_model' => $detail->invoice_parent->model_reference,
                            'invoice_id' => $detail->invoice_parent->reference_id,
                            'currency_id' => $model->supplier_invoice_currency_id,
                            'exchange_rate' => $detail->invoice_parent->exchange_rate,
                            'date' => $model->date,
                            'amount_to_receive' => 0,
                            'receive_amount' => $detail->total_foreign,
                            'note' => "Receivables Payment - $model->code / $detail->note",
                        ]);

                        $model->checkInvoice($detail->invoice_parent);
                    }

                    foreach ($model->account_payable_purchase_returns as $key => $detail) {
                        PurchaseReturnHistory::create([
                            'purchase_return_id' => $detail->purchase_return_id,
                            'date' => $model->date,
                            'reference_parent_model' => AccountPayable::class,
                            'reference_parent_id' => $model->id,
                            'reference_model' => AccountPayablePurchaseReturn::class,
                            'reference_id' => $detail->id,
                            'amount' => $detail->amount_foreign,
                            'status' => 'approve',
                        ]);
                    }


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
                } else {
                    $journals = Journal::where('reference_id', $model->id)
                        ->where('reference_model', AccountPayable::class)
                        ->get();

                    foreach ($journals as $journal) {
                        $journal->delete();
                        $journal->journal_details->each(function ($detail) {
                            $detail->delete();
                        });
                    }

                    SupplierInvoicePayment::where('model', AccountPayableDetail::class)
                        ->whereIn('reference_id', $model->account_payable_details->pluck('id'))
                        ->delete();

                    foreach ($model->account_payable_details as $key => $account_payable_detail) {
                        $model->checkSupplierInvoice($account_payable_detail->supplier_invoice_parent);
                    }

                    InvoicePayment::where('model', AccountPayableCustomer::class)
                        ->whereIn('reference_id', $model->account_payable_customers->pluck('id'))
                        ->delete();

                    PurchaseReturnHistory::where('reference_parent_model', AccountPayable::class)
                        ->where('reference_parent_id', $model->id)
                        ->delete();

                    foreach ($model->account_payable_customers as $key => $account_payable_customer) {
                        $model->checkInvoice($account_payable_customer->invoice_parent);
                    }

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
                ->where('reference_model', AccountPayable::class)
                ->get();

            foreach ($journals as $journal) {
                $journal->delete();
                $journal->journal_details->each(function ($detail) {
                    $detail->delete();
                });
            }

            SupplierInvoicePayment::where('model', AccountPayableDetail::class)
                ->whereIn('reference_id', $model->account_payable_details->pluck('id'))
                ->delete();

            foreach ($model->account_payable_details as $key => $account_payable_detail) {
                $model->checkSupplierInvoice($account_payable_detail->supplier_invoice_parent);
            }

            InvoicePayment::where('model', AccountPayableCustomer::class)
                ->whereIn('reference_id', $model->account_payable_customers->pluck('id'))
                ->delete();

            foreach ($model->account_payable_customers as $key => $account_payable_customer) {
                $model->checkInvoice($account_payable_customer->invoice_parent);
            }

            PurchaseReturnHistory::where('reference_parent_model', AccountPayable::class)
                ->where('reference_parent_id', $model->id)
                ->delete();

            BankCodeMutation::where('ref_model', AccountPayable::class)
                ->where('ref_id', $model->id)
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
        });
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
            $supplier_invoice->payment_status = "partial-paid";
        }

        $supplier_invoice->save();
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
        return $this->belongsTo(Branch::class);
    }

    public function fund_submission()
    {
        return $this->belongsTo(FundSubmission::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function supplier_invoice_currency()
    {
        return $this->belongsTo(Currency::class, 'supplier_invoice_currency_id');
    }

    public function account_payable_details()
    {
        return $this->hasMany(AccountPayableDetail::class, 'account_payable_id');
    }

    public function account_payable_others()
    {
        return $this->hasMany(AccountPayableOther::class, 'account_payable_id');
    }

    public function account_payable_customers()
    {
        return $this->hasMany(AccountPayableCustomer::class, 'account_payable_id');
    }

    public function account_payable_purchase_returns()
    {
        return $this->hasMany(AccountPayablePurchaseReturn::class, 'account_payable_id');
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
        return BankCodeMutation::where('ref_model', AccountPayable::class)
            ->where('ref_id', $this->id)
            ->first()->code ?? '';
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
