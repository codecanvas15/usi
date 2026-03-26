<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recruitment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
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
            'resume' => 'required|string',
            'foto' => 'required|string',
            'nama' => 'required|string',
            'email' => 'required|string',
            'no_telp' => 'required|string',
            'status' => 'required|string',
            'nama_company' => 'required|string',
            'pengalaman_kerja ' => 'required|integer',
            'deskripsi_job ' => 'required|string',
            'cover_letter ' => 'required|string',
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

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }

    public function url_recruitment()
    {
        return $this->hasOne(UrlRecruitment::class, 'id', 'url_recruitment_id')->with('user');
    }

    public function edu_recruitment()
    {
        return $this->hasOne(EduRecruitment::class, 'id', 'edu_recruitment_id')->with('user');
    }
}
