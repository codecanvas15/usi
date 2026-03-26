<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvTradingAddOn extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_trading_id',
        'item_id',
        'quantity',
        'price',
        'sub_total',
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
     * Get the item that owns the InvoiceTradingAdditionalItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the invoiceTrading that owns the InvoiceTradingAdditionalItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice_trading(): BelongsTo
    {
        return $this->belongsTo(InvoiceTrading::class);
    }

    /**
     * Get all of the invoiceTradingAdditionalTaxes for the InvoiceTradingAdditionalItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inv_trading_add_on_tax(): HasMany
    {
        return $this->hasMany(InvTradingAddOnTax::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
