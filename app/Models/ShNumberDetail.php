<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShNumberDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sh_number_id',
        'alamat',
        'longitude',
        'latitude',
        'type',
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
            'sh_number_id' => 'required|exists:sh_numbers,id',
            'alamat' => 'required|string|max:255',
            'longitude' => 'nullable|string|max:24',
            'latitude' => 'nullable|string|max:24',
            'type' => 'required|in:Supply Point,Drop Point',
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
        static::created(function ($model) {
            $parent = $model->sh_number;

            // * if have duplicate and same type, then delete old data
            if (count($parent->sh_number_details) > 2) {
                foreach ($parent->sh_number_details as $key => $value) {
                    if ($value->type == $model->type && $value->id != $model->id) {
                        $value->delete();
                    }
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

    public function sh_number()
    {
        return $this->belongsTo(ShNumber::class)->withTrashed();
    }
}
