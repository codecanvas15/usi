<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelAuthorizationDivision extends Model
{
    use HasFactory;

    protected $fillable = ['model_authorization_id', 'division_id'];

    public function modelAuthorization()
    {
        return $this->belongsTo(ModelAuthorization::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}
