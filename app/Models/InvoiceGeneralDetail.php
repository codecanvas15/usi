<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceGeneralDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_general_id',
        'sale_order_general_id',
        'sale_order_general_detail_id',
        'delivery_order_general_detail_id',
        'item_id',
        'unit_id',
        'quantity',
        'invoice_quantity',
        'price',
        'sub_total',
        'total_tax',
        'total',
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
     * Get the invoice_general that owns the InvoiceGeneralDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice_general(): BelongsTo
    {
        return $this->belongsTo(InvoiceGeneral::class);
    }

    /**
     * Get the sale_order_general_detail that owns the InvoiceGeneralDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale_order_general_detail(): BelongsTo
    {
        return $this->belongsTo(SaleOrderGeneralDetail::class);
    }

    /**
     * Get the delivery_order_general that owns the DeliveryOrderGeneral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function delivery_order_general(): BelongsTo
    {
        return $this->belongsTo(DeliveryOrderGeneral::class);
    }

    /**
     * Get the delivery_order_general_detail that owns the DeliveryOrderGeneralDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function delivery_order_general_detail(): BelongsTo
    {
        return $this->belongsTo(DeliveryOrderGeneralDetail::class);
    }

    /**
     * Get the item that owns the InvoiceGeneralDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the unit that owns the InvoiceGeneralDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the sale_order_general that owns the InvoiceGeneralDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale_order_general(): BelongsTo
    {
        return $this->belongsTo(SaleOrderGeneral::class);
    }

    /**
     * Get all of the invoice_general_detail_taxes for the InvoiceGeneralDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoice_general_detail_taxes(): HasMany
    {
        return $this->hasMany(InvoiceGeneralDetailTax::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
