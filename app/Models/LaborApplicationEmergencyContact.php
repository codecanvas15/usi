<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaborApplicationEmergencyContact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'labor_application_id',
        'name',
        'relationship',
        'phone',
        'address',
    ];

    /**
     * Get the laborApplication that owns the LaborApplicationEmergencyContact
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function laborApplication(): BelongsTo
    {
        return $this->belongsTo(LaborApplication::class)->withTrashed();
    }
}
