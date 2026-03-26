<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterGpEvaluation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'description',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "master_gp_evaluations";

    public static function rules($method = 'create', $id = null)
    {
        $validate = [
            'type' => 'required|max:64',
            'description' => 'required|max:255',
        ];

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
