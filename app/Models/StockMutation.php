<?php

namespace App\Models;

use App\Http\Helpers\ActivityLogHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class StockMutation extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ActivityLogHelper;

    protected $fillable = [
        'date',
        'ware_house_id',
        'branch_id',
        'item_id',
        'price_id',
        'document_model',
        'document_id',
        'document_code',
        'vendor_model',
        'vendor_id',
        'type',
        'in',
        'out',
        'booking_in',
        'booking_out',
        'note',
        'price_unit',
        'subtotal',
        'total',
        'value',
        'is_return',
        'tax',
        'revaluation_log',
        'ordering',
        'timestamp',
    ];

    protected static function booted()
    {
        self::creating(function ($model) {
            if (is_null($model->branch_id)) {
                $model->branch_id = auth()->user()?->branch_id;
            }

            $date = $model->date ?? date('Y-m-d');
            $model->ordering = generate_stock_mutation_order($date);
            $model->timestamp = $model->date . ' ' . Carbon::now()->format('H:i:s');
        });

        static::created(function ($model) {
            // * from item receiving report get exchange_rate from purchase order
            if ($model->document_model == "\App\Modols\ItemReceivingReport") {
                $exchange_rate = $model->document?->reference?->exchange_rate ?? 1;
            } else {
                $exchange_rate = 1;
            }

            $stock_before = $model->dataBefore();

            if (!$model->subtotal) {
                if ($model->in) {
                    $price = ($model->price->harga_beli ?? 0) * $exchange_rate;
                    $subtotal =  $price * $model->in;
                } else {
                    $price = ($stock_before->value ?? 0);
                    $subtotal =  $price * $model->out;
                }
            } else {
                $price = $model->price_unit;
                $subtotal = $model->subtotal;
            }

            $model->price_unit = $price;
            $model->subtotal = $subtotal;

            if ($model->in) {
                $get_total = ($stock_before->total ?? 0) + $subtotal;
            } else {
                $get_total = ($stock_before->total ?? 0) - $subtotal;
            }

            if ($get_total < 0) {
                $get_total = 0;
            }

            $model->total = $get_total;
            if ($model->in) {
                if (($model->stockBefore() + $model->in) != 0) {
                    $value = replaceComma($get_total / ($model->stockBefore() + $model->in));
                }
            }

            if ($model->out) {
                if (($model->stockBefore() - $model->out) != 0) {
                    $value = replaceComma($get_total / ($model->stockBefore() - $model->out));
                }
            }

            $model->value = $value ?? 0;

            $model->branch_id = $model->ware_house->branch_id;

            $model->save();
        });

        self::updating(function ($model) {});

        // self::deleted(function ($model) {
        //     $clear = app('App\Http\Controllers\ItemController')->clearValue($model->item_id, $model->ware_house_id);
        //     if ($clear) {
        //         app('App\Http\Controllers\ItemController')->regenerateValue($model->item_id, $model->ware_house_id);
        //     }
        // });
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

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function price()
    {
        return $this->belongsTo(Price::class);
    }

    public function document()
    {
        return $this->belongsTo($this->document_model, 'document_id');
    }

    public function vendor()
    {
        return $this->belongsTo($this->vendor_model, 'vendor_id')->withTrashed();
    }

    /**
     * Get the ware_house that owns the StockMutation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ware_house(): BelongsTo
    {
        return $this->belongsTo(WareHouse::class)->withTrashed();
    }

    public function dataBefore()
    {
        $query = StockMutation::where('ordering', '<', $this->ordering)
            ->whereDate('date', '<=', Carbon::parse($this->date))
            ->where('item_id', $this->item_id)
            ->orderBy('ordering', 'desc')
            ->first();

        // $stock_before = null;
        // foreach ($query as $key => $value) {
        //     if ($value->id == $this->id) {
        //         return null;
        //     }
        //     $stock_before = $value;
        //     if (isset($query[$key + 1])) {
        //         if ($query[$key + 1]->id == $this->id) {
        //             break;
        //         }
        //     }
        // }

        return $query;
    }

    public function stockBefore($model_id = null)
    {
        $in = StockMutation::where('ordering', '<', $this->ordering)
            ->whereDate('date', '<=', Carbon::parse($this->date))
            ->where('item_id', $this->item_id)
            ->orderBy('ordering', 'desc')
            ->sum('in');

        $out = StockMutation::where('ordering', '<', $this->ordering)
            ->whereDate('date', '<=', Carbon::parse($this->date))
            ->where('item_id', $this->item_id)
            ->orderBy('ordering', 'desc')
            ->sum('out');

        return $in - $out;
    }
}
