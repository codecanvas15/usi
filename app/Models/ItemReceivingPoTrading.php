<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ItemReceivingPoTrading extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id',
        'price_id',
        'item_receiving_report_id',
        'liter_15',
        'liter_obs',
        'ware_house_id',
        'loading_order',
        'sub_total',
        'tax_total',
        'total',
        'vechicle_fleet_id',
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
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::updating(function ($model) {
            if ($model->isDirty('liter_obs')) {
                $po_trading_detail = $model->item_receiving_report->reference->po_trading_detail;

                $po_trading_detail->jumlah_lpbs -= $model->getOriginal('liter_obs');
                $po_trading_detail->jumlah_lpbs += $model->liter_obs;
            }
        });
    }

    public function item_receiving_report()
    {
        return $this->belongsTo(ItemReceivingReport::class);
    }

    /**
     * Get the vechicle_fleet that owns the data.
     */
    public function vechicle_fleet()
    {
        return $this->belongsTo(VechicleFleet::class, 'vechicle_fleet_id', 'id');
    }

    public function ware_house()
    {
        return $this->belongsTo(WareHouse::class)->withTrashed();
    }

    public function price()
    {
        return $this->belongsTo(Price::class)->withTrashed();
    }

    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
