<?php

namespace App\View\Components\Composers;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmployeeComposer
{
    public function compose(View $view)
    {

        $user = Auth::user();
        $employee = Employee::where('id', $user->employee_id)->first();

        $view->with('employee', $employee);
    }
}
