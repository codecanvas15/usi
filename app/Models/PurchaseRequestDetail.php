<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PurchaseRequestDetail extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_request_id',
        'item_id',
        'item',
        'jumlah',
        'jumlah_diapprove',
        'quantity_used',
        'approve_desc',
        'status',
        'unit_id',
        'file',
        'reject_reason',
        'keterangan'
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
            '' => ''
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
            if (!$model->status) {
                $model->status = 'pending';
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('status') && !in_array($model->status, ['done', 'partial']) && $model->lock_stock) {
                $model->lock_stock->status = $model->status;
                $model->lock_stock->save();
            }
        });
    }


    /**
     * set attrbutes modelp
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

    public function purchase_request()
    {
        return $this->belongsTo(PurchaseRequest::class)->withTrashed();
    }

    public function item_data()
    {
        return $this->belongsTo(Item::class, 'item_id')->withTrashed();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class)->withTrashed();
    }

    public function lock_stock()
    {
        return $this->hasOne(LockStock::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
