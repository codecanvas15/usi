<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaleOrderGeneralDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sale_order_general_id',
        'purchase_order_general_id',
        'item_id',
        'unit_id',
        'price_before_discount',
        'discount',
        'price',
        'amount',
        'amount_paired',
        'sended',
        'sub_total',
        'total',
        'notes',
        'status',
        'status_pairing',
    ];

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
            // * if updating sended update and check status
            if ($model->getOriginal('sended') != $model->sended) {
                if ($model->sended >= $model->amount) {
                    $model->status = 'done';
                } elseif ($model->sended > 0 && $model->sended < $model->amount) {
                    $model->status = 'partial';
                } else {
                    $model->status = 'approve';
                }
            }
        });

        static::updated(function ($model) {
            if ($model->getOriginal('sended') != $model->sended) {
                $all_so_general_details = self::where('sale_order_general_id', $model->sale_order_general_id)
                    ->whereNotIn('status', ['reject', 'cancel', 'void'])
                    ->get();

                if ($all_so_general_details->where('status', 'done')->count() == $all_so_general_details->count()) {
                    $model->sale_order_general->status = 'done';
                } else if ($all_so_general_details->where('status', 'partial')->count() > 0) {
                    $model->sale_order_general->status = 'partial-sent';
                } else {
                    $model->sale_order_general->status = 'approve';
                }
                $model->sale_order_general->save();
            }

            $data_undone = self::where('sale_order_general_id', $model->sale_order_general_id)
                ->whereNotIn('status', ['reject', 'cancel', 'void'])
                ->get();

            $data_done = self::where('sale_order_general_id', $model->sale_order_general_id)
                ->where('status', 'done')
                ->get();

            if ($data_undone->count() == $data_done->count()) {
                $model->sale_order_general->status = 'done';
                $model->sale_order_general->save();
            }
        });
    }

    /**
     * Get the sale_order_general that owns the SaleOrderGeneralDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale_order_general(): BelongsTo
    {
        return $this->belongsTo(SaleOrderGeneral::class);
    }

    /**
     * Get the item that owns the SaleOrderGeneralDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    /**
     * Get the unit that owns the SaleOrderGeneralDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class)->withTrashed();
    }

    /**
     * Get all of the sale_order_general_detail_taxes for the SaleOrderGeneralDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sale_order_general_detail_taxes(): HasMany
    {
        return $this->hasMany(SaleOrderGeneralDetailTax::class, 'so_general_detail_id');
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function delivery_order_general_details()
    {
        return $this->hasMany(DeliveryOrderGeneralDetail::class, 'sale_order_general_detail_id');
    }
}
