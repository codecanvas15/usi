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

class InvoiceDownPayment extends Model
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
        'customer_id',
        'currency_id',
        'bank_internal_id',
        'exchange_rate',
        'sale_order_model',
        'sale_order_model_id',
        'date',
        'due_date',
        'code',
        'total_amount',
        'down_payment',
        'tax_total',
        'tax_number',
        'tax_attachment',
        'grand_total',
        'payment_status',
        'status',
        'note',
        'created_by'
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
     * validation rules
     *
     * @param  string  $method
     * @param  int  $id
     * @return array
     */
    public static function rules($method = 'create', $id = null)
    {
        $validate = [];

        // * if has unique validation or diff rules when create or update
        if ($method == 'create') {
            $validate = array_merge($validate, []);
        } else {
            $validate = array_merge($validate, []);
        }

        return $validate;
    }

    public function approved_by_user()
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    /**
     * set attributes model
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
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        self::creating(function ($model) {
            if (is_null($model->status)) {
                $model->status = 'pending';
            }

            if (is_null($model->payment_status)) {
                $model->payment_status = 'unpaid';
            }
            $model->created_by = auth()->user()->id;
        });

        self::created(function ($model) {
            //CREATE OR UPDATE INVOICE PARENT
            InvoiceParent::updateOrCreate(
                [
                    'model_reference' => InvoiceDownPayment::class,
                    'reference_id' => $model->id,
                ],
                [
                    'model_reference' => InvoiceDownPayment::class,
                    'reference_id' => $model->id,
                    'branch_id' => $model->branch_id,
                    'customer_id' => $model->customer_id,
                    'currency_id' => $model->currency_id,
                    'exchange_rate' => $model->exchange_rate,
                    'date' => $model->date,
                    'due_date' => $model->due_date,
                    'type' => 'down_payment',
                    'code' => $model->code,
                    'total' => $model->grand_total,
                    'status' => $model->status,
                    'payment_status' => $model->payment_status,
                ]
            );
        });

        self::updated(function ($model) {
            // ! IF STATUS IS CHANGED #####################################
            if ($model->getOriginal('status') != $model->status) {
                if ($model->status == 'approve') {

                    foreach ($model->invoice_down_payment_taxes as $key => $invoice_down_payment_tax) {
                        if ($invoice_down_payment_tax->tax->type == 'ppn') {
                            $invoice_tax = new InvoiceTax();
                            $invoice_tax->loadModel(
                                [
                                    'reference_model' => InvoiceDownPayment::class,
                                    'reference_id' => $model->id,
                                    'reference_parent_model' => InvoiceDownPayment::class,
                                    'reference_parent_id' => $model->id,
                                    'date' => Carbon::parse($model->date),
                                    'customer_id' => $model->customer_id,
                                    'tax_id' => $invoice_down_payment_tax->tax_id,
                                    'dpp' => ($model->down_payment * $model->exchange_rate),
                                    'value' => $invoice_down_payment_tax->value,
                                    'amount' => ($invoice_down_payment_tax->amount * $model->exchange_rate),
                                ]
                            );
                            $invoice_tax->save();
                        }
                    }

                    //CREATE OR UPDATE INVOICE PAYMENT
                    InvoicePayment::updateOrCreate([
                        'invoice_model' => InvoiceDownPayment::class,
                        'invoice_id' => $model->id,
                    ], [
                        'invoice_model' => InvoiceDownPayment::class,
                        'invoice_id' => $model->id,
                        'currency_id' => $model->currency_id,
                        'exchange_rate' => $model->exchange_rate,
                        'model' => InvoiceDownPayment::class,
                        'reference_id' => $model->id,
                        'date' => $model->date,
                        'amount_to_receive' => $model->grand_total,
                        'receive_amount' => 0,
                        'note' => "Invoice - $model->code",
                    ]);
                }

                // * if revert, void, cancel
                if (in_array($model->status, ['revert', 'void', 'cancel'])) {
                    //DELETE INVOICE TAX
                    InvoiceTax::where('reference_parent_model', InvoiceDownPayment::class)
                        ->where('reference_parent_id', $model->id)
                        ->delete();

                    // * delete journal
                    if (in_array($model->status, ['void', 'cancel', 'reject'])) {
                        //DELETE INVOICE PAYMENT
                        InvoicePayment::where('invoice_model', InvoiceDownPayment::class)
                            ->where('invoice_id', $model->id)
                            ->delete();
                    }
                }
            }

            //CREATE OR UPDATE INVOICE PARENT
            InvoiceParent::updateOrCreate(
                [
                    'model_reference' => InvoiceDownPayment::class,
                    'reference_id' => $model->id,
                ],
                [
                    'model_reference' => InvoiceDownPayment::class,
                    'reference_id' => $model->id,
                    'branch_id' => $model->branch_id,
                    'customer_id' => $model->customer_id,
                    'currency_id' => $model->currency_id,
                    'exchange_rate' => $model->exchange_rate,
                    'date' => $model->date,
                    'due_date' => $model->due_date,
                    'type' => 'down_payment',
                    'code' => $model->code,
                    'total' => $model->grand_total,
                    'status' => $model->status,
                    'payment_status' => $model->payment_status,
                ]
            );
            // ! end IF STATUS IS CHANGED #################################
        });

        self::deleted(function ($model) {
            InvoiceParent::where('model_reference', InvoiceDownPayment::class)
                ->where('reference_id', $model->id)
                ->delete();

            // * DELETE INVOICE PAYMENT
            InvoicePayment::where('invoice_model', InvoiceDownPayment::class)
                ->where('invoice_id', $model->id)
                ->delete();
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
     * Get the branch that owns the InvoiceDownPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the customer that owns the InvoiceDownPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    /**
     * Get the currency that owns the InvoiceDownPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the bank_internal that owns the InvoiceDownPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bank_internal(): BelongsTo
    {
        return $this->belongsTo(BankInternal::class)->withTrashed();
    }

    /**
     * Get all of the invoice_down_payment_taxes for the InvoiceDownPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoice_down_payment_taxes(): HasMany
    {
        return $this->hasMany(InvoiceDownPaymentTax::class);
    }

    public function sale_order_reference()
    {
        return $this->belongsTo($this->sale_order_model, 'sale_order_model_id', 'id');
    }


    public function invoice_payment()
    {
        return InvoicePayment::where('invoice_model', InvoiceDownPayment::class)
            ->where('invoice_id', $this->id)
            ->get();
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function down_payment_invoices()
    {
        return $this->hasMany(DownPaymentInvoice::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function printedData()
    {
        return $this->hasMany(DocumentPrint::class, 'model_id', 'id')
            ->where('model', InvoiceDownPayment::class);
    }
}
