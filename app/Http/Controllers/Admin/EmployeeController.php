<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EmployeeExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreStep3Request;
use App\Http\Requests\Employee\StoreStep4Request;
use App\Http\Requests\Employee\StoreStep5Request;
use App\Http\Requests\Employee\StoreStep6Request;
use App\Http\Requests\Employee\StoreStep7Request;
use App\Http\Requests\Employee\StoreUpdateRequest;
use App\Http\Requests\Employee\StoreUpdateStep2Request;
use App\Http\Requests\Employee\Update\UpdateRequest;
use App\Http\Requests\Employee\Update\UpdateStep2Request;
use App\Http\Requests\Employee\Update\UpdateStep3Request;
use App\Http\Requests\Employee\Update\UpdateStep4Request;
use App\Http\Requests\Employee\Update\UpdateStep5Request;
use App\Http\Requests\Employee\Update\UpdateStep6Request;
use App\Http\Requests\Employee\Update\UpdateStep7Request;
use App\Imports\Admin\Employee as AdminEmployee;
use App\Models\Employee as model;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EmployeeController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder", ['only' => ['index', 'show']]);
        $this->middleware("permission:create $this->view_folder", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit $this->view_folder", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete $this->view_folder", ['only' => ['destroy']]);
        $this->middleware("permission:import $this->view_folder", ['only' => ['import']]);
        $this->middleware("permission:export $this->view_folder", ['only' => ['export']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'employee';

    /**
     * search  value
     *
     * @var string|null
     */
    protected string|null $search = null;

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return (new \App\Services\EmployeeService())->datatables($request);
        }

        return view('admin.' . $this->view_folder . '.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $model = [];

        return view("admin.$this->view_folder.create", compact('model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateRequest $request)
    {
        DB::beginTransaction();

        try {
            $employee = (new \App\Services\EmployeeService())->store($request);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->withInput()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.employee.create.step2", ['employee_id' => $employee->id])->with($this->ResponseMessageCRUD());
    }

    /**
     * render create page step 2
     *
     */
    public function createStep2($employee_id)
    {
        $model = model::findOrFail($employee_id);

        return view("admin.$this->view_folder.partials.create.step-2", compact('model'));
    }

    /**
     * store step 2
     */
    public function storeStep2(StoreUpdateStep2Request $request, $employee_id)
    {
        $employee = model::findOrFail($employee_id);

        DB::beginTransaction();

        try {
            (new \App\Services\EmployeeService())->store2($request, $employee);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.employee.create.step3", ['employee_id' => $employee->id])->with($this->ResponseMessageCRUD());
    }

    /**
     * render create page step 3
     */
    public function createStep3($employee_id)
    {
        $model = model::findOrFail($employee_id);

        return view("admin.$this->view_folder.partials.create.step-3", compact('model'));
    }

    /**
     * store step 3
     */
    public function storeStep3(StoreStep3Request $request, $employee_id)
    {
        $employee = model::findOrFail($employee_id);

        DB::beginTransaction();

        try {
            (new \App\Services\EmployeeService())->store3($request, $employee);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.employee.create.step4", ['employee_id' => $employee->id])->with($this->ResponseMessageCRUD());
    }

    /**
     * render create page step 4
     */
    public function createStep4($employee_id)
    {
        $model = model::findOrFail($employee_id);

        return view("admin.$this->view_folder.partials.create.step-4", compact('model'));
    }

    /**
     * store step 4
     */
    public function storeStep4(StoreStep4Request $request, $employee_id)
    {
        $employee = model::findOrFail($employee_id);

        DB::beginTransaction();

        try {
            (new \App\Services\EmployeeService())->store4($request, $employee);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.employee.create.step5", ['employee_id' => $employee->id])->with($this->ResponseMessageCRUD());
    }

    /**
     * render create page step 5
     */
    public function createStep5($employee_id)
    {
        $model = model::findOrFail($employee_id);

        return view("admin.$this->view_folder.partials.create.step-5", compact('model'));
    }

    /**
     * store step 5
     */
    public function storeStep5(StoreStep5Request $request, $employee_id)
    {
        $employee = model::findOrFail($employee_id);

        DB::beginTransaction();

        try {
            (new \App\Services\EmployeeService())->store5($request, $employee);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.employee.create.step6", ['employee_id' => $employee->id])->with($this->ResponseMessageCRUD());
    }

    /**
     * render create page step 6
     */
    public function createStep6($employee_id)
    {
        $model = model::findOrFail($employee_id);

        return view("admin.$this->view_folder.partials.create.step-6", compact('model'));
    }

    /**
     * store step 6
     */
    public function storeStep6(StoreStep6Request $request, $employee_id)
    {
        $employee = model::findOrFail($employee_id);

        DB::beginTransaction();

        try {
            (new \App\Services\EmployeeService())->store6($request, $employee);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.employee.create.step7", ['employee_id' => $employee->id])->with($this->ResponseMessageCRUD());
    }

    /**
     * render create page step 7
     */
    public function createStep7($employee_id)
    {
        $model = model::findOrFail($employee_id);

        return view("admin.$this->view_folder.partials.create.step-7", compact('model'));
    }

    /**
     * store step 7
     */
    public function storeStep7(StoreStep7Request $request, $employee_id)
    {
        $employee = model::findOrFail($employee_id);

        DB::beginTransaction();

        try {
            (new \App\Services\EmployeeService())->store7($request, $employee);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.show", $employee)->with($this->ResponseMessageCRUD());
    }


    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $int
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $model = (new \App\Services\EmployeeService())->show($id);

        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }
        return view("admin.$this->view_folder.show", compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = model::findOrFail($id);


        return view("admin.$this->view_folder.edit", compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        if ($request->hasFile('file')) {
            $this->validate($request, [
                'file' => 'file|image|mimes:png,jpg,jpeg|max:10340',
            ]);
        }
        $model = model::findOrFail($id);

        DB::beginTransaction();

        // * saving and make response
        try {
            (new \App\Services\EmployeeService())->update($request, $model);
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.employee.show", $model)->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    /**
     * edit step 2
     */
    public function editStep2($employee_id)
    {
        $model = model::findOrFail($employee_id);

        return view("admin.$this->view_folder.partials.edit.step-2", compact('model'));
    }

    /**
     * update step 2
     */
    public function updateStep2(UpdateStep2Request $request, $employee_id)
    {
        $employee = model::findOrFail($employee_id);

        DB::beginTransaction();

        try {
            (new \App\Services\EmployeeService())->update2($request, $employee);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.employee.show", $employee)->with($this->ResponseMessageCRUD());
    }

    /**
     * edit step 3
     */
    public function editStep3($employee_id)
    {
        $model = model::findOrFail($employee_id);

        return view("admin.$this->view_folder.partials.edit.step-3", compact('model'));
    }

    /**
     * update step 3
     */
    public function updateStep3(UpdateStep3Request $request, $employee_id)
    {
        $employee = model::findOrFail($employee_id);

        DB::beginTransaction();

        try {
            (new \App\Services\EmployeeService())->update3($request, $employee);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.employee.show", $employee)->with($this->ResponseMessageCRUD());
    }

    /**
     * edit step 4
     */
    public function editStep4($employee_id)
    {
        $model = model::findOrFail($employee_id);

        return view("admin.$this->view_folder.partials.edit.step-4", compact('model'));
    }

    /**
     * update step 4
     */
    public function updateStep4(UpdateStep4Request $request, $employee_id)
    {
        $employee = model::findOrFail($employee_id);

        DB::beginTransaction();

        try {
            (new \App\Services\EmployeeService())->update4($request, $employee);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.employee.show", $employee)->with($this->ResponseMessageCRUD());
    }

    /**
     * edit step 5
     */
    public function editStep5($employee_id)
    {
        $model = model::findOrFail($employee_id);

        return view("admin.$this->view_folder.partials.edit.step-5", compact('model'));
    }

    /**
     * Update step 5
     */
    public function updateStep5(UpdateStep5Request $request, $employee_id)
    {
        $employee = model::findOrFail($employee_id);

        DB::beginTransaction();

        try {
            (new \App\Services\EmployeeService())->update5($request, $employee);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.employee.show", $employee)->with($this->ResponseMessageCRUD());
    }

    /**
     * edit step 6
     */
    public function editStep6($employee_id)
    {
        $model = model::findOrFail($employee_id);

        return view("admin.$this->view_folder.partials.edit.step-6", compact('model'));
    }

    /**
     * Update step 6
     */
    public function updateStep6(UpdateStep6Request $request, $employee_id)
    {
        $employee = model::findOrFail($employee_id);

        DB::beginTransaction();

        try {
            (new \App\Services\EmployeeService())->update6($request, $employee);
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.employee.show", $employee)->with($this->ResponseMessageCRUD());
    }

    /**
     * edit step 7
     */
    public function editStep7($employee_id)
    {
        $model = model::findOrFail($employee_id);

        return view("admin.$this->view_folder.partials.edit.step-7", compact('model'));
    }

    /**
     * Update step 7
     */
    public function updateStep7(UpdateStep7Request $request, $employee_id)
    {
        $employee = model::findOrFail($employee_id);

        DB::beginTransaction();

        try {
            (new \App\Services\EmployeeService())->update7($request, $employee);
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.employee.show", $employee)->with($this->ResponseMessageCRUD());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $model = model::findOrFail($id);

        DB::beginTransaction();

        try {
            $model->delete();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
        }
        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    public function findById(Request $request)
    {
        $model = model::with(['position', 'division'])->findOrFail($request->id);
        return response()->json($model);
    }

    public function select(Request $request)
    {
        $model = model::query();
        if ($request->search) {
            $model->where(function ($m) use ($request) {
                $m->where('name', 'like', "%$request->search%")
                    ->orWhere('email', 'like', "%$request->search%")
                    ->orWhere('employees.NIK', 'like', "%$request->search%");
            });
        }

        if ($request->division_id) {
            $model->where('division_id', $request->division_id);
        }

        if ($request->employment_status_id) {
            $model->where('employment_status_id', $request->employment_status_id);
        }

        $data = $model->orderByDesc('created_at')->limit(10)->get();

        return $this->ResponseJsonData($data);
    }

    public function selectWithUser(Request $request)
    {
        if ($request->search) {
            $model = model::orWhere('name', 'like', "%$request->search%")
                ->orWhere('email', 'like', "%$request->search%")
                ->orWhere('NIK', 'like', "%$request->search%")
                ->orderByDesc('created_at')->limit(10)
                ->get();
        } else {
            $model = model::orderByDesc('created_at')->limit(10)->get();
        }

        return $this->ResponseJsonData($model);
    }

    public function selectWithID(Request $request)
    {
        $model = model::where('employees.id', $request->id)->orderByDesc('employees.created_at')->limit(10)->get();


        return $this->ResponseJsonData($model);
    }

    public function export()
    {
        $employees = model::all();

        $data = [
            'title' => 'Data Karyawan',
            'date' => \Carbon\Carbon::now()->format('d/m/Y'),
            'employees' => $employees,
        ];

        return Excel::download(new EmployeeExport('admin.employee.excel.export', $data), 'employees.xlsx');
    }

    public function export_pdf($id, Request $request)
    {
        $model = model::findOrFail(decryptId($id));

        $qr_url = route('employee.export.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));

        $pdf = Pdf::loadView('admin.employee.pdf.export', compact('model', 'qr'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');

        return $pdf->stream("EMPLOYEE $model->NIK export.pdf");
    }

    public function import_format()
    {
        return $this->ResponseDownload(public_path('import/admin/employee.xlsx'));
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file',
        ]);

        DB::beginTransaction();
        // * import
        try {
            Excel::import(new AdminEmployee(), $request->file('file'));

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
            return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(false, 'import', 'failed import data', $th->getMessage()));
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'import', 'success import data'));
    }

    /**
     * Get employee data detail api
     *
     * @param string|int|null $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id = null)
    {
        $model = model::with([
            'employee_banks',
            'position',
            'education',
            'degree',
            'branch',
            'division',
            'employment_status',
        ])->findOrFail($id);

        return $this->ResponseJsonData($model);
    }

    public function generate_employee_code()
    {
        DB::beginTransaction();
        try {
            $employees = Employee::orderBy('id', 'asc')->get();
            foreach ($employees as $key => $employee) {
                if ($employee->position->code) {
                    $employee_by_roles = Employee::whereHas('position', function ($q) use ($employee) {
                        $q->where('code', $employee->position->code);
                    })
                        ->orderBy('id')
                        ->get();

                    $employee_index = collect($employee_by_roles)->search(function ($value, $key) use ($employee) {
                        return $value->id == $employee->id;
                    });

                    $code = "{$employee->position->code}-" . Carbon::parse($employee->join_date ?? $employee->start_contract)->format('my') . sprintf("%04s", abs($employee_index) + 1);

                    DB::table('employees')->where('id', $employee->id)->update(['NIK' => $code]);
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json($th->getMessage());
        }
    }
}
