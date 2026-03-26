<?php

namespace App\Models;

use App\Http\Helpers\ActivityLogHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
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
        'type',
        'alamat',
        'npwp',
        'email',
        'business_phone',
        'mobile_phone',
        'whatsapp',
        'fax',
        'website',
        'term_of_payment',
        'top_days',
        'business_field_id',
        'loss_tolerance',
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
            'nama' => 'required|max:255|string',
            'alamat' => 'required|max:255|string',
            'npwp' => 'nullable|max:24|string',
            'email' => 'nullable|string|email|max:255',
            'business_phone' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:13',
            'mobile_phone' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:13',
            'whatsapp' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:13',
            'fax' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:6|max:24',
            'website' => 'nullable|url',
            // 'nomor_rekening' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:6|max:24',
            // 'jenis_bank' => 'nullable|string|max:255',
            // 'business_bank_name' => 'nullable|string|max:255'
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
            $generateCode = generate_vendor_customer_code(strtoupper($model->nama));
            $code = 'VEN-' . $generateCode . '-001';

            $vendor = Vendor::where('code', $code)->first();
            if ($vendor) {
                $replaceCode = explode('-', $vendor->code);
                $tmpNumber = sprintf("%04d", (int)$replaceCode[2] + 1);
                $model->code = 'VEN-' . $generateCode . '-' . $tmpNumber;
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

        static::updating(function ($model) {
            if (strtolower($model->term_of_payment) == 'cash') {
                $model->top_days = 0;
            }

            if (is_null($model->term_of_payment)) {
                $model->top_days = 0;
                $model->term_of_payment = 'cash';
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

    /**
     * The user that belong to the Vendor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'vendor_users');
    }

    public function business_field(): BelongsTo
    {
        return $this->belongsTo(BusinessField::class, 'business_field_id')->withTrashed();
    }

    public function vendor_coas()
    {
        return $this->hasMany(VendorCoa::class);
    }

    /**
     * Get all of the vendor_banks for the Vendor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vendor_banks(): HasMany
    {
        return $this->hasMany(VendorBank::class);
    }

    public function supplier_invoice_parents()
    {
        return $this->hasMany(SupplierInvoiceParent::class);
    }

    public function cash_advance_payments()
    {
        return $this->hasMany(CashAdvancePayment::class, 'to_id', 'id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
