<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PurchaseOrderServiceDetailItem extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_order_service_detail_id',
        'purchase_request_detail_id',
        'item_id',
        'unit_id',
        'status',
        'quantity',
        'quantity_received',
        'price_before_discount',
        'discount',
        'price',
        'discount_type',
        'discount_value',
        'discount_value_percent',
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
        'quantity' => 'float',
        'quantity_received' => 'float',
        'price' => 'float',
        'discount_value' => 'float',
        'discount_value_percent' => 'float',
        'sub_total' => 'float',
        'sub_total_after_tax' => 'float',
        'amount_discount' => 'float',
        'tax_total' => 'float',
        'total' => 'float',
    ];

    protected $appends = ['price_display'];

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

        static::updating(function ($model) {
            if ($model->isDirty('quantity_received')) {
                if ($model->quantity_received == $model->quantity) {
                    $model->status = 'done';
                } elseif ($model->quantity_received < $model->quantity && $model->quantity_received > 0) {
                    $model->status = 'partial';
                } elseif ($model->quantity_received == 0) {
                    $model->status = 'approve';
                }
            }
        });

        static::updated(function ($model) {
            if ($model->isDirty('status') && $model->isDirty('quantity_received')) {
                if ($model->isDirty('status')) {
                    // * if status is reject

                    // * if status is approve or pending
                }

                /**
                 * * if status is done check purchase order service detail
                 *
                 * if all item is done, then update status purchase order service detail to done
                 * if all purchase order service detail is done then update status purchase order service to done
                 */
                if ($model->status == 'done') {
                    $items_done_count = $model
                        ->purchase_order_service_detail
                        ->purchase_order_service_detail_items()
                        ->where('status', 'done')
                        ->whereNotIn('status', ['reject', 'revert', 'void', 'pending'])
                        ->count();

                    $items_count = $model
                        ->purchase_order_service_detail
                        ->purchase_order_service_detail_items()
                        ->whereNotIn('status', ['reject', 'revert', 'void', 'pending'])
                        ->count();

                    if ($items_done_count == $items_count) {
                        // * update purchase order service detail to done
                        $model->purchase_order_service_detail->status = 'done';
                        $model->purchase_order_service_detail->save();

                        // * check and update purchase order service parent
                        $purchase_order_service = $model
                            ->purchase_order_service_detail
                            ->purchase_order_service;

                        $purchase_order_service_details_done_count = $purchase_order_service
                            ->purchaseOrderServiceDetails()
                            ->where('type', 'main')
                            ->where('status', 'done')
                            ->whereNotIn('status', ['reject', 'revert', 'void', 'pending'])
                            ->count();

                        $purchase_order_service_details_count = $purchase_order_service
                            ->purchaseOrderServiceDetails()
                            ->where('type', 'main')
                            ->whereNotIn('status', ['reject', 'revert', 'void', 'pending'])
                            ->count();

                        if ($purchase_order_service_details_done_count == $purchase_order_service_details_count) {
                            $purchase_order_service->status = 'done';
                        } else {
                            $purchase_order_service->status = 'partial';
                        }

                        $purchase_order_service->save();
                    } elseif ($model->purchase_order_service_detail->status != 'partial') {
                        $model->purchase_order_service_detail->status = 'partial';
                        $model->purchase_order_service_detail->save();

                        // * check and update purchase order service parent
                        $purchase_order_service = $model
                            ->purchase_order_service_detail
                            ->purchase_order_service;

                        if ($purchase_order_service->status != 'partial') {
                            $purchase_order_service->status = 'partial';
                        }

                        $purchase_order_service->save();
                    }
                }

                // * if status is partial
                // * check and the purchase detail and parent status
                if ($model->status == 'partial') {
                    if ($model->purchase_order_service_detail->status != 'partial') {
                        $model->purchase_order_service_detail->status = 'partial';
                        $model->purchase_order_service_detail->save();

                        // * check and update purchase order service parent
                        $purchase_order_service = $model
                            ->purchase_order_service_detail
                            ->purchase_order_service;

                        if ($purchase_order_service->status != 'partial') {
                            $purchase_order_service->status = 'partial';
                            $purchase_order_service->save();
                        }
                    }
                }

                // * if status is approve
                // * check and the purchase detail and parent status
                if ($model->status == 'approve') {
                    // * check and update purchase order detail
                    $items_approve_count = $model
                        ->purchase_order_service_detail
                        ->purchase_order_service_detail_items()
                        ->where('status', 'approve')
                        ->count();

                    $items_count = $model
                        ->purchase_order_service_detail
                        ->purchase_order_service_detail_items()
                        ->whereNotIn('status', ['reject', 'revert', 'void', 'pending'])
                        ->count();

                    if ($items_approve_count == $items_count) {
                        $model->purchase_order_service_detail->status = 'approve';
                        $model->purchase_order_service_detail->save();

                        // * check and update purchase order service parent
                        $purchase_order_service = $model
                            ->purchase_order_service_detail
                            ->purchase_order_service;

                        $purchase_order_service_details_approve_count = $purchase_order_service->purchaseOrderServiceDetails()
                            ->where('type', 'main')
                            ->where('status', 'approve')
                            ->count();

                        $purchase_order_service_details_count = $purchase_order_service->purchaseOrderServiceDetails()
                            ->where('type', 'main')
                            ->whereNotIn('status', ['reject', 'revert', 'void', 'pending'])
                            ->count();

                        if ($purchase_order_service_details_approve_count == $purchase_order_service_details_count) {
                            $purchase_order_service->status = 'approve';
                        } else {
                            $purchase_order_service->status = 'partial';
                        }

                        $purchase_order_service->save();
                    } else {
                        $model->purchase_order_service_detail->status = 'partial';
                        $model->purchase_order_service_detail->save();

                        // * check and update purchase order service parent
                        $purchase_order_service = $model
                            ->purchase_order_service_detail
                            ->purchase_order_service;

                        if ($purchase_order_service->status != 'partial') {
                            $purchase_order_service->status = 'partial';
                        }

                        $purchase_order_service->save();
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
     * Check and set status for purchase order service detail
     *
     * @return void
     */
    public function checkAndSetStatusForPurchaseOrderServiceDetail()
    {
        $purchase_order_service_detail = $this->purchase_order_service_detail;

        $purchase_order_service_detail_item_all_count = $purchase_order_service_detail
            ->purchase_order_service_detail_items()
            ->whereNotIn('status', ['reject', 'pending', 'void', 'revert'])
            ->count();

        $purchase_order_service_detail_item_done_count = $purchase_order_service_detail
            ->purchase_order_service_detail_items()
            ->whereNotIn('status', ['reject', 'pending', 'void', 'revert'])
            ->where('status', 'done')
            ->count();

        if ($purchase_order_service_detail_item_all_count == $purchase_order_service_detail_item_done_count && $purchase_order_service_detail->status != 'done') {
            $purchase_order_service_detail->status = 'done';
        } elseif ($purchase_order_service_detail_item_all_count > $purchase_order_service_detail_item_done_count && $purchase_order_service_detail->status != 'partial') {
            $purchase_order_service_detail->status = 'partial';
        } else {
            if ($purchase_order_service_detail->status != 'approve') {
                $purchase_order_service_detail->status = 'approve';
            }
        }

        $purchase_order_service_detail->save();
    }

    /**
     * Get the purchase order service detail that owns the PurchaseOrderServiceDetailItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase_order_service_detail(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderServiceDetail::class);
    }

    /**
     * Get the purchase request detail that owns the PurchaseOrderServiceDetailItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase_request_detail(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequestDetail::class);
    }

    /**
     * Get the item that owns the PurchaseOrderServiceDetailItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    /**
     * Get the unit that owns the PurchaseOrderServiceDetailItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class)->withTrashed();
    }

    /**
     * Get all of the purchase order service detail item taxes for the PurchaseOrderServiceDetailItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchase_order_service_detail_item_taxes(): HasMany
    {
        return $this->hasMany(PurchaseOrderServiceDetailItemTax::class);
    }

    public function getPriceDisplayAttribute()
    {
        $price = $this->price_before_discount;
        if ($this->purchase_order_service_detail->purchase_order_service->is_include_tax) {
            $price = $this->price;
            $price += $this->discount;
        }

        if ($this->purchase_order_service_detail->type == "additional") {
            $price = $this->price;
        }

        return $price;
    }
}
