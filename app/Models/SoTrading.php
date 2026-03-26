<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SoTrading extends Model
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
        'nomor_so',
        'customer_id',
        'tanggal',
        'sub_total',
        'total',
        'sub_total_after_tax',
        'other_cost',
        'tax_id',
        'nomor_po_external',
        'status',
        'currency_id',
        'exchange_rate',
        'approved_by',
        'sh_number_id',
        'branch_id',
        'type',
        'quotation',
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
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sale_orders';

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
            'customer_id' => 'required|exists:customers,id',
            'tanggal' => 'nullable|date',
            // 'sub_total' => '',
            // 'total' => '',
            'tax_id' => 'nullable|exists:taxes,id',
            'nomor_po_external' => 'nullable|string|max:255',
            'status' => '',
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
            if ($model->tanggal == null) {
                $model->tanggal =  Carbon::today()->format('Y-m-d');
            }

            if ($model->status == null) {
                $model->status = 'pending';
            }

            $model->created_by = auth()->user()->id;

            if ($model->nomor_so == null) {
                $code2 =  $model->customer;
                $model->nomor_so = generate_code_with_cus_name(
                    model: self::class,
                    code: 'SO',
                    code2: $code2,
                    date_column: 'tanggal',
                    date: $model->tanggal ?? \Carbon\Carbon::now()->format('Y-m-d'),
                    filter: [],
                );
            }

            // determine if the nomor_po_external is null
            if ($model->nomor_po_external == null) {
                $model->nomor_po_external = "POX/" . Carbon::parse($model->tanggal)->format('dmy');
            }
        });

        static::updating(function ($model) {
            if ($model->getOriginal('status') != $model->status) {
                if ($model->status == 'approve') {
                    $model->approved_by = auth()->user()->id;
                }

                // * done
                if ($model->status == 'done') {
                    $detail = $model->so_trading_detail;

                    if ($detail->sudah_dialokasikan != $detail->sudah_dikirim) {
                        throw new \Exception("sesuaikan Sale order dan Purchase Order pairing");
                    }
                }
            }
        });

        static::updated(function ($model) {
            $so_trading_detail = $model->so_trading_detail;

            if ($model->status != $model->getOriginal('status')) {
                // * status updating to done
                if ($model->status == 'done') {
                    if ($so_trading_detail != null) {
                        if ($so_trading_detail->status != 'done' || $so_trading_detail->status != 'reject' || $so_trading_detail->status != 'cancel') {
                            $so_trading_detail->status = 'done';
                            $so_trading_detail->save();
                        }
                    }
                }

                // * status updating to revert
                if ($model->status == 'revert') {
                    if ($so_trading_detail->status != 'revert') {
                        $so_trading_detail->status = 'revert';
                        $so_trading_detail->save();
                    }
                }

                // * status updating to void
                if ($model->status == 'void') {
                    if ($so_trading_detail->status != 'void') {
                        $so_trading_detail->status = 'void';
                        $so_trading_detail->save();
                    }
                }

                // * status updating to approve
                if ($model->status == 'approve') {
                    // if order item not canceled ot rejected change item status to pairing
                    if ($so_trading_detail != null) {
                        if ($so_trading_detail->status != 'void' || $so_trading_detail->status != 'revert') {
                            if ($so_trading_detail->status != 'pairing') {
                                $so_trading_detail->status = 'pairing';
                                $so_trading_detail->save();
                            }
                        }
                    }
                }
            }
        });

        static::deleted(function ($model) {
            if ($model->status != 'void') {
                $model->status = 'void';
                $model->save();
            }
        });
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

    public function getJumlahNumberAttribute(): float
    {
        $item = $this->so_trading_detail;
        $jumlah = 0;

        return (float)  $item->jumlah;
    }

    public function getAllStatusAttribute()
    {
        if ($this->status != 'pending' or $this->status != 'reject' or $this->status != 'cancel') {
            if ($this->so_trading_detail->status == 'done' || $this->status == 'delivery_complete') {
                return 'full';
            } elseif ($this->sudah_dialokasikan == 0) {
                return 'not paired';
            } else {
                return 'partial';
            }
        } else {
            return $this->status;
        }
    }

    public function getDeliverySendedAttribute()
    {
        $already_created = false;
        $do_created = 0;
        $do_failed = 0;

        if (count($this->delivery_orders) != 0) {
            $already_created = true;

            // * calculate
            foreach ($this->delivery_orders as $delivery_order_key => $delivery_order_value) {
                if ($delivery_order_value->status == 'reject' or $delivery_order_value->status == 'cancel') {
                    $do_failed += $delivery_order_value->kuantitas_kirim;
                } else {
                    $do_created += $delivery_order_value->kuantitas_kirim;
                }
            }
        }


        return [
            'result' => $this->jumlah_number - ($do_created - $do_failed),
            'already_created' => $already_created,
        ];
    }

    public function getIsHaveAnyRequestPrintAttribute()
    {
        $count = $this->delivery_orders->where('status', 'request-print')->count();

        return $count > 0;
    }

    public function getIsCanSetDoneAttribute()
    {
        //
    }

    public function getIsHaveAnySubmittedAttribute()
    {
        $count = $this->delivery_orders->where('status', 'submitted')->count();

        return $count > 0;
    }

    /**
     * getAdditionalTaxTotalAttribute
     *
     * @return string|int|null
     */
    public function getAdditionalTaxTotalAttribute(): string|int|null
    {
        $tax_total = 0;

        foreach ($this->sale_order_additionals as $purchase_order_additional_key => $purchase_order_additional) {
            $tax_total += $purchase_order_additional->sale_order_additional_taxes->sum('total');
        }

        return $tax_total;
    }

    /**
     * Determine check the sale order delivery count and delivery complete and invoiced
     */
    public function getIsDeliveryCompleteAttribute(): bool
    {
        $deliveryOrderQuery = DeliveryOrder::where('so_trading_id', $this->id);

        $deliveryOrderCreated = $deliveryOrderQuery->whereNotIn('status', [
            'reject',
            'cancel',
            'void',
            'revert',
        ])->count();

        $deliveryOrderInvoiceCreated = $deliveryOrderQuery->where('status', 'done')
            ->where('is_invoice_created', true)
            ->count();

        return $deliveryOrderCreated == $deliveryOrderInvoiceCreated;
    }

    /**
     * getLogsData
     *
     * @return array
     */
    public function getLogsDataAttribute()
    {
        $activity_logs = ActivityLog::where('subject_type', SoTrading::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', SoTrading::class)
            ->where('reference_id', $this->id)
            ->orderByDesc('created_at')
            ->get();

        return compact('status_logs', 'activity_logs');
    }

    public function approve_by()
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    public function create_by()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    public function sh_number()
    {
        return $this->belongsTo(ShNumber::class)->withTrashed();
    }

    /**
     * Get the branch that owns the data.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function so_trading_detail()
    {
        return $this->hasOne(SoTradingDetail::class);
    }

    public function sale_order_taxes()
    {
        return $this->hasMany(SaleOrderTax::class);
    }

    public function delivery_orders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    public function pairings()
    {
        return $this->hasMany(PairingSoToPo::class);
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function approved_by_user()
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    public function invoice_tradings()
    {
        return $this->hasMany(InvoiceTrading::class);
    }

    /**
     * Get all of the sale_order_additionals for the SoTrading
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sale_order_additionals(): HasMany
    {
        return $this->hasMany(SaleOrderAdditional::class, 'sale_order_id');
    }

    /**
     * getCheckAvailableDateAttribute for generated SO Trading
     *
     * @return bool
     */
    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->tanggal);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
