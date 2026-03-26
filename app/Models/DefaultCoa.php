<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DefaultCoa extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'coa_id',
        'name',
        'type',
    ];

    /**
     * Get the coa that owns the DefaultCoa
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class)->withTrashed();
    }

    /**
     * Get all of the detafault_coa_logs for the DefaultCoa
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function default_coa_logs(): HasMany
    {
        return $this->hasMany(DefaultCoaLog::class);
    }
}
