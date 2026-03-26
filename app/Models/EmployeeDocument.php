<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'document_name',
        'card_number',
        'validity_period',
        'document_file',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
