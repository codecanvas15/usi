<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveChangeFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'leave_id',
        'file_path',
        'file_name',
        'status',
    ];

    public function leave()
    {
        return $this->belongsTo(Leave::class);
    }

    public function setFill ($request)
    {
        foreach ($this->fillable as $key) {
            if ($request->has($key)) {
                $this->{$key} = $request->{$key};
            }
        }
    }

    public static function rules($method) 
    {
        $rules = [
            'leave_id' => 'required|exists:leaves,id',
            'file_path' => 'required',
            'file_name' => 'nullable',
        ];

        return $rules;
    }
}
