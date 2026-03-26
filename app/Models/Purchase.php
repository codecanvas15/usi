<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Purchase extends Model
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
        'tipe',
        'model_reference',
        'status',
        'branch_id',
        'vendor_id',
        'model_id',
        'currency_id',
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
            '' => '',
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

    public function getStatusHtmlAttribute()
    {
        if ($this->tipe == 'trading') {
            $status = status_purchase_orders();
        } elseif ($this->tipe == 'general') {
            $status = purchase_general_status();
        } elseif ($this->tipe == 'jasa') {
            $status = purchase_service_status();
        } elseif ($this->tipe == 'transportir') {
            $status = purchase_transport_status();
        }

        $color = array_key_exists($this->status, $status) ? $status[$this->status]['color'] : 'secondary';
        $text = array_key_exists($this->status, $status) ? $status[$this->status]['text'] : 'Undefined';
        $label = array_key_exists($this->status, $status) ? $status[$this->status]['label'] : 'Undefined';

        return "<div class='badge badge-lg badge-$color'>
                    $label - $text
                </div>";
    }

    public function poHasLpb()
    {
        $type = '';

        if ($this->tipe === 'trading') {
            $type = 'trading';
        } else if ($this->tipe === 'general') {
            $type = 'general';
        } else if ($this->tipe === 'jasa') {
            $type = 'jasa';
        } else {
            $type = 'transport';
        }

        $data = ItemReceivingReport::where('tipe', $type)
            ->where('reference_model', $this->model_reference)
            ->where('reference_id', $this->model_id)
            ->whereIn('status', ['approve', 'done'])
            ->first();

        return !is_null($data);
    }

    public function reference()
    {
        return $this->hasOne($this->model_reference);
    }

    public function general()
    {
        return $this->hasOne(PurchaseOrderGeneral::class);
    }

    public function trading()
    {
        return $this->hasOne(PoTrading::class);
    }

    public function service()
    {
        return $this->hasOne(PurchaseOrderService::class);
    }

    public function transport()
    {
        return $this->hasOne(PurchaseTransport::class);
    }

    public function purchase_down_payments()
    {
        return $this->hasMany(PurchaseDownPayment::class);
    }

    /**
     * Get the branch that owns the data.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     *
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

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    function cash_advance_payments()
    {
        return $this->hasMany(CashAdvancePayment::class);
    }

    public function item_receiving_reports()
    {
        return $this->hasMany(ItemReceivingReport::class);
    }
}
