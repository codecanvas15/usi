<?php

namespace App\Http\Controllers\Admin;

use App\Models\Leave;
use App\Models\Leave as model;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MassLeave;
use App\Models\MassLeaveDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class MassLeaveController extends Controller
{
    use \App\Http\Helpers\ActivityStatusLogHelper;

    protected string $view_folder = 'mass-leave';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder", ['only' => ['index']]);
        $this->middleware("permission:create $this->view_folder", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit $this->view_folder", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete $this->view_folder", ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['month'] = Carbon::now()->month;
        $data['year'] = Carbon::now()->year;
        return view("admin.$this->view_folder.index", $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(model::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

        $model = [];
        $employee_ids = Employee::where('employee_status', 'active')
            ->whereDate('join_date', '<=', Carbon::now()->subYear())
            ->pluck('id');

        return view("admin.$this->view_folder.create", compact('model', 'employee_ids'));
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
            'date' => 'required|date',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'cause' => 'required|string',
            'note' => 'required|string',
            'attachment' => 'file|max:5120'
        ]);

        $from_date = Carbon::parse($request->from_date)->startOfDay();
        $to_date = Carbon::parse($request->to_date)->endOfDay();

        if (Carbon::parse($from_date)->format('m-Y') != Carbon::parse($to_date)->format('m-Y')) {
            return redirect()->back()->withInput()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal awal dan akhir harus dalam bulan yang sama'));
        }

        DB::beginTransaction();


        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $this->upload_file($request->file('attachment'), 'leave-attachment');
        }
        $model = new MassLeave();
        $model->fill([
            'branch_id' => $request->branch_id,
            'date' => Carbon::parse($request->date),
            'from_date' => Carbon::parse($request->from_date),
            'to_date' => Carbon::parse($request->to_date),
            'necessary' => $request->cause,
            'note' => $request->note,
            'attachment' => $attachment,
        ]);

        try {
            $model->save();

            $employee_ids = explode(',', $request->employee_ids);
            $employees = Employee::whereIn('id', $employee_ids)
                ->when($request->branch_id, function ($query) use ($request) {
                    return $query->where('branch_id', $request->branch_id);
                })
                ->get();

            foreach ($employees as $key => $employee) {
                $leave = new Leave();
                $leave->fill([
                    'branch_id' => $employee->branch_id,
                    'employee_id' => $employee->id,
                    'from_date' => Carbon::parse($request->from_date),
                    'to_date' => Carbon::parse($request->to_date),
                    'note' => $request->note,
                    'day' =>  $from_date->diffInDaysFiltered(function (Carbon $date) {
                        return !$date->isWeekend();
                    }, $to_date),
                    'cause' => $request->cause,
                    'address' => '-',
                    'phone_number' => '-',
                    'division_id' => $employee->division_id,
                    'type' => 'cuti',
                    'necessary' => 'others',
                    'leave_remaining' => $request->leave_remaining,
                    'date' => Carbon::parse($request->date),
                    'attachment' => $attachment,
                    'status' => 'approve',
                ]);
                $leave->save();

                $mass_leave_detail = new MassLeaveDetail();
                $mass_leave_detail->fill([
                    'mass_leave_id' => $model->id,
                    'leave_id' => $leave->id,
                    'employee_id' => $employee->id,
                ]);
                $mass_leave_detail->save();
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'create'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = MassLeave::findOrFail($id);
        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = MassLeave::findOrFail($id);
        $employee_ids = Employee::where('employee_status', 'active')
            ->whereDate('join_date', '<=', Carbon::now()->subYear())
            ->pluck('id');

        return view("admin.$this->view_folder.edit", compact('model', 'employee_ids'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'branch_id' => 'nullable|exists:branches,id',
            'date' => 'required|date',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'cause' => 'required|string',
            'note' => 'required|string',
            'attachment' => 'file|max:5120'
        ]);

        $from_date = Carbon::parse($request->from_date)->startOfDay();
        $to_date = Carbon::parse($request->to_date)->endOfDay();

        if (Carbon::parse($from_date)->format('m-Y') != Carbon::parse($to_date)->format('m-Y')) {
            return redirect()->back()->withInput()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal awal dan akhir harus dalam bulan yang sama'));
        }

        DB::beginTransaction();


        $model = MassLeave::find($id);
        $attachment = $model->attachment;
        if ($request->hasFile('attachment')) {
            Storage::delete($model->attachment);
            $attachment = $this->upload_file($request->file('attachment'), 'leave-attachment');
        }
        $model->fill([
            'branch_id' => $request->branch_id,
            'date' => Carbon::parse($request->date),
            'from_date' => Carbon::parse($request->from_date),
            'to_date' => Carbon::parse($request->to_date),
            'necessary' => $request->cause,
            'note' => $request->note,
            'attachment' => $attachment,
        ]);

        try {
            $model->save();

            $employee_ids = explode(',', $request->employee_ids);
            $employees = Employee::whereIn('id', $employee_ids)
                ->when($request->branch_id, function ($query) use ($request) {
                    return $query->where('branch_id', $request->branch_id);
                })
                ->get();

            MassLeaveDetail::where('mass_leave_id', $model->id)
                ->whereNotIn('employee_id', $employee_ids)
                ->get()
                ->each(function ($leave) {
                    $leave->leave()->delete();
                    $leave->delete();
                });

            foreach ($employees as $key => $employee) {
                $mass_leave_detail = MassLeaveDetail::where('mass_leave_id', $model->id)
                    ->where('employee_id', $employee->id)
                    ->first();

                if ($mass_leave_detail) {
                    $leave = Leave::find($mass_leave_detail->leave_id);
                } else {
                    $leave = new Leave();
                }

                $leave->fill([
                    'branch_id' => $employee->branch_id,
                    'employee_id' => $employee->id,
                    'from_date' => Carbon::parse($request->from_date),
                    'to_date' => Carbon::parse($request->to_date),
                    'note' => $request->note,
                    'day' =>  $from_date->diffInDaysFiltered(function (Carbon $date) {
                        return !$date->isWeekend();
                    }, $to_date),
                    'cause' => $request->cause,
                    'address' => '-',
                    'phone_number' => '-',
                    'division_id' => $employee->division_id,
                    'type' => 'cuti',
                    'necessary' => 'others',
                    'leave_remaining' => $request->leave_remaining,
                    'date' => Carbon::parse($request->date),
                    'attachment' => $attachment,
                    'status' => 'approve',
                ]);
                $leave->save();

                if (!$mass_leave_detail) {
                    $mass_leave_detail = new MassLeaveDetail();
                }

                $mass_leave_detail->fill([
                    'mass_leave_id' => $model->id,
                    'leave_id' => $leave->id,
                    'employee_id' => $employee->id,
                ]);
                $mass_leave_detail->save();
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy($leave)
    {
        $model = MassLeave::findOrFail($leave);

        DB::beginTransaction();

        try {
            MassLeaveDetail::where('mass_leave_id', $model->id)
                ->get()
                ->each(function ($leave) {
                    $leave->leave()->delete();
                    $leave->delete();
                });

            Storage::delete($model->attachment);

            $model->delete();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $query = MassLeave::when(
                get_current_branch()->is_primary
                    && $request->branch_id,
                function ($query) {
                    $query->where('branch_id', get_current_branch()->id);
                }
            )
                ->when(!get_current_branch()->is_primary, function ($query) {
                    $query->where('branch_id', get_current_branch()->id);
                })
                ->when($request->from_date, function ($query) use ($request) {
                    $query->where('from_date', Carbon::parse($request->from_date));
                })
                ->when($request->to_date, function ($query) use ($request) {
                    $query->where('to_date', Carbon::parse($request->to_date));
                });


            $data = $query->get();

            return datatables($data)
                ->addIndexColumn()
                ->editColumn('necessary', fn ($row) => view('components.datatable.detail-link', [
                    'field' => $row->necessary,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('date', fn ($row) => localDate($row->date))
                ->editColumn('from_date', fn ($row) => localDate($row->from_date))
                ->editColumn('to_date', fn ($row) => localDate($row->to_date))
                ->escapeColumns([])
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'btn_config' => [
                            'detail' => [
                                'display' => true,
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
                ->make();
        }
    }

    public function employee_data(Request $request)
    {
        $data = \App\Models\Employee::join('divisions', 'employees.division_id', '=', 'divisions.id')
            ->join('positions', 'employees.position_id', '=', 'positions.id')
            ->where('employees.employee_status', 'active')
            ->select('employees.*', 'divisions.name as division_name', 'positions.nama as position_nama')
            ->whereDate('join_date', '<=', Carbon::now()->subYear())
            ->when($request->branch_id, function ($q) use ($request) {
                $q->where('employees.branch_id', $request->branch_id);
            })
            ->when($request->employee_ids, function ($q) use ($request) {
                $q->whereIn('employees.id', $request->employee_ids);
            });

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('user', function ($row) {
                $data = '';
                foreach ($row->users as $user) {
                    $data .= $user ? '<a href="' . route("admin.user.show", $user->id) . '" class="text-primary text-decoration-underline hover_text-dark">' . $user->username . '</a>' : '';
                }
                return $data;
            })
            ->editColumn('name', function ($row) {
                if (strlen($row->name) > 25) {
                    return substr($row->name, 0, 25) . '...';
                } else {
                    return $row->name;
                }
            })
            ->editColumn('position_nama', function ($row) {
                return $row->position?->nama ?? '-';
            })
            ->editColumn('division_name', function ($row) {
                return $row->division?->name ?? '-';
            })
            ->make(true);
    }
}
