<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PurchaseOrderServiceDetail extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_order_service_id',
        'purchase_request_id',
        'type',
        'status',
        'sub_total',
        'sub_total_after_tax',
        'amount_discount',
        'tax_total',
        'total',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'sub_total' => 'float',
        'sub_total_after_tax' => 'float',
        'amount_discount' => 'float',
        'tax_total' => 'float',
        'total' => 'float',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (is_null($model->status)) {
                $model->status = 'pending';
            }
        });

        static::updated(function ($model) {
            if ($model->isDirty("status")) {
                // ! reject void ############
                if (in_array($model->status, ['reject', 'void'])) {
                    $model
                        ->purchase_order_service_detail_items
                        ->each(function ($detail) use ($model) {
                            $detail->status = $model->status;
                            $detail->save();

                            // * update purchase request status
                            if ($detail->purchase_request_detail) {
                                $detail->purchase_request_detail->status = 'approve';
                                $detail->purchase_request_detail->save();
                            }
                        });

                    if ($model->type == 'main') {
                        /**
                         * ! if this purchase request is only have on purchase order
                         *      ? if this purchase order created form all purchase request
                         *          update purchase request status to approve
                         *      ? else
                         *          update purchase request status to partial
                         *
                         * ! else
                         *     ? if this purchase request status is done
                         *          update purchase request status to partial
                         */
                        // if (PurchaseOrderServiceDetail::where('purchase_request_id')->count() == 1) {
                        //     if ($model->purchase_request->purchase_request_details->whereNotIn('status', ['reject', 'pending'])->count() == $model->purchase_order_service_detail_items->count()) {
                        //         $model->purchase_request->status = 'approve';
                        //         $model->purchase_request->save();
                        //     } else {
                        //         $model->purchase_request->status = 'partial';
                        //         $model->purchase_request->save();
                        //     }
                        // } else {
                        //     if ($model->purchase_request->status == 'done') {
                        //         $model->purchase_request->status = 'partial';
                        //         $model->purchase_request->save();
                        //     }
                        // }

                        create_activity_status_log_not_trait(PurchaseRequest::class, $model->purchase_request->id, "your purchase was {$model->status}", 'done', $model->purchase_request->status);
                    }
                }

                // ! revert ############
                if (in_array($model->status, ['revert', 'pending'])) {
                    $model->purchase_order_service_detail_items
                        ->each(function ($detail) use ($model) {
                            $detail->status = $model->status;
                            $detail->save();
                        });
                }

                // ! approve ############
                if ($model->status == 'approve') {
                    $model->purchase_order_service_detail_items
                        ->where('status', '!=', 'reject')
                        ->each(function ($detail) use ($model) {
                            $detail->status = $model->status;
                            $detail->save();
                        });
                }

                // ! void ############
                if ($model->status == 'void') {
                    $model->purchase_order_service_detail_items
                        ->each(function ($detail) use ($model) {
                            $detail->status = $model->status;
                            $detail->save();
                        });
                }

                // ! reject ############
                if ($model->status == 'reject') {
                    $model->purchase_order_service_detail_items
                        ->each(function ($detail) use ($model) {
                            $detail->status = $model->status;
                            $detail->save();
                        });
                }

                if (in_array($model->status, ['done'])) {
                    if ($model->type == 'main') {
                        // * set purchase order detail item to done
                        $model->purchase_order_service_detail_items
                            ->whereNotIn('status', ['reject', 'pending'])
                            ->each(function ($detail) use ($model) {
                                $detail->status = 'done';
                                $detail->save();
                            });

                        $model->purchase_order_service_detail_items
                            ->whereNotIn('status', ['reject', 'done'])
                            ->each(function ($detail) use ($model) {
                                $detail->status = 'void';
                                $detail->save();
                            });
                    }

                    if ($model->type == 'additional') {
                        // * set purchase order detail item to done
                        $model->purchase_order_service_detail_items
                            ->where('status', 'approve')
                            ->each(function ($detail) use ($model) {
                                $detail->status = 'done';
                                $detail->save();
                            });

                        $model->purchase_order_service_detail_items
                            ->whereNotIn('status', ['reject', 'done'])
                            ->each(function ($detail) use ($model) {
                                $detail->status = 'void';
                                $detail->save();
                            });
                    }
                }
            }
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
            ->setDescriptionForEvent(fn (string $eventName) => "This data has been {$eventName}")
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
     * Check and set status for purchase order service parent
     *
     * @return void
     */
    public function checkAndSetStatusForPurchaseOrderServiceParent()
    {
        $purchase_order_service = $this->purchase_order_service;

        $purchase_order_service_detail_done_count = $purchase_order_service
            ->purchaseOrderServiceDetails()
            ->where('status', 'main')
            ->whereNotIn('status', ['pending', 'reject', 'revert', 'void'])
            ->where('status', 'done')
            ->count();
        $purchase_order_service_detail_all_count = $purchase_order_service
            ->purchaseOrderServiceDetails()
            ->where('status', 'main')
            ->whereNotIn('status', ['pending', 'reject', 'revert', 'void'])
            ->count();

        if ($purchase_order_service_detail_done_count == $purchase_order_service_detail_all_count) {
            $purchase_order_service->status = 'done';
        } elseif ($purchase_order_service_detail_done_count > $purchase_order_service_detail_all_count && $purchase_order_service->status != 'partial') {
            $purchase_order_service->status = 'partial';
        } else {
            $purchase_order_service->status = 'approve';
        }

        $purchase_order_service->save();
    }

    /**
     * Get the purchase order service that owns the PurchaseOrderServiceDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase_order_service(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderService::class)->withTrashed();
    }

    /**
     * Get the purchase request that owns the PurchaseOrderServiceDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase_request(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class)->withTrashed();
    }

    /**
     * Get all of the purchase order service detail items for the PurchaseOrderServiceDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchase_order_service_detail_items(): HasMany
    {
        return $this->hasMany(PurchaseOrderServiceDetailItem::class);
    }
}
