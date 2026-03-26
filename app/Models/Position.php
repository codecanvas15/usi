<?php

namespace App\Models;

use App\Http\Helpers\ActivityLogHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ActivityLogHelper;

    protected $fillable = [
        'nama',
        'code'
    ];

    public static function rules($method = 'create', $id = null)
    {
        if ($method == 'create') {
            $validate = [
                'nama' => 'required|max:50|string|unique:positions,nama',
                'code' => 'required|max:50|string',
            ];
        } else {
            $validate = [
                'nama' => 'required|max:50|string|unique:positions,nama,' . $id,
                'code' => 'required|max:50|string',
            ];
        }

        // * if has unique validation or diff rules when create or update
        if ($method == 'create') {
            $validate = array_merge($validate, []);
        } else {
            $validate = array_merge($validate, []);
        }

        return $validate;
    }

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

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
