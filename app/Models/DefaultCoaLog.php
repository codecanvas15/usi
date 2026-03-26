<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefaultCoaLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'default_coa_id',
        'user_id',
        'from',
        'to',
    ];

    /**
     * Get the default coa that owns the DefaultCoaLog
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function default_coa(): BelongsTo
    {
        return $this->belongsTo(DefaultCoa::class);
    }

    /**
     * Get the from_coa that owns the DefaultCoaLog
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function from_coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'from')->withTrashed();
    }

    /**
     * Get the to_coa that owns the DefaultCoaLog
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function to_coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'to')->withTrashed();
    }

    /**
     * Get the user that owns the DefaultCoaLog
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
