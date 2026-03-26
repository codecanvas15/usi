<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeFamilyTree extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
        'type',
        'relation',
        'name',
        'gender',
        'birth_place',
        'birth_date',
        'education',
        'last_position',
        'last_company',
    ];

    /**
     * Get the employee that owns the EmployeeFamilyTree
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
