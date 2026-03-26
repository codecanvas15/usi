<?php

namespace App\Models;

use App\Http\Helpers\ActivityLogHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
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
        'code',
        'nama',
        'alamat',
        'npwp',
        'email',
        'mobile_phone',
        'bussiness_phone',
        'whatsapp_number',
        'fax',
        'website',
        'lost_tolerance',
        'lost_tolerance_type',
        'term_of_payment',
        'top_days',
        'type',
        'is_complete'
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
            'alamat' => 'required|string|max:255',
            'lost_tolerance' => 'nullable|string|max:255',
            'lost_tolerance_type' => 'nullable|in:liter,percent',
        ];

        // * if has unique validation or diff rules when create or update
        if ($method == 'create') {
            $validate = array_merge($validate, [
                'nama' => 'required|string|max:255|unique:customers,nama',
                'npwp' => 'nullable|string|max:100',
                'email' => 'nullable|string|max:255',
                'bussiness_phone' => 'nullable|string|max:24',
                'mobile_phone' => 'nullable|string|max:24',
                'whatsapp_number' => 'nullable|string|max:24',
                'fax' => 'nullable|string|max:24',
                'website' => 'nullable|string|max:255',
            ]);
        } else {
            $validate = array_merge($validate, [
                'nama' => 'required|string|max:255|unique:customers,nama,' . $id,
                'npwp' => 'nullable|string|max:100',
                'email' => 'nullable|string|max:255',
                'bussiness_phone' => 'nullable|string|max:24',
                'mobile_phone' => 'nullable|string|max:24',
                'whatsapp_number' => 'nullable|string|max:24',
                'fax' => 'nullable|string|max:255',
                'website' => 'nullable|string|max:255',
            ]);
        }

        return $validate;
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $generateCode = generate_vendor_customer_code(strtoupper($model->nama));
            $code = 'CUS-' . $generateCode . '-001';

            $customer = Customer::where('code', $code)->first();
            if ($customer) {
                $replaceCode = explode('-', $customer->code);
                $tmpNumber = sprintf('%04d', (int)$replaceCode[2] + 1);
                $model->code = 'CUS-' . $generateCode . '-' . $tmpNumber;
            } else {
                $model->code = $code;
            }

            if (is_null($model->term_of_payment)) {
                $model->top_days = 0;
                $model->term_of_payment = 'cash';
            }

            if (strtolower($model->term_of_payment) == 'cash') {
                $model->top_days = 0;
            }
        });
        // * # change lost tolerance
        static::created(function ($model) {});

        static::updating(function ($model) {
            if (is_null($model->term_of_payment)) {
                $model->top_days = 0;
                $model->term_of_payment = 'cash';
            }

            if (strtolower($model->term_of_payment) == 'cash') {
                $model->top_days = 0;
            }
        });

        // * # change lost tolerance
        static::updated(function ($model) {});
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

    public function po_tradings()
    {
        return $this->hasMany(PoTrading::class);
    }

    public function so_tradings()
    {
        return $this->hasMany(SoTrading::class);
    }

    public function sh_numbers()
    {
        return $this->hasMany(ShNumber::class);
    }

    public function customer_coas()
    {
        return $this->hasMany(CustomerCoa::class);
    }

    public function customer_banks()
    {
        return $this->hasMany(CustomerBank::class);
    }

    public function sale_order_generals()
    {
        return $this->hasMany(SaleOrderGeneral::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function getLostToleranceNameAttribute()
    {
        return $this->lost_tolerance_type == 'percent' ? number_format($this->lost_tolerance * 100, 2) . '%' : "$this->lost_tolerance Liter";
    }
}
