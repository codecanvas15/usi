<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PurchaseOrderGeneral extends Model
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
        'purchase_id',
        'branch_id',
        'vendor_id',
        'currency_id',
        'created_by',
        'approved_by',
        'type',
        'code',
        'date',
        'status',
        'quotation',
        'term_of_payment',
        'term_of_payment_days',
        'payment_description',
        'exchange_rate',
        'total',
        'total_main',
        'total_additional',
        'total_tax_main',
        'total_tax_additional',
        'amount_discount',
        'close_note',
        'is_include_tax',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'exchange_rate' => 'float',
        'total' => 'float',
        'total_main' => 'float',
        'total_additional' => 'float',
        'total_tax_main' => 'float',
        'total_tax_additional' => 'float',
        'amount_discount' => 'float',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (is_null($model->branch_id)) {
                $model->branch_id = auth()->user()->branch_id;
            }

            if (is_null($model->created_by)) {
                $model->created_by = auth()->user()->id;
            }

            if (is_null($model->status)) {
                $model->status = "pending";
            }

            if (is_null($model->date)) {
                $model->date = \Carbon\Carbon::now()->format("Y-m-d");
            }

            if (is_null($model->code)) {
                $branch = Branch::find($model->branch_id);
                $model->code = generate_code(self::class, 'code', 'date', "POG", branch_sort: $branch->sort ?? null, date: $model->date);
            }

            if ($model->term_of_payment == 'cash') {
                $model->term_of_payment_days = 0;
            }
        });

        static::updated(function ($model) {
            // * if status is updating
            if ($model->isDirty('status')) {
                // * update parent status
                if ($model->purchase) {
                    $purchase = $model->purchase;
                    $purchase->status = $model->status;
                    $purchase->save();
                }

                // * if status is approve
                if ($model->status == 'approve') {
                    $model->approved_by = auth()->user()->id;
                }

                // * if status is reject or void set all detail to reject or void
                if (in_array($model->status, ['reject', 'void'])) {
                    $model->purchaseOrderGeneralDetails()
                        ->each(function ($q) use ($model) {
                            $q->status = $model->status;
                            $q->save();
                        });
                }

                // * if status is approve set all detail where not rejected to approve
                if ($model->status == 'approve') {
                    $model->purchaseOrderGeneralDetails()
                        ->whereNotIn('status', ['reject'])
                        ->each(function ($q) use ($model) {
                            $q->status = $model->status;
                            $q->save();
                        });
                }

                // * if status is revert ot pending set all detail where not rejected to pending
                if (in_array($model->status, ['revert', 'pending'])) {
                    $model->purchaseOrderGeneralDetails()
                        ->each(function ($q) use ($model) {
                            $q->status = $model->status;
                            $q->save();
                        });
                }

                // * if status is done set all detail where not rejected to done and set the un complete to void
                if ($model->status == 'done') {
                    $model->purchaseOrderGeneralDetails()
                        ->where('type', 'main')
                        ->each(function ($q) use ($model) {
                            $q->status = 'done';
                            $q->save();
                        });

                    $model->purchaseOrderGeneralDetails()
                        ->where('status', 'done')
                        ->where('type', 'additional')
                        ->each(function ($q) use ($model) {
                            $q->status = 'done';
                            $q->save();
                        });
                }

                if ($model->status == 'close') {
                    $model->purchaseOrderGeneralDetails()
                        ->where('type', 'main')
                        ->each(function ($q) use ($model) {
                            $q->status = 'close';
                            $q->save();
                        });

                    $model->purchaseOrderGeneralDetails()
                        ->where('status', 'close')
                        ->where('type', 'additional')
                        ->each(function ($q) use ($model) {
                            $q->status = 'close';
                            $q->save();
                        });
                }
            }
        });

        static::deleted(function ($model) {
            // // * update purchase request status
            // $model->purchaseOrderGeneralDetails()->each(function ($query) {
            //     // * update purchase request detail status
            //     $query->purchase_order_general_detail_items
            //         ->each(function ($detail) use ($query) {
            //             if ($detail->purchase_request_detail) {
            //                 $detail->purchase_request_detail->status = 'approve';
            //                 $detail->purchase_request_detail->save();
            //             }
            //         });
            // });
        });
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
     * Get approved at
     */
    public function getApprovedAtAttribute()
    {
        return ActivityStatusLog::where('reference_model', self::class)
            ->where('reference_id', $this->id)
            ->where('to_status', 'approved')
            ->orderByDesc('created_at')
            ->first()
            ->created_at ?? null;
    }

    /**
     * Get the purchase that owns the PurchaseOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class)->withTrashed();
    }

    /**
     * Get the branch that owns the PurchaseOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }


    /**
     * Get the vendor that owns the PurchaseOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class)->withTrashed();
    }

    /**
     * Get the currency that owns the PurchaseOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    /**
     * Get the create that owns the PurchaseOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function create(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Get the approve that owns the PurchaseOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approve(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    /**
     * Get all of the purchaseOrderGeneralDetails for the PurchaseOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchaseOrderGeneralDetails(): HasMany
    {
        return $this->hasMany(PurchaseOrderGeneralDetail::class);
    }

    /**
     * getCheckAvailableDateAttribute for generated purchaseOrderGeneral
     *
     * @return bool
     */
    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->date);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
