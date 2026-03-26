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

class SaleOrderGeneral extends Model
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
        'created_by',
        'approved_by',
        'type',
        'tanggal',
        'kode',
        'sub_total',
        'total',
        'exchange_rate',
        'status',
        'quotation',
        'no_po_external',
        'drop_point',
        'is_include_tax',
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
            ->setDescriptionForEvent(fn (string $eventName) => "This data has been {$eventName}")
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
            'customer_id' => 'required|exists:customers,id',
            'date' => 'nullable|date',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'required',

            'item_id.*' => 'required|exists:items,id',
            'unit_id.*' => 'required|exists:units,id',
            'price.*' => 'required',
            'amount.*' => 'required',
            'no_po_external' => 'nullable|string|max:255'
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
            // * branch
            if ($model->branch_id == null) {
                $model->branch_id = get_current_branch_id();
            }

            // * status
            if ($model->status == null) {
                $model->status = 'pending';
            }

            // * date
            if ($model->tanggal == null) {
                $model->tanggal =  Carbon::today()->format('Y-m-d');
            }

            // * created by
            if ($model->created_by == null) {
                $model->created_by = auth()->user()->id;
            }
        });

        static::updating(function ($model) {
            // * approve
            if ($model->status == 'approve' and $model->status != $model->getOriginal('status')) {
                // * model detail
                $model->sale_order_general_details
                    ->whereNotIn('status', ['reject', 'void', 'cancel'])
                    ->each(function ($detail) {
                        $detail->status = 'approve';
                        $detail->save();
                    });
            }

            // * reject
            if ($model->status == 'reject' and $model->status != $model->getOriginal('status')) {
                // * model detail
                $model->sale_order_general_details
                    ->whereNotIn('status', ['reject', 'void', 'cancel'])
                    ->each(function ($detail) {
                        $detail->status = 'reject';
                        $detail->save();
                    });
            }

            // * revert void
            if (in_array($model->status, ['revert', 'void']) and $model->status != $model->getOriginal('status')) {
                // * model detail
                $model->sale_order_general_details
                    ->whereNotIn('status', ['reject', 'void', 'cancel'])
                    ->each(function ($detail) {
                        $detail->status = 'revert';
                        $detail->save();
                    });
            }
        });
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
     * Get the customer that owns the SaleOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    /**
     * Get the currency that owns the SaleOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    /**
     * Get the branch that owns the SaleOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    /**
     * Get the user that create the SaleOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_by_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Get the user that approve the SaleOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approved_by_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    /**
     * Get all of the saleOrderGeneralDetails for the SaleOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sale_order_general_details(): HasMany
    {
        return $this->hasMany(SaleOrderGeneralDetail::class);
    }

    public function invoice_generals()
    {
        return $this->hasMany(InvoiceGeneral::class);
    }

    /**
     * getCheckAvailableDateAttribute for generated SO General
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
