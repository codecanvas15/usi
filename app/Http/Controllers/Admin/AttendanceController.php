<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->permission_name", ['only' => ['index', 'show']]);
        $this->middleware("permission:create $this->permission_name", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit $this->permission_name", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete $this->permission_name", ['only' => ['destroy']]);
    }

    /**
     * Permission Name
     *
     * @var string
     */
    protected string $permission_name = 'presensi';

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'attendance';

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
        return view("admin.$this->view_folder.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $model = [];
        $employees = Employee::where('employee_status', 'active')
            ->orderBy('name', 'ASC')
            ->get();

        return view("admin.$this->view_folder.create", compact('model', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'branch_id' => 'nullable|exists:branches,id',
            'date' => 'nullable',
        ]);

        DB::beginTransaction();

        $data = collect(json_decode($request->data));
        $data = $data->filter(fn ($value) => $value != null && ($value->in_time || $value->out_time || $value->attendance_hours || $value->overtime || $value->description));
        $data = $data->map(function ($value) use ($request) {
            $value->branch_id = $request->branch_id;
            $value->date = Carbon::parse($request->date)->format('Y-m-d');
            $value->employee_id = $value->id;

            return $value;
        });

        try {
            foreach ($data as $key => $value) {
                $model = new \App\Models\Attendance();
                $model->fill([
                    'employee_id' => $value->employee_id,
                    'branch_id' => $value->branch_id,
                    'date' => Carbon::parse($value->date),
                    'in_time' => $value->in_time ?? null,
                    'out_time' => $value->out_time ?? null,
                    'go_home_early' => $value->go_home_early ?? null,
                    'late' => $value->late ?? null,
                    'overtime' => $value->overtime ?? null,
                    'work_hours' => $value->work_hours ?? null,
                    'attendance_hours' => $value->attendance_hours ?? null,
                    'description' => $value->description ?? null,
                ]);

                $model->save();
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, "create"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = \App\Models\Attendance::findOrFail($id);

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
        $model = \App\Models\Attendance::findOrFail($id);

        if (!$model->check_available_date) {
            return abort(403);
        }

        return view("admin.$this->view_folder.edit", compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'employee_id' => 'required|exists:employees,id',
            'branch_id' => 'nullable|exists:branches,id',
            'date' => 'nullable',
            'in_time' => 'nullable',
            'out_time' => 'nullable',
            'go_home_early' => 'nullable',
            'late' => 'nullable',
            'overtime' => 'nullable',
            'work_hours' => 'nullable',
            'attendance_hours' => 'nullable',
            'description' => 'nullable',
        ]);

        DB::beginTransaction();

        $model = \App\Models\Attendance::findOrFail($id);
        $model->fill([
            'employee_id' => $request->employee_id,
            'branch_id' => $request->branch_id,
            'date' => Carbon::parse($request->date),
            'in_time' => $request->in_time,
            'out_time' => $request->out_time,
            'go_home_early' => $request->go_home_early,
            'late' => $request->late,
            'overtime' => $request->overtime,
            'work_hours' => $request->work_hours,
            'attendance_hours' => $request->attendance_hours,
            'description' => $request->description,
        ]);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal sudah closing'));
        }

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "edit", null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, "edit"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = \App\Models\Attendance::findOrFail($id);

        DB::beginTransaction();

        try {
            $model->delete();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "delete", null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, "delete"));
    }

    /**
     * getImportFormat
     *
     * @return \Illuminate\Http\Response
     */
    public function getImportFormat(Request $request)
    {
        $file_name = "import-attendance-format.xlsx";

        return Excel::download(new \App\Exports\AttendanceImportFormat($request), $file_name);
    }

    /**
     * importAttendance
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function importAttendance(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $file = $this->upload_file($request->file('file'), 'attendance-import');

        DB::beginTransaction();
        try {
            Excel::import(new \App\Imports\Admin\AttendanceImport(), "storage/" . $file);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->delete_file($file ?? '');

            throw $th;

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "import", null, $th->getMessage()));
        }

        $this->delete_file($file ?? '');
        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, "import", "import success"));
    }

    /**
     * exportAttendance
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportAttendance(Request $request)
    {
        $this->validate($request, [
            'employee_id' => 'nullable|exists:employees,id',
            'from_date' => 'nullable',
            'to_date' => 'nullable',
        ]);

        $file_name = "attendance-";

        if ($request->employee_id) {
            $file_name .= \App\Models\Employee::findOrFail($request->employee_id)->NIK . "-";
        }

        if ($request->from_date) {
            $file_name .= Carbon::parse($request->from_date) . "-";
        }

        if ($request->to_date) {
            $file_name .= Carbon::parse($request->to_date) . "-";
        }

        $file_name .= ".xlsx";

        return Excel::download(new \App\Exports\Admin\AttendanceExport(employee: $request->employee_id, from: $request->from_date, to: $request->to_date), $file_name);
    }

    public function employee(Request $request)
    {
        $data = DB::table('employees')
            ->when($request->branch_id, function ($row) {
                $row->where('employees.branch_id', request()->branch_id);
            })
            ->whereNull('employees.deleted_at')
            ->leftJoin('positions', 'positions.id', '=', 'employees.position_id')
            ->leftJoin('attendances', function ($query) use ($request) {
                $query->on('attendances.employee_id', '=', 'employees.id')
                    ->when($request->from_date, function ($query) use ($request) {
                        $query->whereDate('date', '>=', Carbon::parse($request->from_date));
                    })
                    ->when($request->to_date, function ($query) use ($request) {
                        $query->whereDate('date', '<=', Carbon::parse($request->to_date));
                    });
            })
            ->groupBy('employees.id')
            ->select([
                'employees.id',
                'employees.NIK',
                'employees.name',
                'positions.nama as position',
                DB::raw('COUNT(attendances.id) as total_attendance'),
            ]);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('NIK', function ($row) {
                $route = route('admin.employee.show', ['employee' => $row->id]);

                return "<a href='$route' target='_blank'>$row->NIK</a>";
            })
            ->editColumn('total_attendance', function ($row) use ($request) {
                if ($row->total_attendance == 0) {
                    return "<span class='badge badge-danger'>Belum ada presensi</span>";
                }

                $route = route('admin.attendance.show-by-employee') . "?from_date={$request->from_date}&to_date={$request->to_date}&branch_id={$request->branch_id}&employee_id={$row->id}";
                return "<a href='$route' target='_blank'>Lihat Presensi</a>";
            })
            ->rawColumns(['NIK', 'total_attendance'])
            ->make(true);
    }

    public function showByEmployee(Request $request)
    {
        if ($request->ajax()) {
            $data = \App\Models\Attendance::with('employee')
                ->when($request->employee_id, function ($query) use ($request) {
                    $query->where('employee_id', $request->employee_id);
                })
                ->when($request->from_date, function ($query) use ($request) {
                    $query->whereDate('date', '>=', Carbon::parse($request->from_date));
                })
                ->when($request->to_date, function ($query) use ($request) {
                    $query->whereDate('date', '<=', Carbon::parse($request->to_date));
                })
                ->when(!get_current_branch()->is_primary, function ($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                })
                ->select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('employee_name', fn ($row) => view('components.datatable.detail-link', [
                    'field' => "{$row->employee->name} - {$row->employee->NIK}}",
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('date', fn ($row) => localDate($row->date))
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'btn_config' => [
                            'detail' => [
                                'display' => false,
                            ],
                            'edit' => [
                                'display' => true,
                            ],
                            'delete' => [
                                'display' => true,
                            ],
                        ],
                    ]);
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $data['employee'] = \App\Models\Employee::findOrFail($request->employee_id);
        $data['from_date'] = Carbon::parse($request->from_date) ?? Carbon::now()->firstOfMonth()->format('Y-m-d');
        $data['to_date'] = Carbon::parse($request->to_date) ?? Carbon::now()->lastOfMonth()->format('Y-m-d');

        return view("admin.$this->view_folder.show-by-employee", $data);
    }

    public function bulkDelete(Request $request)
    {
        DB::beginTransaction();
        try {
            Attendance::whereDate('date', '>=', Carbon::parse($request->from_date))
                ->whereDate('date', '<=', Carbon::parse($request->to_date))
                ->when($request->employee_id, function ($query) use ($request) {
                    $query->where('employee_id', $request->employee_id);
                })
                ->delete();
            DB::commit();

            return redirect()->back()->with($this->ResponseMessageCRUD(true, "delete", "Berhasil menghapus data"));
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "delete", null, $th->getMessage()));
        }
    }

    public function data_employee(Request $request)
    {
        $employees = Employee::where('employee_status', 'active')
            ->when($request->branch_id, function ($query) use ($request) {
                $query->where('branch_id', $request->branch_id);
            })
            ->orderBy('name', 'asc');

        $data_attendance = collect(json_decode($request->data_attendance));

        return DataTables::of($employees)
            ->addIndexColumn()
            ->addColumn('in_time', function ($row) use ($data_attendance) {
                $value = $data_attendance->where('id', $row->id)->first()->in_time ?? '';
                return '<input class="form-control" type="time" id="in_time_' . $row->id . '" onblur="insert_presence(`in_time`, ' . $row->id . ')" value="' . $value . '">';
            })
            ->addColumn('in_time_val', function ($row) use ($data_attendance) {
                return $data_attendance->where('id', $row->id)->first()->in_time ?? '';
            })
            ->addColumn('out_time', function ($row) use ($data_attendance) {
                $value = $data_attendance->where('id', $row->id)->first()->out_time ?? '';
                return '<input class="form-control" type="time" id="out_time_' . $row->id . '" onblur="insert_presence(`out_time`, ' . $row->id . ')" value="' . $value . '">';
            })
            ->addColumn('out_time_val', function ($row) use ($data_attendance) {
                return $data_attendance->where('id', $row->id)->first()->out_time ?? '';
            })
            ->addColumn('attendance_hours', function ($row) use ($data_attendance) {
                $value = $data_attendance->where('id', $row->id)->first()->attendance_hours ?? '';
                return '<input class="form-control" type="time" id="attendance_hours_' . $row->id . '" onblur="insert_presence(`attendance_hours`, ' . $row->id . ')" value="' . $value . '">';
            })
            ->addColumn('attendance_hours_val', function ($row) use ($data_attendance) {
                return $data_attendance->where('id', $row->id)->first()->attendance_hours ?? '';
            })
            ->addColumn('overtime', function ($row) use ($data_attendance) {
                $value = $data_attendance->where('id', $row->id)->first()->overtime ?? '';
                return '<input class="form-control" type="time" id="overtime_' . $row->id . '" onblur="insert_presence(`overtime`, ' . $row->id . ')" value="' . $value . '">';
            })
            ->addColumn('overtime_val', function ($row) use ($data_attendance) {
                return $data_attendance->where('id', $row->id)->first()->overtime ?? '';
            })
            ->addColumn('description', function ($row) use ($data_attendance) {
                $value = $data_attendance->where('id', $row->id)->first()->description ?? '';
                return '<textarea id="description_' . $row->id . '" rows="3" class="form-control" onblur="insert_presence(`description`, ' . $row->id . ')">' . $value . '</textarea>';
            })
            ->addColumn('description_val', function ($row) use ($data_attendance) {
                return $data_attendance->where('id', $row->id)->first()->description ?? '';
            })
            ->rawColumns(['in_time', 'out_time', 'attendance_hours', 'overtime', 'description'])
            ->make(true);
    }
}
