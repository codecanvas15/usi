<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Quotation extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'date',
        'sub_total',
        'sub_total_after_tax',
        'additional_sub_total',
        'additional_sub_total_after_tax',
        'additional_total',
        'total',
        'branch_id',
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
            'customer_id' => 'required|exists:customers,id',
            'item_id' => 'required|exists:items,id',
            'price' => 'required',
            'tax_id_' => 'nullable|string',
            'quantity' => 'required',
            'date' => 'required',
            'quotation_add_on_type_id' => 'nullable',
            'additional_item' => 'nullable',
            'additional_price' => 'nullable',
            'additional_quantity' => 'nullable',
            'additional_tax_id' => 'nullable',
            'keterangan' => 'nullable|string',
            // 'jumlah_barang' => 'required|string|max:8',
        ];

        // * if has unique validation or diff rules when create or update
        if ($method == 'create') {
            $validate = array_merge($validate, []);
        } else {
            $validate = array_merge($validate, [
                'customer_id' => 'nullable|exists:customers,id',
                'item_id' => 'nullable|exists:items,id',
                'price' => 'nullable',
                'tax_id_' => 'nullable|array',
                'quantity' => 'nullable',
                'date' => 'nullable',
                'quotation_add_on_type_id' => 'nullable',
                'additional_item' => 'nullable',
                'additional_price' => 'nullable',
                'additional_quantity' => 'nullable',
                'additional_tax_id' => 'nullable',
                'keterangan' => 'nullable|string',
            ]);
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
            // Generate kode
            $code2 = $model->customer;
            $kode = generate_code_with_cus_name(
                model: self::class,
                code: 'QUO',
                code2: $code2,
                date_column: 'date',
                date: $model->created_at ?? \Carbon\Carbon::now()->format('Y-m-d'),
                filter: [],
            );

            $model->setAttribute('code', $kode);
            $model->setAttribute('branch_id', Auth::user()?->branch_id);

            if (Auth::check()) {
                if (!$model->branch_id) {
                    $model->branch_id = Auth::user()->branch_id;
                }
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
     * Get the customer that owns the Quotation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    /**
     * Get currency
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    public function price()
    {
        return $this->belongsTo(Price::class)->withTrashed();
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id')->withTrashed();
    }

    /**
     * Get the quotation items that
     */
    public function quotationItems()
    {
        return $this->hasMany(QuotationItem::class);
    }

    /**
     * Get the branch that owns the data.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function quotation_add_on_values()
    {
        return $this->hasMany(QuotationAddOnValue::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
