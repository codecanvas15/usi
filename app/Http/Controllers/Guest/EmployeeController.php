<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\Update\UpdateRequest;
use App\Http\Requests\Employee\Update\UpdateStep2Request;
use App\Http\Requests\Employee\Update\UpdateStep3Request;
use App\Http\Requests\Employee\Update\UpdateStep4Request;
use App\Http\Requests\Employee\Update\UpdateStep5Request;
use App\Http\Requests\Employee\Update\UpdateStep6Request;
use App\Http\Requests\Employee\Update\UpdateStep7Request;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("guest.employee.index");
    }

    /**
     * Find the given employee by nik request
     */
    public function find(Request $request)
    {
        $this->validate($request, [
            'nik' => 'required|exists:employees,NIK',
        ]);

        $NIK = $request->nik;
        $employee = Employee::where('NIK', $NIK)->firstOrFail();

        return redirect()->route("guest.employee.edit.step1", [
            'employee_id' => $employee->id,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function editStep1($employee_id)
    {
        $model = Employee::findOrFail($employee_id);

        return view("guest.employee.edit", compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStep1(UpdateRequest $request, $id)
    {
        if ($request->hasFile('file')) {
            $this->validate($request, [
                'file' => 'file|image|mimes:png,jpg,jpeg|max:10340',
            ]);
        }
        $model = Employee::findOrFail($id);

        DB::beginTransaction();

        // * saving and make response
        try {
            (new \App\Services\EmployeeService())->update($request, $model);
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("guest.employee.edit.step2", [
            'employee_id' => $model->id,
        ])->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    /**
     * edit step 2
     */
    public function editStep2($employee_id)
    {
        $model = Employee::findOrFail($employee_id);

        return view("guest.employee.partials.edit.step-2", compact('model'));
    }

    /**
     * update step 2
     */
    public function updateStep2(UpdateStep2Request $request, $employee_id)
    {
        $employee = Employee::findOrFail($employee_id);

        DB::beginTransaction();

        try {
            (new \App\Services\EmployeeService())->update2($request, $employee);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("guest.employee.edit.step3", [
            'employee_id' => $employee->id,
        ])->with($this->ResponseMessageCRUD());
    }

    /**
     * edit step 3
     */
    public function editStep3($employee_id)
    {
        $model = Employee::findOrFail($employee_id);

        return view("guest.employee.partials.edit.step-3", compact('model'));
    }

    /**
     * update step 3
     */
    public function updateStep3(UpdateStep3Request $request, $employee_id)
    {
        $employee = Employee::findOrFail($employee_id);

        DB::beginTransaction();

        try {
            (new \App\Services\EmployeeService())->update3($request, $employee);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("guest.employee.edit.step4", [
            'employee_id' => $employee->id,
        ])->with($this->ResponseMessageCRUD());
    }

    /**
     * edit step 4
     */
    public function editStep4($employee_id)
    {
        $model = Employee::findOrFail($employee_id);

        return view("guest.employee.partials.edit.step-4", compact('model'));
    }

    /**
     * update step 4
     */
    public function updateStep4(UpdateStep4Request $request, $employee_id)
    {
        $employee = Employee::findOrFail($employee_id);

        DB::beginTransaction();

        try {
            (new \App\Services\EmployeeService())->update4($request, $employee);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("guest.employee.edit.step5", [
            'employee_id' => $employee->id,
        ])->with($this->ResponseMessageCRUD());
    }

    /**
     * edit step 5
     */
    public function editStep5($employee_id)
    {
        $model = Employee::findOrFail($employee_id);

        return view("guest.employee.partials.edit.step-5", compact('model'));
    }

    /**
     * Update step 5
     */
    public function updateStep5(UpdateStep5Request $request, $employee_id)
    {
        $employee = Employee::findOrFail($employee_id);

        DB::beginTransaction();

        try {
            (new \App\Services\EmployeeService())->update5($request, $employee);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("guest.employee.edit.step6", [
            'employee_id' => $employee->id,
        ])->with($this->ResponseMessageCRUD());
    }

    /**
     * edit step 6
     */
    public function editStep6($employee_id)
    {
        $model = Employee::findOrFail($employee_id);

        return view("guest.employee.partials.edit.step-6", compact('model'));
    }

    /**
     * Update step 6
     */
    public function updateStep6(UpdateStep6Request $request, $employee_id)
    {
        $employee = Employee::findOrFail($employee_id);

        DB::beginTransaction();

        try {
            (new \App\Services\EmployeeService())->update6($request, $employee);
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("guest.employee.edit.step7", [
            'employee_id' => $employee->id,
        ])->with($this->ResponseMessageCRUD());
    }

    /**
     * edit step 7
     */
    public function editStep7($employee_id)
    {
        $model = Employee::findOrFail($employee_id);

        return view("guest.employee.partials.edit.step-7", compact('model'));
    }

    /**
     * Update step 7
     */
    public function updateStep7(UpdateStep7Request $request, $employee_id)
    {
        $employee = Employee::findOrFail($employee_id);

        DB::beginTransaction();

        try {
            (new \App\Services\EmployeeService())->update7($request, $employee);
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("guest.employee.index", [
            'id' => $employee->id,
        ])->with($this->ResponseMessageCRUD());
    }
}
