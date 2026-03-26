<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PurchaseRequest extends Model
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
        'kode',
        'tanggal',
        'status',
        'type',
        'created_by',
        'keterangan',
        'branch_id',
        'division_id',
        'project_id',
        'close_notes'
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

    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->tanggal);
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
            // 'kode' => ''
            'date' => 'nullable|date',
            // 'status' => 'nullable|',
            // 'created_by' => 'nullable|exists:users,id',
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

            if ($model->kode == null) {
                $last_purchase_request = PurchaseRequest::where('branch_id', get_current_branch_id())
                    // ->where('type', $model->type)
                    ->whereMonth('created_at', date('m'))
                    ->orderBy('id', 'desc')
                    ->withTrashed()
                    ->first();

                if ($last_purchase_request) {
                    $model->kode = generate_code_purchase_request($last_purchase_request->kode, year: $model->tanggal);
                } else {
                    $model->kode = generate_code_purchase_request("0000/0000/00/0000", year: $model->tanggal);
                }
            }

            if (Auth::check()) {
                if ($model->division_id == null) {
                    $model->division_id = Auth::user()->division_id;
                }
            }

            if (!$model->status) {
                $model->status = 'pending';
            }

            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->branch_id = Auth::user()->branch_id;
            }
        });

        static::updating(function ($model) {
            $purchase_request_details = $model->purchase_request_details;

            // dd($model->getOriginal('status'), $model->status);
            if ($model->status != $model->getOriginal('status')) {
                // * reject void
                if (in_array($model->status, ['reject', 'void'])) {
                    $purchase_request_details
                        ->each(function ($detail) use ($model) {
                            $detail->status = $model->status;
                            $detail->save();
                        });
                }

                // * revert
                if ($model->status == 'revert') {
                    $purchase_request_details
                        ->where('status', '!=', 'reject')
                        ->each(function ($detail) use ($model) {
                            $detail->status = 'pending';
                            $detail->save();
                        });
                }

                // * approve
                if ($model->status == 'approve') {
                    $purchase_request_details
                        ->where('status', '!=', 'reject')
                        ->each(function ($detail) use ($model) {
                            $detail->status = $model->status;
                            $detail->save();
                        });
                }

                // * done
                if ($model->status == 'done') {
                    $model->purchase_request_details()
                        ->where('status', '!=', 'reject')
                        ->each(function ($detail) use ($model) {
                            if (in_array($detail->status, ['pending'])) {
                                $detail->status = 'void';
                            } else {
                                $detail->status = $model->status;
                            }

                            $detail->save();
                        });

                    // * if all void set parent to void
                    if ($model->purchase_request_details->where('status', 'void')->count() == $purchase_request_details->count()) {
                        $model->status = 'void';
                    }

                    // * create status log
                    create_activity_status_log_not_trait(PurchaseRequest::class, $model->id, 'your request was completed', $model->getOriginal('status'), 'done');
                }
            }
        });
    }

    /**
     * get property PurchaseRequestDetailItemCountAttribute
     *
     * @return
     */
    public function getPurchaseRequestDetailItemCountAttribute()
    {
        $total = 0;
        foreach ($this->purchase_request_details as $key => $value) {
            if ($value->status == 'approve') {
                $total += $value->jumlah_diapprove;
            }
        }

        return $total;
    }

    /**
     * get property IfPurchaseRequestDetailAllFromMasterItem
     *
     * @return bool
     */
    public function getIfPurchaseRequestDetailAllFromMasterItemAttribute()
    {
        $result = false;
        foreach ($this->purchase_request_details as $key => $value) {
            $result = $value->item_id ? false : true;
        }

        return $result;
    }

    /**
     * get property PurchaseRequestLockStocksData
     *
     * @return array|Collection
     */
    public function getPurchaseRequestLockStocksDataAttribute()
    {
        $purchase_request_detail_ids = $this->purchase_request_details->pluck('id')->toArray();
        $results = LockStock::whereIn('purchase_request_detail_id', $purchase_request_detail_ids)->get();

        return $results;
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
        $activity_logs = ActivityLog::where('subject_type', PurchaseRequest::class)
            ->where('subject_id', $this->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'event', 'causer_id', 'created_at']);
        $status_logs = ActivityStatusLog::where('reference_model', PurchaseRequest::class)
            ->where('reference_id', $this->id)
            ->orderByDesc('created_at')
            ->get();

        return compact('status_logs', 'activity_logs');
    }

    /**
     * get property purchaseOrderGeneralAttribute
     *
     * @return Collection
     */
    public function getPurchaseOrderGeneralAttribute(): Collection
    {
        $purchase_request_details = $this->purchase_request_details;

        $results = DB::table('purchase_order_general_detail_items')
            ->leftJoin('items', 'items.id', '=', 'purchase_order_general_detail_items.item_id')
            ->leftJoin('units', 'units.id', '=', 'items.unit_id')
            ->leftJoin('purchase_order_general_details', 'purchase_order_general_details.id', '=', 'purchase_order_general_detail_items.purchase_order_general_detail_id')
            ->leftJoin('purchase_order_generals', 'purchase_order_generals.id', '=', 'purchase_order_general_details.purchase_order_general_id')
            ->whereIn('purchase_order_general_detail_items.purchase_request_detail_id', $purchase_request_details->pluck('id')->toArray())
            ->selectRaw('
                purchase_order_generals.code as code,
                purchase_order_generals.date as date,
                items.nama as item_name,
                items.kode as item_code,
                units.name as unit_name,
                purchase_order_general_detail_items.quantity as quantity,
                purchase_order_general_detail_items.quantity_received as quantity_received,
                purchase_order_general_detail_items.status as status
            ')
            ->get();

        return $results;
    }

    /**
     * get property purchaseOrderServiceAttribute
     *
     * @return Collection
     */
    public function getPurchaseOrderServiceAttribute(): Collection
    {
        $purchase_request_details = $this->purchase_request_details;

        $results = DB::table('purchase_order_service_detail_items')
            ->leftJoin('items', 'items.id', '=', 'purchase_order_service_detail_items.item_id')
            ->leftJoin('units', 'units.id', '=', 'items.unit_id')
            ->leftJoin('purchase_order_service_details', 'purchase_order_service_details.id', '=', 'purchase_order_service_detail_items.purchase_order_service_detail_id')
            ->leftJoin('purchase_order_services', 'purchase_order_services.id', '=', 'purchase_order_service_details.purchase_order_service_id')
            ->whereIn('purchase_order_service_detail_items.purchase_request_detail_id', $purchase_request_details->pluck('id')->toArray())
            ->selectRaw('
                purchase_order_services.code as code,
                purchase_order_services.date as date,
                items.nama as item_name,
                items.kode as item_code,
                units.name as unit_name,
                purchase_order_service_detail_items.quantity as quantity,
                purchase_order_service_detail_items.quantity_received as quantity_received,
                purchase_order_service_detail_items.status as status
            ')
            ->get();

        return $results;
    }

    /**
     * get property PurchaseOrderAndItemReceivingReportAttribute
     *
     * @return mixed
     */
    public function getPurchaseOrderAndItemReceivingReportAttribute()
    {
        if ($this->type == 'general') {
            $result = $this->getPurchaseOrderGeneralAttribute();
        } elseif ($this->type == 'jasa') {
            $result = $this->getPurchaseOrderServiceAttribute();
        }

        return $result;
    }

    public function purchase_request_details()
    {
        return $this->hasMany(PurchaseRequestDetail::class);
    }

    /**
     * Get all of the purchaseRequestDetailsStockUsage for the PurchaseRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchaseRequestDetailsStockUsage(): HasMany
    {
        return $this->hasMany(PurchaseRequestDetail::class)->where('purchase_request_details.jumlah_diapprove', '>', 'purchase_request_details.quantity_used');
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Get the branch that owns the data.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    /**
     * Get the division that owns the data.
     */
    public function division()
    {
        return $this->belongsTo(Division::class)->withTrashed();
    }

    /**
     * Get the project that owns the data.
     */
    public function project()
    {
        return $this->belongsTo(Project::class)->withTrashed();
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
}
