<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salary extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'payroll_period_id', 'work_days', 'work_days_total', 'absences_days', 'base_salary', 'brutto_salary', 'netto_salary'
    ];

    public function user()
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function feeSalaries()
    {
        return $this->hasMany(FeeSalary::class);
    }

    public function allowanceSalaries()
    {
        return $this->hasMany(AllowanceSalary::class);
    }

    public function deductionSalaries()
    {
        return $this->hasMany(DeductionSalary::class);
    }

    public function getCheckAvailableDateAttribute(): bool
    {
        return checkAvailableDate($this->created_at);
    }
}
