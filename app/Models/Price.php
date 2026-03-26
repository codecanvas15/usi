<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Price extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id',
        'nama',
        'period_id',
        'harga_beli',
        'harga_jual',
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
            'item_id' => 'required|exists:items,id',
            'nama' => 'nullable|max:255|string',
            'period_id' => 'nullable|exists:periods,id',
            'harga_beli' => 'required',
            'harga_jual' => 'required',
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

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function so_trading_details()
    {
        return $this->hasMany(SoTradingDetail::class);
    }

    public function po_trading_details()
    {
        return $this->hasMany(PoTradingDetail::class);
    }

    public function price_customers()
    {
        return $this->hasMany(PriceCustomer::class);
    }

    public function price_customers_with_trashed()
    {
        return $this->hasMany(PriceCustomer::class)->withTrashed();
    }

    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    public function period()
    {
        return $this->belongsTo(Period::class)->withTrashed();
    }

    public function toArray()
    {
        $array = parent::toArray();
        // $array['harga_jual'] = $this->harga_jual;
        // $array['harga_beli'] = $this->harga_beli;
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function mainStock($ware_house_id = null)
    {
        $in = StockMutation::where('item_id', $this->item_id)
            ->where('price_id', $this->id)
            ->whereNull('is_return');
        if ($ware_house_id != null) {
            $in->where('ware_house_id', $ware_house_id);
        }
        $in = $in->sum('in');

        $out = StockMutation::where('item_id', $this->item_id)
            ->where('price_id', $this->id)
            ->whereNull('is_return');
        if ($ware_house_id != null) {
            $out->where('ware_house_id', $ware_house_id);
        }
        $out = $out->sum('out');

        return floatFormat($in - $out);
    }
}
