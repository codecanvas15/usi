<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PoTradingDetail extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'po_trading_id',
        'item_id',
        'price_id',
        'jumlah',
        'harga',
        'type',
        'keterangan',
        'sudah_dialokasikan',
        'jumlah_lpbs',
        'tax_id',
        'additional_type',
        'value_tax',
        'discount_per_liter',
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
            ->setDescriptionForEvent(fn(string $eventName) => "This data has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchase_order_details';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if ($model->sudah_dialokasikan == null) {
                $model->sudah_dialokasikan = 0;
            }

            $model->status = 'pairing';
            $model->jumlah_lpbs = 0;
            $model->created_by = auth()->user()->id;
        });

        static::updating(function ($model) {
            // ! pairing status trigger
            /**
             * ? if this field is changing
             * jumlah_lpbs
             * sudah_dialokasikan
             */

            if ($model->getOriginal('jumlah_lpbs') != $model->jumlah_lpbs or $model->getOriginal('sudah_dialokasikan') != $model->sudah_dialokasikan) {
                // * jumlah lpbs is changing
                if ($model->getOriginal('jumlah_lpbs') != $model->jumlah_lpbs) {
                    // * jika pairing status is pending
                    if ($model->po_trading->pairing_status == 'pending') {
                        $model->po_trading->pairing_status = 'ready';
                    }

                    // * jika purchase status is approve
                    if ($model->po_trading->status == 'approve') {
                        $model->po_trading->status = 'ready';
                    }
                }

                // * sudah dialokasikan is changing
                if ($model->getOriginal('sudah_dialokasikan') != $model->sudah_dialokasikan) {
                    // * jika sudah dialokasikan is 0
                    if ($model->sudah_dialokasikan == 0) {
                        $model->po_trading->pairing_status = 'pairing';
                        $model->po_trading->status = 'approve';
                    }

                    // * jika sudah dialokasikan is not 0
                    if ($model->sudah_dialokasikan != 0) {
                        if ($model->sudah_dialokasikan >= $model->jumlah) {
                            $model->po_trading->pairing_status = 'pairing';
                        }

                        if ($model->sudah_dialokasikan < $model->jumlah) {
                            $model->po_trading->pairing_status = 'partial';
                        }
                    }

                    // * jika sudah dialokasikan is equal to jumlah lpbs
                    if ($model->sudah_dialokasikan == $model->jumlah) {
                        $model->po_trading->pairing_status = 'done';
                        if ($model->po_trading->status == 'approve') {
                            $model->po_trading->status = 'ready';
                        }
                    }
                }

                try {
                    $model->po_trading->save();
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
        });

        static::updated(function ($model) {
            // sudah dialokasikan and sudah dialokasikan enought update status to done
            if ($model->getOriginal('sudah_dialokasikan') != $model->sudah_dialokasikan) {
                $jumlah = $model->type == 'Kilo Liter' ? $model->jumlah * 1000 : $model->jumlah;
                if ($model->sudah_dialokasikan == $jumlah and !in_array($model->status, ['reject', 'void', 'cancel', 'done'])) {
                    $model->status = 'done';
                    $model->save();
                }
            }

            if ($model->getOriginal('jumlah_lpbs') != $model->jumlah_lpbs) {
                if ($model->jumlah_lpbs >= $model->jumlah and !in_array($model->status, ['reject', 'void', 'cancel'])) {
                    if ($model->po_trading->status != 'done') {
                        $model->po_trading->status = 'done';
                        $model->po_trading->save();
                    }
                }
            }
        });
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

    public function getAlokasiTersediaAttribute()
    {
        if ($this->jumlah_lpbs == null) {
            return 0;
        }
        return $this->jumlah_lpbs - $this->sudah_dialokasikan;
    }

    /**
     * getTotalPriceAttribute
     *
     * @return mixed
     */
    public function getTotalPriceAttribute()
    {
        if ($this->type == 'Kilo Liter') {
            return ($this->jumlah * 100) * $this->harga;
        } else {
            return $this->jumlah * $this->harga;
        }
    }

    public function create_by()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function po_trading()
    {
        return $this->belongsTo(PoTrading::class)->withTrashed();
    }

    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    public function sh_number()
    {
        return $this->belongsTo(ShNumber::class)->withTrashed();
    }

    /**
     * Get the tax that owns the data.
     */
    public function tax()
    {
        return $this->belongsTo(Tax::class)->withTrashed();
    }

    public function pairing_po_to_sos()
    {
        return $this->hasMany(PairingSoToPo::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
