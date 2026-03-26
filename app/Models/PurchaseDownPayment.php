<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PurchaseDownPayment extends Model
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
        'vendor_id',
        'currency_id',
        'exchange_rate',
        'purchase_id',
        'date',
        'due_date',
        'code',
        'total_amount',
        'is_include_tax',
        'subtotal',
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

        self::created(function ($model) {});

        self::updated(function ($model) {
            // ! IF STATUS IS CHANGED #####################################
            if ($model->getOriginal('status') != $model->status) {
                if ($model->status == 'approve') {
                }

                // * if revert, void, cancel
                if (in_array($model->status, ['revert', 'void', 'cancel'])) {
                }
            }
            // ! end IF STATUS IS CHANGED #################################
        });

        self::deleted(function ($model) {});
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
     * Get the branch that owns the PurchaseDownPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the vendor that owns the PurchaseDownPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class)->withTrashed();
    }

    /**
     * Get the currency that owns the PurchaseDownPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get all of the purchase_down_payment_taxes for the PurchaseDownPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchase_down_payment_taxes(): HasMany
    {
        return $this->hasMany(PurchaseDownPaymentTax::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id')->withTrashed();
    }

    public function fund_submissions()
    {
        return $this->hasMany(FundSubmission::class);
    }


    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
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
            ->where('model', PurchaseDownPayment::class);
    }
}
