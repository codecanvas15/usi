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

class PoTrading extends Model
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
        'purchase_request_trading_id',
        'sale_order_id',
        'customer_id',
        'sh_number_id',
        'tanggal',
        'nomor_po',
        'jumlah',
        'sub_total',
        'total_after_discount',
        'total_before_discount',
        'other_cost',
        'status',
        'sale_confirmation',
        'purchase_id',
        'currency_id',
        'exchange_rate',
        'vendor_id',
        'branch_id',
        'quotation',
        'note',
        'top',
        'top_day',
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
    protected $table = 'purchase_orders';

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
            'customer_id' => 'nullable|exists:customers,id',
            'sh_number_id' => 'nullable|exists:sh_numbers,id',
            'tanggal' => 'nullable|date',
            'jumlah' => 'nullable',
            'sub_total' => 'nullable',
            'total' => 'nullable',
            'status' => 'nullable',
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
                $model->tanggal = Carbon::today()->format('Y-m-d');
            }

            if ($model->status == null) {
                $model->status = 'pending';
            }

            // if vendor is null
            if (is_null($model->vendor_id)) {
                $model->vendor_id = 1;
            }

            $model->created_by = auth()->user()->id;

            // kode
            if ($model->nomor_po == null) {
                $code2 = $model->customer;
                $model->nomor_po = generate_trading_code(
                    model: self::class,
                    code: 'PO',
                    code2: $code2,
                    date_column: 'tanggal',
                    date: $model->tanggal
                );
            }
        });

        static::created(function ($model) {
            $purchase = $model->purchase;
            if ($purchase) {
                $purchase->status = $model->status;
                $purchase->save();
            }

            $model->update_purchase_request($model->purchase_request_trading_id);
        });

        static::updated(function ($model) {
            $po_trading_detail = $model->po_trading_detail;

            if ($model->purchase_request_trading_id != $model->getOriginal('purchase_request_trading_id')) {
                $model->update_purchase_request($model->getOriginal('purchase_request_trading_id'));
            }

            $model->update_purchase_request($model->purchase_request_trading_id);

            if ($model->status != $model->getOriginal('status')) {

                // purchase status
                $purchase = $model->purchase;
                if ($purchase) {
                    $purchase->status = $model->status;
                    $purchase->save();
                }

                // status updating to close
                if ($model->status == 'close') {
                    if ($po_trading_detail != null) {
                        if ($po_trading_detail->status != 'close') {
                            $po_trading_detail->status = 'close';
                            $po_trading_detail->save();
                        }
                    }
                }

                // status updating to approve
                if ($model->status == 'approve') {
                    // if order item not canceled ot rejected change item status to pairing

                    if ($po_trading_detail != null) {
                        if ($po_trading_detail->status != 'pairing') {
                            $po_trading_detail->status = 'pairing';
                            $po_trading_detail->save();
                        }
                    }

                    // !! AUTO PAIRING
                    if ($model->sale_order) {
                        $pairing = PairingSoToPo::where('po_trading_detail_id', $model->po_trading_detail->id)
                            ->where('so_trading_detail_id', $model->sale_order->so_trading_detail->id)
                            ->first();

                        if (!$pairing) {
                            $pairing = new PairingSoToPo();
                            $pairing->loadModel([
                                'so_trading_detail_id' => $model->sale_order->so_trading_detail->id,
                                'po_trading_detail_id' => $model->po_trading_detail->id,
                                'alokasi' => $model->po_trading_detail->jumlah,
                            ]);
                            $pairing->save();

                            $sale_order_detail = $model->sale_order->so_trading_detail;
                            $sale_order_detail->sudah_dialokasikan += $model->po_trading_detail->jumlah;
                            $sale_order_detail->save();

                            $po_trading_detail->sudah_dialokasikan += $model->po_trading_detail->jumlah;
                            $po_trading_detail->save();
                        }
                    }
                }

                if ($model->status == 'void' || $model->status == 'reject' || $model->status == 'revert') {
                    if ($po_trading_detail != null) {
                        $po_trading_detail->status = $model->status;
                        $po_trading_detail->save();
                    }

                    // !! REMOVE PAIRING
                    if ($model->sale_order) {
                        $pairing = PairingSoToPo::where('so_trading_detail_id', $model->sale_order->so_trading_detail->id)->where('po_trading_detail_id', $model->po_trading_detail->id)->first();
                        if ($pairing) {
                            $model->sale_order->so_trading_detail->sudah_dialokasikan -= $model->po_trading_detail->jumlah;
                            $model->sale_order->so_trading_detail->save();
                            $pairing->delete();

                            $po_trading_detail->sudah_dialokasikan -= $model->po_trading_detail->jumlah;
                            $po_trading_detail->save();
                        }
                    }
                }

                $model->update_purchase_request($model->purchase_request_trading_id);
            }
        });

        static::deleted(function ($model) {
            if ($model->status != 'cancel') {
                $model->status = 'cancel';
                $model->save();

                $po_trading_detail = $model->po_trading_detail;
                if ($po_trading_detail != null) {
                    $po_trading_detail->status = 'cancel';
                    $po_trading_detail->save();
                }
            }

            $model->update_purchase_request($model->purchase_request_trading_id);
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

    /**
     * getLogsData
     *
     * @return array
     */
    public function getLogsDataAttribute()
    {
        $activity_logs = ActivityLog::where('subject_type', PoTrading::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', PoTrading::class)
            ->where('reference_id', $this->id)
            ->orderByDesc('created_at')
            ->get();

        return compact('status_logs', 'activity_logs');
    }

    public function getJumlahAttribute()
    {
        return $this->po_trading_detail->jumlah . ' ' . ($this->po_trading_detail->item->unit->name ?? '');
    }

    public function getAllStatusAttribute()
    {
        if ($this->status != 'pending' or $this->status != 'reject' or $this->status != 'cancel') {
            if ($this->po_trading_detail->status == 'done') {
                return 'full';
            } elseif ($this->po_trading_detail->sudah_dialokasikan == 0) {
                return 'not paired';
            } else {
                return 'partial';
            }
        } else {
            return $this->status;
        }
    }

    /**
     * getAdditionalTaxTotalAttribute
     *
     * @return string|int|null
     */
    public function getAdditionalTaxTotalAttribute(): string|int|null
    {
        $tax_total = 0;

        foreach ($this->purchase_order_additionals as $purchase_order_additional_key => $purchase_order_additional) {
            $tax_total += $purchase_order_additional->purchase_order_additional_taxes->sum('total');
        }

        return $tax_total;
    }

    /**
     * get property ItemReceivingReportAttribute
     *
     * @return array|Collection
     */
    public function getItemReceivingReportDataAttribute()
    {
        $item_receiving_reports = ItemReceivingReport::where('reference_id', $this->id)->where('reference_model', PoTrading::class)->with(['item_receiving_report_po_trading'])->get();

        return $item_receiving_reports;
    }

    public function approve_by()
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    public function create_by()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Get the created_by_user that owns the PoTrading
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_by_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function sh_number()
    {
        return $this->belongsTo(ShNumber::class)->withTrashed();
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class)->withTrashed();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    /**
     * Get the vendor that owns the data.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class)->withTrashed();
    }

    /**
     * Get the branch that owns the PoTrading
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function po_trading_detail()
    {
        return $this->hasOne(PoTradingDetail::class);
    }

    public function purchase_order_ware_house()
    {
        return $this->hasOne(PurchaseOrderWareHouse::class);
    }

    public function lpb_tradings()
    {
        return $this->hasMany(LpbTrading::class);
    }

    /**
     * Get all of the purchase_order_taxes for the PoTrading
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchase_order_taxes(): HasMany
    {
        return $this->hasMany(PurchaseOrderTax::class);
    }

    /**
     * Get all of the purchase_order_taxes for the PoTrading
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchase_order_additionals(): HasMany
    {
        return $this->hasMany(PurchaseOrderAdditionalItems::class);
    }

    public function purchase_request_trading()
    {
        return $this->belongsTo(PurchaseRequestTrading::class);
    }

    public function sale_order()
    {
        return $this->belongsTo(SoTrading::class, 'sale_order_id')->withTrashed();
    }

    /**
     * getCheckAvailableDataAttribute for generated PO Trading
     */
    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->tanggal);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $arrat['total'] = number_format($this->total, 2, '.', '.');
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function update_purchase_request($id)
    {
        $purchase_request_trading = PurchaseRequestTrading::find($id);
        if ($purchase_request_trading) {
            $purchase_request_trading_detail = $purchase_request_trading->purchase_request_trading_details[0];
            $ordered_qty = PoTradingDetail::whereHas('po_trading', function ($q) use ($id) {
                $q->whereNotIn('status', ['void', 'reject', 'cancel'])
                    ->where('purchase_request_trading_id', $id);
            })->sum('jumlah');

            $purchase_request_trading_detail->ordered_qty = $ordered_qty;
            $purchase_request_trading_detail->save();

            if ($ordered_qty == 0) {
                $purchase_request_trading->order_status = 'pending';
            } elseif ($ordered_qty == $purchase_request_trading_detail->qty) {
                $purchase_request_trading->order_status = 'done';
            } else {
                $purchase_request_trading->order_status = 'partial';
            }
            $purchase_request_trading->save();
        }
    }
}
