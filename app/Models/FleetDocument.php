<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FleetDocument extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fleet_id',
        'name',
        'transaction_date',
        'effective_date',
        'end_date',
        'due_date',
        'audit_result',
        'description',
        'file',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($model) {
            Storage::disk('public')->delete($model->file ?? '');
        });
    }

    /**
     * Get the fleet that owns the FleetDocument
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fleet(): BelongsTo
    {
        return $this->belongsTo(Fleet::class);
    }
}
