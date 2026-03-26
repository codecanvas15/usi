<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelTable extends Model
{
    use HasFactory;

    protected $table = 'models';

    protected $fillable = [
        'name',
        'alias',
        'type',
        'group',
    ];

    public function model_authorizations()
    {
        return $this->hasMany(ModelAuthorization::class, 'model_id');
    }
}
