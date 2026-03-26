<?php

namespace App\Models;

use App\Http\Helpers\ActivityLogHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WareHouse extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ActivityLogHelper;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'nama',
        'code',
        'deskripsi',
        'type',
        'jalan',
        'kota',
        'provinsi',
        'zip_code',
        'branch_id',
        'longitude',
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
            'nama' => 'required|string|max:100|unique:ware_houses,nama',
            'deskripsi' => 'nullable|string|max:255',
            'type' => 'nullable',
            'jalan' => 'nullable|string|max:255',
            'kota' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
        ];

        // * if has unique validation or diff rules when create or update
        if ($method == 'create') {
            $validate = array_merge($validate, [
                'nama' => 'required|string|max:100|unique:ware_houses,nama',
            ]);
        } else {
            $validate = array_merge($validate, [
                'nama' => 'required|string|max:100|unique:ware_houses,nama,' . $id,
            ]);
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

    public function itemCategory()
    {
        return $this->belongsTo(ItemCategory::class);
    }

    /**
     * Get the branch that owns the data.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    /**
     * Get the warehouse_stock_logs for the current model.
     */
    public function warehouse_stock_logs()
    {
        return $this->hasMany(WarehouseStockLog::class);
    }

    public function stockUsages()
    {
        return $this->hasMany(StockUsage::class);
    }

    public function stock_mutations()
    {
        return $this->hasMany(StockMutation::class);
    }

    public function stockCards()
    {
        $items = Item::whereIn('type', ['general', 'trading'])->orderByDesc('created_at')->get();

        $items = $items->map(function ($item, $key) {
            $query = StockMutation::where('item_id', $item->id)
                ->where('ware_house_id', $this->id)
                ->whereNull('is_return');

            $in = clone $query;
            $in = $in->sum('in');
            $out = clone $query;
            $out = $out->sum('out');

            $item->stock = floatFormat($in - $out);
            $item->minimum_stock = floatDotFormat($item->branch_min_stock);

            return $item;
        });

        return $items;
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
