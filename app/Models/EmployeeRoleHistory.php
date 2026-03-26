<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRoleHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'causer_id',
        'employee_id',
        'from_role_id',
        'to_role_id',
    ];

    public function causer()
    {
        return $this->belongsTo(Employee::class, 'causer_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function from()
    {
        return $this->belongsTo(Role::class, 'from_role_id');
    }

    public function to()
    {
        return $this->belongsTo(Role::class, 'to_role_id');
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['created_at'] = toDayDateTimeString($this->created_at);
        $array['updated_at'] = toDayDateTimeString($this->updated_at);

        return $array;
    }
}
