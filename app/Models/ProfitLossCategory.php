<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfitLossCategory extends Model
{
    use HasFactory;

    public function profit_loss_subcategories()
    {
        return $this->hasMany(ProfitLossSubcategory::class);
    }
}
