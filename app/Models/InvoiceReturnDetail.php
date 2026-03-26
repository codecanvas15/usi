<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceReturnDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_return_id',
        'item_id',
        'unit_id',
        'reference_model',
        'reference_id',
        'lpb_qty',
        'qty',
        'return_qty',
        'price',
        'subtotal',
        'tax_amount',
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
        $validate = [
            'invoice_return_id' => 'required',
            'item_id' => 'required',
            'reference_model' => 'required',
            'reference_id' => 'required',
            'lpb_qty' => 'required',
            'qty' => 'required',
            'return_qty' => 'required',
            'price' => 'required',
            'subtotal' => 'required',
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

    public function invoice_return()
    {
        return $this->belongsTo(InvoiceReturn::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function invoice_return_taxes()
    {
        return $this->hasMany(InvoiceReturnTax::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function reference()
    {
        return $this->belongsTo($this->reference_model, 'reference_id');
    }
}
