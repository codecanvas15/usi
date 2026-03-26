<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelAuthorizationBranch extends Model
{
    use HasFactory;

    protected $fillable = ['model_authorization_id', 'branch_id'];

    public function modelAuthorization()
    {
        return $this->belongsTo(ModelAuthorization::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
