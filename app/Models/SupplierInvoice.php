<?php

namespace App\Models;

use App\Http\Helpers\JournalHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SupplierInvoice extends Model
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
        'vendor_id',
        'currency_id',
        'exchange_rate',
        'branch_id',
        'code',
        'reference',
        'tax_reference',
        'date',
        'accepted_doc_date',
        'exchange_rate',
        'term_of_payment',
        'top_days',
        'top_due_date',
        'sub_total',
        'tax_total',
        'grand_total',
        'approved_by',
        'status',
        'payment_status',
        'receipt_status',
        'file',
        'tax_file',
        'po_reference_id',
        'po_reference_model',
        'po_reference_kode'
    ];

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
        $activity_logs = ActivityLog::where('subject_type', SupplierInvoice::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', SupplierInvoice::class)
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
            'vendor_id' => 'required',
            'branch_id' => 'required',
            'reference' => 'required',
            'tax_reference' => 'nullable',
            'date' => 'required|date',
            'accepted_doc_date' => 'required|date',
            'exchange_rate' => 'required',
            'term_of_payment' => 'nullable',
            'top_days' => 'required_if:term_of_payment,by days',
            'top_due_date' => 'required_if:term_of_payment,by days',
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

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function detail()
    {
        return $this->hasMany(SupplierInvoiceDetail::class);
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        self::creating(function ($model) {
            if (is_null($model->code)) {
                $branch = Branch::find($model->branch_id);
                $model->code = generate_code(SupplierInvoice::class, 'code', 'date', 'PI', branch_sort: $branch->sort ?? null, date: $model->date);
            }
        });

        self::created(function ($model) {
            //CREATE OR UPDATE INVOICE PARENT
            SupplierInvoiceParent::updateOrCreate(
                [
                    'model_reference' => SupplierInvoice::class,
                    'reference_id' => $model->id,
                ],
                [
                    'model_reference' => SupplierInvoice::class,
                    'reference_id' => $model->id,
                    'branch_id' => $model->branch_id,
                    'vendor_id' => $model->vendor_id,
                    'currency_id' => $model->currency_id,
                    'exchange_rate' => $model->exchange_rate,
                    'date' => $model->date,
                    'due_date' => $model->top_due_date,
                    'type' => 'trading',
                    'code' => $model->code,
                    'reference' => $model->reference,
                    'tax_reference' => $model->tax_reference,
                    'total' => $model->grand_total,
                    'status' => $model->status,
                    'payment_status' => $model->payment_status,
                ]
            );
        });

        static::updating(function ($model) {
            // * model date in dirty
            // if ($model->isDirty('date')) {
            // $branch = Branch::find($model->branch_id);
            // $model->code = generate_code(SupplierInvoice::class, 'code', 'accepted_doc_date', 'PI', branch_sort: $branch->sort ?? null, date: $model->accepted_doc_date);
            // }

            // * model status in dirty
            if ($model->isDirty('status')) {
                // ! APPROVE =========================================================================================================
                if ($model->status == 'approve') {
                    //CREATE OR UPDATE INVOICE PAYMENT
                    SupplierInvoicePayment::updateOrCreate([
                        'supplier_invoice_model' => SupplierInvoice::class,
                        'supplier_invoice_id' => $model->id,
                        'model' => SupplierInvoice::class,
                        'reference_id' => $model->id,
                    ], [
                        'supplier_invoice_model' => SupplierInvoice::class,
                        'supplier_invoice_id' => $model->id,
                        'model' => SupplierInvoice::class,
                        'reference_id' => $model->id,
                        'currency_id' => $model->currency_id,
                        'exchange_rate' => $model->exchange_rate,
                        'date' => $model->date,
                        'amount_to_pay' => $model->grand_total,
                        'pay_amount' => 0,
                        'note' => "Invoice - $model->reference",
                    ]);

                    if ($model->supplier_invoice_down_payments) {
                        $journal = new JournalHelpers('supplier-invoice', $model->id);
                        $journal->generate();
                    }

                    // * update item receiving report
                    try {
                        $ids = $model->detail->pluck('item_receiving_report_id');
                        ItemReceivingReport::whereIn('id', $ids)
                            ->where('status', 'approve')
                            ->update([
                                'status' => 'done',
                            ]);
                    } catch (\Throwable $th) {
                        throw $th;
                    }
                }
                // ! APPROVE =========================================================================================================

                // ! VOID REVERT REJECT =========================================================================================================
                if (in_array($model->status, ['void', 'revert', 'reject'])) {
                    // * update item receiving report
                    try {
                        $ids = $model->detail->pluck('item_receiving_report_id');
                        ItemReceivingReport::whereIn('id', $ids)
                            ->where('status', 'done')
                            ->update([
                                'status' => 'approve',
                            ]);

                        Journal::where('reference_model', SupplierInvoice::class)
                            ->where('reference_id', $model->id)
                            ->delete();

                        ItemReceivingReportTax::where('reference_parent_model', SupplierInvoice::class)
                            ->where('reference_parent_id', $model->id)
                            ->delete();

                        if ($model->supplier_invoice_down_payments) {
                            $model->supplier_invoice_down_payments->map(function ($item) {
                                $item->cash_advance_payment->update([
                                    'returned_amount' => 0
                                ]);
                            });
                        }

                        SupplierInvoicePayment::where('supplier_invoice_model', SupplierInvoice::class)
                            ->where('supplier_invoice_id', $model->id)
                            ->delete();
                    } catch (\Throwable $th) {
                        throw $th;
                    }
                }
                // ! VOID REVERT REJECT =========================================================================================================

                //CREATE OR UPDATE INVOICE PARENT
                SupplierInvoiceParent::updateOrCreate(
                    [
                        'model_reference' => SupplierInvoice::class,
                        'reference_id' => $model->id,
                    ],
                    [
                        'model_reference' => SupplierInvoice::class,
                        'reference_id' => $model->id,
                        'branch_id' => $model->branch_id,
                        'vendor_id' => $model->vendor_id,
                        'currency_id' => $model->currency_id,
                        'exchange_rate' => $model->exchange_rate,
                        'date' => $model->date,
                        'due_date' => $model->top_due_date,
                        'type' => 'trading',
                        'code' => $model->code,
                        'reference' => $model->reference,
                        'tax_reference' => $model->tax_reference,
                        'total' => $model->grand_total,
                        'status' => $model->status,
                        'payment_status' => $model->payment_status,
                    ]
                );
            }

            if ($model->isDirty('payment_status')) {
                //CREATE OR UPDATE INVOICE PARENT
                SupplierInvoiceParent::updateOrCreate(
                    [
                        'model_reference' => SupplierInvoice::class,
                        'reference_id' => $model->id,
                    ],
                    [
                        'model_reference' => SupplierInvoice::class,
                        'reference_id' => $model->id,
                        'branch_id' => $model->branch_id,
                        'vendor_id' => $model->vendor_id,
                        'currency_id' => $model->currency_id,
                        'exchange_rate' => $model->exchange_rate,
                        'date' => $model->date,
                        'due_date' => $model->top_due_date,
                        'type' => 'trading',
                        'code' => $model->code,
                        'reference' => $model->reference,
                        'tax_reference' => $model->tax_reference,
                        'total' => $model->grand_total,
                        'status' => $model->status,
                        'payment_status' => $model->payment_status,
                    ]
                );
            }
        });

        static::deleted(function ($model) {
            //DELETE INVOICE PAYMENT
            SupplierInvoicePayment::where('supplier_invoice_model', SupplierInvoice::class)
                ->where('supplier_invoice_id', $model->id)
                ->delete();

            // DELETE PARENT
            SupplierInvoiceParent::where('model_reference', SupplierInvoice::class)
                ->where('reference_id', $model->id)->delete();

            Journal::where('reference_model', SupplierInvoice::class)
                ->where('reference_id', $model->id)
                ->delete();

            if ($model->supplier_invoice_down_payments) {
                $model->supplier_invoice_down_payments->map(function ($item) {
                    $item->cash_advance_payment->update([
                        'returned_amount' => 0
                    ]);
                });
            }
        });
    }

    public function supplier_invoice_payment()
    {
        return SupplierInvoicePayment::where('supplier_invoice_model', SupplierInvoice::class)
            ->where('supplier_invoice_id', $this->id)
            ->with('item_receiving_report')
            ->get();
    }

    public function supplier_invoice_tax_summaries()
    {
        return $this->hasMany(SupplierInvoiceTaxSummary::class);
    }

    public function getModelPermissionAttribute()
    {
        $edit = true;
        $revert = true;
        $delete = true;
        $void = true;

        $parent = SupplierInvoiceParent::where('model_reference', SupplierInvoice::class)
            ->where('reference_id', $this->id)
            ->first();

        $check_fund_submissions = FundSubmissionSupplierDetail::where('supplier_invoice_parent_id', $parent->id)
            ->join('fund_submissions', 'fund_submissions.id', '=', 'fund_submission_supplier_details.fund_submission_id')
            ->groupBy('fund_submissions.status')
            ->get();

        $check_has_submission = $check_fund_submissions->count();
        $check_processed_submission = $check_fund_submissions->whereIn('status', ['pending', 'approve'])->count();

        $delete = $check_has_submission > 0 ? false : true;
        $void = $check_processed_submission > 0 ? false : true;
        $revert = $check_processed_submission > 0 ? false : true;
        $edit = $check_processed_submission > 0 ? false : true;

        return [
            'edit' => $edit,
            'revert' => $revert,
            'delete' => $delete,
            'void' => $void,
        ];
    }

    public function supplier_invoice_down_payments()
    {
        return $this->hasMany(SupplierInvoiceDownPayment::class);
    }

    public function supplier_invoice_parent()
    {
        return SupplierInvoiceParent::where('model_reference', SupplierInvoice::class)
            ->where('reference_id', $this->id)
            ->first();
    }
}
