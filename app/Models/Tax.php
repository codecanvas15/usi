<?php

namespace App\Models;

use App\Http\Helpers\ActivityLogHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
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
        'name',
        'category',
        'description',
        'value',
        'coa_sale',
        'coa_purchase',
        'is_discount',
        'type',
        'is_default',
        'is_show_percent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'float',
    ];

    protected $appends = ['tax_name_without_percent', 'tax_name_with_percent'];

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
            'name' => 'required|max:50|string',
            'description' => 'nullable|max:255|string',
            'value' => 'required',
            'coa_sale' => 'nullable|exists:coas,id',
            'coa_purchase' => 'nullable|exists:coas,id',
            'is_discount' => 'nullable',
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

    public function coa_sale_data()
    {
        return $this->belongsTo(Coa::class, 'coa_sale')->withTrashed();
    }

    public function coa_purchase_data()
    {
        return $this->belongsTo(Coa::class, 'coa_purchase')->withTrashed();
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function getTaxNameWithoutPercentAttribute()
    {
        $explodes = explode(' ', $this->name);

        $final_string = '';
        foreach ($explodes as $key => $explode) {
            if (strpos($explode, '%') == false) {
                if ($key != 0) {
                    $final_string .= ' ';
                }
                $final_string .= $explode;
            }
        }

        return trim($final_string);
    }

    public function getTaxNameWithPercentAttribute()
    {
        if (strpos($this->name, '%') === false) {
            return $this->name . ' ' . ($this->value * 100) . '%';
        }

        return $this->name;
    }
}
