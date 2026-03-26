<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfitLossSubcategory extends Model
{
    use HasFactory;

    public function profit_loss_details()
    {
        return $this->hasMany(ProfitLossDetail::class)->orderBy('position');
    }
}
