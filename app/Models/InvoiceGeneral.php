<?php

namespace App\Models;

use App\Http\Helpers\JournalHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class InvoiceGeneral extends Model
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
        'sale_order_general_id',
        'customer_id',
        'currency_id',
        'branch_id',
        'bank_internal_id',
        'bank_internal_ids',
        'exchange_rate',
        'code',
        'receipt_number',
        'reference',
        'date',
        'due_date',
        'due',
        'term_of_payments',
        'sub_total_main',
        'total_tax_main',
        'total_main',
        'sub_total_additional',
        'total_tax_additional',
        'total_additional',
        'total',
        'status',
        'payment_status',
        'created_by',
        'is_old',
        'is_printed',
        'so_references',
    ];

    protected $casts = [
        'is_printed' => 'array',
        'so_references' => 'array',
        'bank_internal_ids' => 'array',
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
            if (is_null($model->date)) {
                $model->date = Carbon::now()->format('Y-m-d');
            }

            if (is_null($model->due_date)) {
                $model->due_date = date('Y-m-d');
            }

            if (is_null($model->due)) {
                $model->due = 0;
            }

            if (is_null($model->status)) {
                $model->status = 'pending';
            }

            if (is_null($model->payment_status)) {
                $model->payment_status = 'unpaid';
            }

            if (!checkAvailableDate($model->date)) {
                throw new \Exception('Tanggal tidak tersedia / sudah tutup buku');
            }
        });

        self::created(function ($model) {
            //CREATE OR UPDATE INVOICE PARENT
            InvoiceParent::updateOrCreate(
                [
                    'model_reference' => InvoiceGeneral::class,
                    'reference_id' => $model->id,
                ],
                [
                    'model_reference' => InvoiceGeneral::class,
                    'reference_id' => $model->id,
                    'branch_id' => $model->branch_id,
                    'customer_id' => $model->customer_id,
                    'currency_id' => $model->currency_id,
                    'exchange_rate' => $model->exchange_rate,
                    'date' => $model->date,
                    'due_date' => $model->due_date,
                    'type' => 'general',
                    'code' => $model->code,
                    'total' => $model->total,
                    'status' => $model->status,
                    'payment_status' => $model->payment_status,
                ]
            );
        });

        self::updated(function ($model) {
            // ! IF STATUS IS CHANGED #####################################
            if ($model->getOriginal('status') != $model->status) {
                // * if approve create journal and update status delivery order general
                if ($model->status == 'approve') {
                    //CREATE OR UPDATE INVOICE PAYMENT
                    InvoicePayment::updateOrCreate([
                        'invoice_model' => InvoiceGeneral::class,
                        'invoice_id' => $model->id,
                        'model' => InvoiceGeneral::class,
                        'reference_id' => $model->id,
                    ], [
                        'invoice_model' => InvoiceGeneral::class,
                        'invoice_id' => $model->id,
                        'currency_id' => $model->currency_id,
                        'exchange_rate' => $model->exchange_rate,
                        'model' => InvoiceGeneral::class,
                        'reference_id' => $model->id,
                        'date' => $model->date,
                        'amount_to_receive' => $model->total,
                        'receive_amount' => 0,
                        'note' => "Invoice - $model->code",
                    ]);

                    // * create journal
                    $journal = new JournalHelpers('invoice-general', $model->id);
                    $journal->generate();


                    $delivery_order_generals = DeliveryOrderGeneral::whereHas('delivery_order_general_details', function ($d) use ($model) {
                        $d->whereIn('id', $model->invoice_general_details->pluck('delivery_order_general_detail_id')->toArray());
                    })
                        ->pluck('id')
                        ->toArray();
                }

                // * if revert, void, cancel
                if (in_array($model->status, ['revert', 'void', 'cancel', 'reject'])) {
                    // * update delivery order general
                    $model->invoice_general_details->map(function ($item) {
                        $item->delivery_order_general_detail->delivery_order_general->where("is_invoice_created", true)->update([
                            'is_invoice_created' => false,
                        ]);
                    });

                    // * revert journal
                    if (
                        $model->status == 'revert'
                    ) {
                        Journal::where('reference_model', InvoiceGeneral::class)
                            ->where('reference_id', $model->id)
                            ->delete();
                    }

                    //DELETE INVOICE TAX
                    InvoiceTax::where('reference_parent_model', InvoiceGeneral::class)
                        ->where('reference_parent_id', $model->id)
                        ->delete();

                    // * delete journal
                    if (in_array($model->status, ['void', 'cancel', 'reject'])) {
                        Journal::where('reference_model', InvoiceGeneral::class)
                            ->where('reference_id', $model->id)
                            ->delete();

                        //DELETE INVOICE PAYMENT
                        InvoicePayment::where('invoice_model', InvoiceGeneral::class)
                            ->where('invoice_id', $model->id)
                            ->delete();
                    }
                }
            }

            //CREATE OR UPDATE INVOICE PARENT
            InvoiceParent::updateOrCreate(
                [
                    'model_reference' => InvoiceGeneral::class,
                    'reference_id' => $model->id,
                ],
                [
                    'model_reference' => InvoiceGeneral::class,
                    'reference_id' => $model->id,
                    'branch_id' => $model->branch_id,
                    'customer_id' => $model->customer_id,
                    'currency_id' => $model->currency_id,
                    'exchange_rate' => $model->exchange_rate,
                    'date' => $model->date,
                    'due_date' => $model->due_date,
                    'type' => 'general',
                    'code' => $model->code,
                    'total' => $model->total,
                    'status' => $model->status,
                    'payment_status' => $model->payment_status,
                ]
            );
            // ! end IF STATUS IS CHANGED #################################

            // ! UPDATE SALES ORDER STATUS
            if ($model->sale_order_general_id) {
                $sale_order = $model->sale_order_general;
                $invoice = $sale_order->invoice_generals->where('status', 'approve')->count();
                $unpaid_invoice = $sale_order->invoice_generals->where('status', 'approve')->where('payment_status', 'unpaid')->count();
                $paid_invoice = $sale_order->invoice_generals->where('status', 'approve')->where('payment_status', 'paid')->count();
                $partial_invoice = $sale_order->invoice_generals->where('status', 'approve')->where('payment_status', 'partial-paid')->count();

                if ($invoice == 0) {
                    $sale_order->payment_status = 'unpaid';
                } elseif ($invoice > 0 && $paid_invoice == $invoice) {
                    $sale_order->payment_status = 'paid';
                } elseif ($invoice > 0 && $partial_invoice > 0) {
                    $sale_order->payment_status = 'partial-paid';
                } elseif ($invoice > 0 && $unpaid_invoice == $invoice) {
                    $sale_order->payment_status = 'unpaid';
                }
                $sale_order->save();
            } else {
                foreach ($model->invoice_general_details as $key => $value) {
                    $sale_order = $value->sale_order_general;
                    $invoice = $sale_order->invoice_generals->where('status', 'approve')->count();
                    $unpaid_invoice = $sale_order->invoice_generals->where('status', 'approve')->where('payment_status', 'unpaid')->count();
                    $paid_invoice = $sale_order->invoice_generals->where('status', 'approve')->where('payment_status', 'paid')->count();
                    $partial_invoice = $sale_order->invoice_generals->where('status', 'approve')->where('payment_status', 'partial-paid')->count();

                    if ($invoice == 0) {
                        $sale_order->payment_status = 'unpaid';
                    } elseif ($invoice > 0 && $paid_invoice == $invoice) {
                        $sale_order->payment_status = 'paid';
                    } elseif ($invoice > 0 && $partial_invoice > 0) {
                        $sale_order->payment_status = 'partial-paid';
                    } elseif ($invoice > 0 && $unpaid_invoice == $invoice) {
                        $sale_order->payment_status = 'unpaid';
                    }
                    $sale_order->save();
                }
            }
        });

        self::deleted(function ($model) {
            // * update delivery order general
            $model->invoice_general_details->map(function ($item) {
                $item->delivery_order_general_detail->delivery_order_general->where("is_invoice_created", true)->update([
                    'is_invoice_created' => false,
                ]);
            });

            // * DELETE INVOICE PAYMENT
            InvoicePayment::where('invoice_model', InvoiceGeneral::class)
                ->where('invoice_id', $model->id)
                ->delete();

            // * DELETE INVOICE JOURNAL
            Journal::where('reference_model', InvoiceGeneral::class)
                ->where('reference_id', $model->id)
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
     * Get delivery order generals code
     */
    public function getDeliveryOrderGeneralsCodeAttribute()
    {
        $delivery_order_generals = DeliveryOrderGeneral::whereHas('delivery_order_general_details', function ($d) {
            $d->whereIn('id', $this->invoice_general_details->pluck('delivery_order_general_detail_id')->toArray());
        })
            ->pluck('code');

        return implode(', ', $delivery_order_generals->toArray());
    }

    /**
     * Get the branch that owns the InvoiceGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the sale_order_general that owns the InvoiceGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale_order_general(): BelongsTo
    {
        return $this->belongsTo(SaleOrderGeneral::class);
    }

    /**
     * Get the delivery_order_general that owns the InvoiceGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    // public function delivery_order_general(): BelongsTo
    // {
    //     return $this->belongsTo(DeliveryOrderGeneral::class);
    // }

    /**
     * Get the customer that owns the InvoiceGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    /**
     * Get the currency that owns the InvoiceGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the bank_internal that owns the InvoiceGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bank_internal(): BelongsTo
    {
        return $this->belongsTo(BankInternal::class)->withTrashed();
    }

    /**
     * Get all of the invoice_general_details for the InvoiceGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoice_general_details(): HasMany
    {
        return $this->hasMany(InvoiceGeneralDetail::class, 'invoice_general_id');
    }

    /**
     * Get all of the invoice_general_additionals for the InvoiceGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoice_general_additionals(): HasMany
    {
        return $this->hasMany(InvoiceGeneralAdditional::class);
    }

    public function invoice_payment()
    {
        return InvoicePayment::where('invoice_model', InvoiceGeneral::class)
            ->where('invoice_id', $this->id)
            ->get();
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function approved_by_user()
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    public function invoice_parent()
    {
        return InvoiceParent::where('model_reference', InvoiceGeneral::class)
            ->where('reference_id', $this->id)
            ->first();
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
            ->where('model', InvoiceGeneral::class);
    }

    public function getBankInternalsAttribute()
    {
        return BankInternal::whereIn('id', $this->bank_internal_ids)->get();
    }
}
