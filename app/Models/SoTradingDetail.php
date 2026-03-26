<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SoTradingDetail extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'so_trading_id',
        'item_id',
        'price_id',
        'harga',
        'jumlah',
        'sudah_dialokasikan',
        'sudah_dikirim',
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
    protected $table = 'sale_order_details';

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
            'so_trading_id' => 'required|exists:so_tradings,id',
            'item_id' => 'required|exists:items,id',
            'price_id' => 'required|exists:prices,id',
            'harga' => 'nullable|string|max:11',
            'jumlah' => 'required|string|max:8',
            'sudah_dialokasikan' => 'nullable|string|max:11',
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

            $model->status = 'pending';
            $model->created_by = auth()->user()->id;
        });

        static::updating(function ($model) {
            // * status updating
            if ($model->getOriginal('status') != $model->status) {
                // cancel
                if ($model->status == 'cancel') {
                    if ($model->so_trading->status != 'cancel') {
                        $model->so_trading->status = 'cancel';
                        $model->so_trading->save();
                    }
                }

                // reject
                if ($model->status == 'reject') {
                    if ($model->so_trading->status != 'reject') {
                        $model->so_trading->status = 'reject';
                    }
                }

                // done or close
                if ($model->status == 'done') {
                    if ($model->sudah_dialokasikan != $model->sudah_dikirim) {
                        throw new \Exception('Sudah dialokasikan tidak sama dengan sudah dikirim');
                    } else {
                        $model->so_trading->pairing_status = 'done';
                    }
                }
            }

            // sudah dialokasikan is updating
            if ($model->getOriginal('sudah_dialokasikan') != $model->sudah_dialokasikan) {
                if (!in_array($model->so_trading->status, ['delivery_complete', 'partial_sent'])) {
                    if ($model->sudah_dialokasikan == 0) {
                        $model->so_trading->pairing_status = 'pending';
                        $model->so_trading->status = 'approve';
                    }

                    if ($model->sudah_dialokasikan != 0) {
                        if ($model->sudah_dialokasikan >= $model->jumlah) {
                            $model->so_trading->pairing_status = 'pairing';
                        }

                        if ($model->sudah_dialokasikan < $model->jumlah) {
                            $model->so_trading->pairing_status = 'partial';
                            $model->so_trading->status = 'paired';
                        }

                        if ($model->sudah_dialokasikan == $model->jumlah) {
                            $model->so_trading->pairing_status = 'done';
                            $model->so_trading->status = 'ready';
                        }
                    }
                }
            }

            // sudah dikirim is updating
            if ($model->sudah_dikirim != $model->getOriginal('sudah_dikirim')) {

                // * quantity all sended
                if ($model->jumlah == $model->sudah_dikirim) {
                    if ($model->so_trading->status != 'delivery_complete') {
                        $model->so_trading->status = 'delivery_complete';
                    }
                }

                // * quantity partials sended
                if ($model->sudah_dikirim > 0 and $model->jumlah > $model->sudah_dikirim) {
                    $model->so_trading->status = 'partial_sent';
                }

                if ($model->sudah_dikirim == 0) {
                    $model->so_trading->status = 'ready';
                }
            }

            // * save so trading or parent data
            try {
                $model->so_trading->save();
            } catch (\Throwable $th) {
                throw $th;
            }
        });
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
        return $this->jumlah - $this->sudah_dialokasikan;
    }

    public function create_by()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function so_trading()
    {
        return $this->belongsTo(SoTrading::class)->withTrashed();
    }

    public function price()
    {
        return $this->belongsTo(Price::class)->withTrashed();
    }

    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    public function pairing_so_to_pos()
    {
        return $this->hasMany(PairingSoToPo::class);
    }
}
