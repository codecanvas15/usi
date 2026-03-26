<?php

namespace App\Models;

use App\Http\Helpers\ActivityLogHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Item extends Model
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
        'kode',
        'nama',
        'deskripsi',
        'status',
        'item_category_id',
        'unit_id',
        'type',
        'is_complete',
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
        $status = '';
        foreach (get_item_status() as $key => $value) {
            $status .= $key . ',';
        }
        $validate = [
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:65535',
            'kode' => 'required|string|max:255',
            'status' => 'required|in:' . $status,
            'item_category_id' => 'required|exists:item_categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'type' => 'nullable|in:general,trading,service,transport',
            'file' => 'mimes:jpg,jpeg,png,pdf|max:6144',
        ];

        // * if has unique validation or diff rules when create or update
        if ($method == 'create') {
            $validate = array_merge($validate, [
                'kode' => 'unique:items,kode'
            ]);
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
            // $model->kode = 'IT-' . date('Ymd') . Str::random(4);
            if ($model->status == null) {
                $model->status = 'active';
            }
        });

        static::deleting(fn ($model) => self::self_delete($model));
    }

    /**
     * self delete - delete price and item sutitute value
     *
     * @param Item $model
     */
    protected static function self_delete($model): void
    {
        $model->prices()->delete();
        $model->item_subtitutes()->delete();
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

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function po_trading_details()
    {
        return $this->hasMany(PoTradingDetail::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function so_trading_details()
    {
        return $this->hasMany(SoTradingDetail::class);
    }

    public function item_subtitutes()
    {
        return $this->hasMany(ItemSubtitute::class, 'parent_id');
    }

    public function item_type()
    {
        return $this->belongsTo(ItemType::class);
    }

    public function item_category()
    {
        return $this->belongsTo(ItemCategory::class)->withTrashed();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function getHargaAttribute()
    {
        if ($this->harga != $this->prices->harga) {
            return $this->prices->harga;
        }

        return $this->harga;
    }

    public function itemMinimums()
    {
        return $this->hasMany(ItemMinimum::class);
    }

    public function getBranchMinStockAttribute()
    {
        $branch_id = auth()->user()->branch_id;
        $qty = "0.00";
        if ($branch_id) {
            $item_minimums = $this->item_minimums;

            if ($item_minimums) {
                $qty = $item_minimums->where('branch_id', $branch_id)
                    ->first();

                if ($qty) {
                    $qty = $qty->qty;
                } else {
                    $qty = "0.00";
                }
            }
        }

        return $qty;
    }

    public function mainStock($ware_house_id = null)
    {
        // $branch_id = auth()->user()->branch_id ?? $branch_id ?? auth()->user()->temp_branch_id;
        $query = StockMutation::where('item_id', $this->id)->whereNull('is_return');

        if ($ware_house_id != null) {
            $query->where('ware_house_id', $ware_house_id);
        }

        $in = $query->sum('in');
        $out = $query->sum('out');

        return floatFormat($in - $out);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function getCurrentValue($branch_id = null)
    {
        $data = StockMutation::where('item_id', $this->id)
            // ->where('branch_id', auth()->user()->branch_id ?? auth()->user()->temp_branch_id, $branch_id)
            ->orderBy('ordering', 'desc')
            ->whereNotNull('value')
            ->where('value', '>', 0)
            ->first();

        return $data->value ?? 0;
    }

    public function dataBefore($ware_house_id = null)
    {
        $query = StockMutation::where('ware_house_id', $ware_house_id)
            ->where('item_id', $this->item_id)
            ->whereNull('is_return')
            ->orderBy('id', 'desc')
            ->first();

        return $query;
    }

    public function stock_mutations()
    {
        return $this->hasMany(StockMutation::class);
    }
}
