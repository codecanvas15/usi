<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPrintAuthorization extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'type',
        'group',
        'can_print',
    ];
}
