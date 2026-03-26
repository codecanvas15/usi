<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvTradingAddOnTax extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inv_trading_add_on_id',
        'tax_id',
        'value',
        'total'
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
     * Get the invoice_trading_additional_item that owns the InvoiceTradingAdditionalTax
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inv_trading_add_on(): BelongsTo
    {
        return $this->belongsTo(InvTradingAddOn::class);
    }

    /**
     * Get the tax that owns the InvoiceTradingAdditionalTax
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class)->withTrashed();
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
