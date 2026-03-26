<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeWorkExperience extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
        'from',
        'to',
        'name',
        'phone',
        'employee_count',
        'type',
        'position',
        'beginning_position',
        'end_position',
        'supervisor',
        'reason_for_leaving',
    ];

    /**
     * Get the employee that owns the EmployeeWorkExperience
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
