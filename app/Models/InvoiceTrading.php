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

class InvoiceTrading extends Model
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
        'customer_id',
        'item_id',
        'currency_id',
        'exchange_rate',
        'date',
        'due',
        'due_date',
        'bank_internal_id',
        'bank_internal_ids',
        'so_trading_id',
        'kode',
        'receipt_number',
        'nomor_po_external',
        'total_jumlah_diterima',
        'status',
        'total',
        'calculate_from',
        'lost_tolerance',
        'lost_tolerance_type',
        'tolerance_amount',
        'total_lost',
        'total_jumlah_dikirim',
        'jumlah',
        'harga',
        'subtotal',
        'subtotal_after_tax',
        'additional_tax_total',
        'after_additional_tax',
        'other_cost',
        'total_other_cost',
        'payment_status',
        'created_by',
        'reference',
        'is_separate_invoice',
    ];

    protected $casts = [
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
        $validate = [
            'kode' => 'nullable|string|max:50|unique:invoice_tradings,kode,' . $id,
            'so_trading_id' => 'required|exists:sale_orders,id',
            'status' => 'nullable',
            'sub_total' => 'nullable',
            'total' => 'nullable',
            'jumlah_diterima' => 'nullable',
            'jumlah_dikirim' => 'nullable',
            'calculate_from' => 'nullable',
            'lost_tolerance' => 'nullable',
            'lost_tolerance_type' => 'nullable',
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
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            $code2 = $model->so_trading->customer;
            $model->kode = generate_code_with_cus_name(
                model: self::class,
                code: 'INV',
                code2: $code2,
                date_column: 'date',
                date: $model->date ?? \Carbon\Carbon::now()->format('Y-m-d'),
                filter: [],
            );

            $model->status = 'pending';
            $model->payment_status = 'unpaid';

            if (!checkAvailableDate($model->date)) {
                throw new \Exception('Tanggal tidak tersedia / sudah tutup buku');
            }
        });

        self::created(function ($model) {
            //CREATE OR UPDATE INVOICE PARENT
            InvoiceParent::updateOrCreate(
                [
                    'model_reference' => InvoiceTrading::class,
                    'reference_id' => $model->id,
                ],
                [
                    'model_reference' => InvoiceTrading::class,
                    'reference_id' => $model->id,
                    'branch_id' => $model->so_trading->branch_id,
                    'customer_id' => $model->customer_id,
                    'currency_id' => $model->currency_id,
                    'exchange_rate' => $model->exchange_rate,
                    'date' => $model->date,
                    'due_date' => $model->due_date,
                    'type' => 'trading',
                    'code' => $model->kode,
                    'total' => $model->total,
                    'status' => $model->status,
                    'payment_status' => $model->payment_status,
                ]
            );
        });

        static::updating(function ($model) {
            if ($model->getOriginal('status') != $model->status) {
                // ! APPROVE =========================================================================================================
                if ($model->status == 'approve') {

                    // * validate nomor po external
                    if (is_null($model->so_trading->nomor_po_external)) {
                        throw new \Exception('Nomor PO External harus diisi');
                    }

                    //CREATE OR UPDATE INVOICE PAYMENT
                    InvoicePayment::updateOrCreate([
                        'invoice_model' => InvoiceTrading::class,
                        'invoice_id' => $model->id,
                        'model' => InvoiceTrading::class,
                        'reference_id' => $model->id,
                    ], [
                        'invoice_model' => InvoiceTrading::class,
                        'invoice_id' => $model->id,
                        'exchange_rate' => $model->exchange_rate,
                        'model' => InvoiceTrading::class,
                        'reference_id' => $model->id,
                        'currency_id' => $model->currency_id,
                        'date' => $model->date,
                        'amount_to_receive' => $model->total,
                        'receive_amount' => 0,
                        'note' => "Invoice - $model->kode",
                    ]);

                    // * journal
                    $journal = new JournalHelpers('invoice-trading', $model->id);
                    try {
                        $journal->generate();
                    } catch (\Throwable $th) {
                        throw $th;
                    }

                    // * update status sales order
                    // $sales_order = $model->so_trading;
                    // $sales_order->status = 'done';
                    try {
                        // $sales_order->save();
                    } catch (\Throwable $th) {
                        throw $th;
                    }

                    // * update status is invoice created in delivery order
                    foreach ($model->invoice_trading_details as $invoice_trading_detail_key => $invoice_trading_detail) {
                        $delivery_order = $invoice_trading_detail->delivery_order;
                        $delivery_order->is_invoice_created = true;
                        try {
                            $delivery_order->save();
                        } catch (\Throwable $th) {
                            throw $th;
                        }
                    }
                }
                // ! APPROVE =========================================================================================================

                // ! REVERT void reject cancel =========================================================================================================
                if (in_array($model->status, ['revert', 'void', 'cancel', 'reject'])) {
                    // * update status is invoice created in delivery order
                    foreach ($model->invoice_trading_details as $invoice_trading_detail_key => $invoice_trading_detail) {
                        $delivery_order = $invoice_trading_detail->delivery_order;
                        $delivery_order->is_invoice_created = false;
                        try {
                            $delivery_order->save();
                        } catch (\Throwable $th) {
                            throw $th;
                        }
                    }

                    // * delete journal
                    if (in_array($model->status, ['void', 'cancel', 'reject', 'revert'])) {
                        $journal = Journal::where('reference_model', InvoiceTrading::class)
                            ->where('reference_id', $model->id)
                            ->delete();

                        //DELETE INVOICE PAYMENT
                        InvoicePayment::where('invoice_model', InvoiceTrading::class)
                            ->where('invoice_id', $model->id)
                            ->delete();

                        // DELETE INVOICE TAX
                        InvoiceTax::where('reference_parent_model', InvoiceTrading::class)
                            ->where('reference_parent_id', $model->id)
                            ->delete();
                    }
                }
                // ! REVERT void reject cancel =========================================================================================================
            }

            //CREATE OR UPDATE INVOICE PARENT
            InvoiceParent::updateOrCreate(
                [
                    'model_reference' => InvoiceTrading::class,
                    'reference_id' => $model->id,
                ],
                [
                    'model_reference' => InvoiceTrading::class,
                    'reference_id' => $model->id,
                    'branch_id' => $model->so_trading->branch_id,
                    'customer_id' => $model->customer_id,
                    'currency_id' => $model->currency_id,
                    'exchange_rate' => $model->exchange_rate,
                    'date' => $model->date,
                    'due_date' => $model->due_date,
                    'type' => 'trading',
                    'code' => $model->kode,
                    'total' => $model->total,
                    'status' => $model->status,
                    'payment_status' => $model->payment_status,
                ]
            );
        });

        static::updated(function ($model) {
            // * update status sales order
            $sales_order = $model->so_trading;
            $invoice = $sales_order->invoice_tradings->where('status', 'approve')->count();
            $paid_invoice = $sales_order->invoice_tradings->where('status', 'approve')->where('payment_status', 'paid')->count();
            $partial_invoice = $sales_order->invoice_tradings->where('status', 'approve')->where('payment_status', 'partial-paid')->count();
            $unpaid_invoice = $sales_order->invoice_tradings->where('status', 'approve')->where('payment_status', 'unpaid')->count();

            if ($invoice == 0) {
                $sales_order->payment_status = 'unpaid';
            } elseif ($invoice > 0 && $paid_invoice == $invoice) {
                $sales_order->payment_status = 'paid';
            } elseif ($invoice > 0 && $partial_invoice > 0) {
                $sales_order->payment_status = 'partial-paid';
            } elseif ($invoice > 0 && $unpaid_invoice == $invoice) {
                $sales_order->payment_status = 'unpaid';
            }
            $sales_order->save();
        });

        static::deleted(function ($model) {
            //DELETE INVOICE PARENT
            InvoiceParent::where('model_reference', InvoiceTrading::class)
                ->where('reference_id', $model->id)->delete();

            //DELETE INVOICE PAYMENT
            InvoicePayment::where('invoice_model', InvoiceTrading::class)
                ->where('invoice_id', $model->id)
                ->delete();

            //DELETE INVOICE TAX
            InvoiceTax::where('reference_parent_model', InvoiceTrading::class)
                ->where('reference_parent_id', $model->id)
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
        $activity_logs = ActivityLog::where('subject_type', InvoiceTrading::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', InvoiceTrading::class)
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

    public function getAdditionalTaxTotalAttribute(): string|int|null
    {
        $tax_total = 0;

        foreach ($this->inv_trading_add_on as $add_on) {
            $tax_total += $add_on->inv_trading_add_on_tax->sum('total');
        }

        return $tax_total;
    }

    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    /**
     * Get the currency that owns the InvoiceTrading
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    public function so_trading()
    {
        return $this->belongsTo(SoTrading::class)->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function bank_internal()
    {
        return $this->belongsTo(BankInternal::class)->withTrashed();
    }

    public function invoice_trading_coas()
    {
        return $this->hasMany(InvoiceTradingCoa::class);
    }

    public function invoice_trading_taxes()
    {
        return $this->hasMany(InvoiceTradingTax::class);
    }

    public function inv_trading_add_on(): HasMany
    {
        return $this->hasMany(InvTradingAddOn::class);
    }

    public function invoice_payment()
    {
        return InvoicePayment::where('invoice_model', InvoiceTrading::class)
            ->where('invoice_id', $this->id)
            ->get();
    }

    public function invoice_parent()
    {
        return InvoiceParent::where('model_reference', InvoiceTrading::class)
            ->where('reference_id', $this->id)
            ->first();
    }

    /**
     * Get the branch that owns the data.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get all of the invoice_trading_details for the InvoiceTrading
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoice_trading_details(): HasMany
    {
        return $this->hasMany(InvoiceTradingDetail::class);
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function approved_by_user()
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }


    function bankInternals()
    {
        return BankInternal::whereIn('id', $this->bank_internal_ids)->get();
    }

    public function printedData()
    {
        return $this->hasMany(DocumentPrint::class, 'model_id', 'id')
            ->where('model', InvoiceTrading::class);
    }
}
