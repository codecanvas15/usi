<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ItemReceivingReportCoa extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'coa_id',
        'item_receiving_report_id',
        'type',
        'reference_model',
        'reference_id',
        'bind_to',
        'item_receiving_report_detail_id',
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
     * Get the coa that owns the data.
     */
    public function coa()
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    /**
     * Get the item_receiving_report that owns the data.
     */
    public function item_receiving_report()
    {
        return $this->belongsTo(ItemReceivingReport::class)->withTrashed();
    }

    /**
     * Get the reference that owns the data.
     */
    public function reference()
    {
        return $this->belongsTo($this->reference_model, 'reference_id');
    }

    /**
     * Get the item_receiving_report_detail that owns the data.
     */
    public function item_receiving_report_detail()
    {
        return $this->belongsTo(ItemReceivingReportDetail::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
