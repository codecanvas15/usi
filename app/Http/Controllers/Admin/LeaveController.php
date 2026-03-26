<?php

namespace App\Http\Controllers\Admin;

use App\Models\Leave;
use App\Models\Leave as model;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Authorization;
use App\Models\ResetLeave;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class LeaveController extends Controller
{
    use \App\Http\Helpers\ActivityStatusLogHelper;

    protected string $view_folder = 'leave';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder", ['only' => ['index']]);
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
        return view("admin.$this->view_folder.create", compact('model'));
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
            'employee_id' => 'required|exists:employees,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'cause' => 'required|string',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'note' => 'required|string',
            'type' => 'required|string',
            'necessary' => 'required|string',
            'date' => 'required|date',
            'attachment' => 'file|max:5120'
        ]);

        $from_date = Carbon::parse($request->from_date)->startOfDay();
        $to_date = Carbon::parse($request->to_date)->endOfDay();

        if (Carbon::parse($from_date)->format('m-Y') != Carbon::parse($to_date)->format('m-Y')) {
            return redirect()->back()->withInput()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal awal dan akhir harus dalam bulan yang sama'));
        }

        DB::beginTransaction();

        $employee = Employee::findOrFail($request->employee_id);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $this->upload_file($request->file('attachment'), 'leave-attachment');
        }

        $model = new Leave();
        $model->fill([
            'employee_id' => $request->employee_id,
            'from_date' => Carbon::parse($request->from_date),
            'to_date' => Carbon::parse($request->to_date),
            'note' => $request->note,
            'day' => $request->type == "cuti" ? $from_date->diffInDaysFiltered(function (Carbon $date) {
                return !$date->isWeekend();
            }, $to_date) : 0,
            'cause' => $request->cause,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'division_id' => $employee->division_id,
            'type' => $request->type,
            'necessary' => $request->necessary,
            'leave_remaining' => $request->leave_remaining,
            'date' => Carbon::parse($request->date),
            'attachment' => $attachment,
        ]);

        try {
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: 0,
                title: "Cuti Pegawai",
                subtitle: Auth::user()->name . " mengajukan cuti pegawai " . $model->employee->name,
                link: route('admin.leave.show', $model),
                update_status_link: route('admin.leave.update-status', ['id' => $model->id]),
                division_id: $model->division_id
            );
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
        $model = Leave::findOrFail($id);
        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];
        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: model::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );

        $authorization_log_file_view = '';
        if ($change_file = $model->changeFile?->where('status', 'pending')->first()) {
            $authorization_log_files = $authorization_helper->get_authorization_logs(
                model: \App\Models\LeaveChangeFile::class,
                model_id: $change_file->id,
                user_id: Auth::user()->id,
            );

            $authorization_log_file_view = view('components.authorization_log', $authorization_log_files)->render();
        }
        $authorization_logs['can_revert'] = false;
        $authorization_logs['can_void'] = false;
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'authorization_log_file_view'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = Leave::findOrFail($id);

        if ($model->status != 'pending') {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, 'Data tidak dapat diubah karena status sudah di approve atau reject'));
        }

        return view("admin.$this->view_folder.edit", compact('model'));
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
        $model = Leave::findOrFail($id);

        if ($model->status != 'pending') {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, 'Data tidak dapat diubah karena status sudah di approve atau reject'));
        }

        $this->validate($request, [
            'employee_id' => 'required|exists:employees,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'note' => 'required|string',
            'cause' => 'required|string',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'type' => 'required|string',
            'necessary' => 'required|string',
            'date' => 'required|date',
        ]);

        $from_date = Carbon::parse($request->from_date)->startOfDay();
        $to_date = Carbon::parse($request->to_date)->endOfDay();

        if (Carbon::parse($from_date)->format('m-Y') != Carbon::parse($to_date)->format('m-Y')) {
            return redirect()->back()->withInput()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal awal dan akhir harus dalam bulan yang sama'));
        }

        $employee = Employee::findOrFail($request->employee_id);


        $attachment = $model->attachment;
        if ($request->hasFile('attachment')) {
            $attachment = $this->upload_file($request->file('attachment'), 'leave-attachment');
        }

        $model->fill([
            'branch_id' => $request->branch_id,
            'employee_id' => $request->employee_id,
            'from_date' => Carbon::parse($request->from_date),
            'to_date' => Carbon::parse($request->to_date),
            'note' => $request->note,
            'day' => $request->type == "cuti" ? $from_date->diffInDaysFiltered(function (Carbon $date) {
                return !$date->isWeekend();
            }, $to_date) : 0,
            'cause' => $request->cause,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'division_id' => $employee->division_id,
            'type' => $request->type,
            'necessary' => $request->necessary,
            'leave_remaining' => $request->leave_remaining,
            'date' => Carbon::parse($request->date),
            'attachment' => $attachment,
        ]);

        try {
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: 0,
                title: "Cuti Pegawai",
                subtitle: Auth::user()->name . " mengajukan cuti pegawai " . $model->employee->name,
                link: route('admin.leave.show', $model),
                update_status_link: route('admin.leave.update-status', ['id' => $model->id]),
                division_id: $model->division_id
            );
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
        $model = Leave::findOrFail($leave);

        if ($model->status != 'pending') {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, 'Data tidak dapat dihapus karena status sudah di approve atau reject'));
        }

        DB::beginTransaction();

        try {
            $model->delete();

            Authorization::where('model', model::class)
                ->where('model_id', $model->id)
                ->delete();
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
            $query = Leave::when(
                get_current_branch()->is_primary
                    && $request->branch_id,
                function ($query) {
                    $query->where('branch_id', get_current_branch()->id);
                }
            )
                ->when(!Auth::user()->can('create employee'), function ($query) {
                    if (Auth::user()->employee) {
                        $query->where('employee_id', Auth::user()->employee->id);
                    }
                })
                ->when(!get_current_branch()->is_primary, function ($query) {
                    $query->where('branch_id', get_current_branch()->id);
                })
                ->when($request->status, function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->when($request->employee_id, function ($query) use ($request) {
                    $query->where('employee_id', $request->employee_id);
                })
                ->when($request->from_date, function ($query) use ($request) {
                    $query->where('from_date', Carbon::parse($request->from_date));
                })
                ->when($request->to_date, function ($query) use ($request) {
                    $query->where('to_date', Carbon::parse($request->to_date));
                });


            $search = $request->input('search.value');
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('from_date', 'LIKE', "%{$search}%")
                        ->orWhere('to_date', 'LIKE', "%{$search}%");
                })
                    ->whereHas('employee', function ($u) use ($search) {
                        $u->where('name', 'LIKE', "%$search%");
                    })->orWhere('status', $search);
            }

            $data = $query->get();

            return datatables($data)
                ->addIndexColumn()
                ->editColumn('employee', fn ($row) => view('components.datatable.detail-link', [
                    'field' => $row->employee->name,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('from_date', fn ($row) => localDate($row->from_date))
                ->editColumn('to_date', fn ($row) => localDate($row->to_date))
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . permission_letter_status()[$row->status]['color'] . '">
                                            ' . permission_letter_status()[$row->status]['label'] . ' - ' . permission_letter_status()[$row->status]['text'] . '
                                        </div>';

                    return $badge;
                })
                ->addColumn('export', function ($row) {
                    $link = route("leave.export", ['id' => encryptId($row->id)]);
                    $export = '<a target="_blank" href="' . $link . '" class="btn btn-sm btn-light" onclick="show_print_out_modal(event)" data-module="leave"><i class="fa fa-file-pdf"></i></a>';

                    return $export;
                })
                ->rawColumns(['status', 'employee', 'export', 'action'])
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
                                'display' => $row->status != "approve" &&  $row->status != "reject",
                            ],
                            'delete' => [
                                'display' => $row->status != "approve" &&  $row->status != "reject",
                            ],
                        ],
                    ]);
                })
                ->make();
        }
    }

    public function export($id, Request $request)
    {
        $model = Leave::findOrFail(decryptId($id));

        $fileName = 'CUTI -  ' . strtoupper($model->employee->name) . '.pdf';
        $leave_taken = Leave::where('employee_id', $model->employee_id)
            ->whereYear('from_date', Carbon::parse($model->from_date)->format('Y'))
            ->where('type', 'cuti')
            ->where('id', '<', $model->id)
            ->sum('day');

        $last_leave_remaining = $model->employee->leave - $leave_taken - $model->day;

        $pdf = Pdf::loadview("admin.$this->view_folder.export", compact('model', 'leave_taken', 'last_leave_remaining'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');

        return $pdf->stream($fileName);
    }

    public function dataLeave(Request $request)
    {
        if ($request->from_date && $request->to_date) {
            $from_date = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::now();
            $to_date = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::now();

            $period = ResetLeave::whereDate('from_date', '<=', Carbon::parse($from_date))
                ->whereDate('to_date', '>=', Carbon::parse($to_date))
                ->where('status', 'open')
                ->first();

            if (!$period) {
                return $this->ResponseJsonData(null, 'Periode cuti sudah habis atau sudah di tutup.', 500);
            }

            $leaves = Leave::whereIn('status', ['approve', 'pending'])
                ->where('type', 'cuti')
                ->where('employee_id', $request->employee_id)
                ->when($period, function ($query) use ($period) {
                    return $query->whereDate('from_date', '>=', Carbon::parse($period->from_date))
                        ->whereDate('to_date', '<=', Carbon::parse($period->to_date));
                })
                ->sum('day');

            $employee = Employee::where('id', $request->employee_id)->first();
            $remaining_leave = $employee->leave - $leaves;

            $submitted_leave_day_count = $from_date->diffInDaysFiltered(function (Carbon $date) {
                return !$date->isWeekend();
            }, $to_date);

            if ($submitted_leave_day_count > $remaining_leave) {
                return $this->ResponseJsonData(null, 'Jumlah cuti yang tersisa tidak mencukupi.', 500);
            }
        } else {
            $remaining_leave = 0;
        }

        return $this->ResponseJsonData($remaining_leave);
    }

    /**
     * update_status
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, $id)
    {
        $model = Leave::findOrFail($id);

        DB::beginTransaction();
        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(Leave::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);
                if ($request->status !== 'approve') {
                    $model->status = $request->status;
                } else {
                    $model->status = 'approve';
                    $model->second_approved_by = auth()->user()->id;
                    $model->first_approved_by = auth()->user()->id;
                }

                $model->save();
            } else {
                $this->create_activity_status_log(Leave::class, $id, $request->note ?? $request->message ?? 'message not available', null, $request->status);
            }

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'update status', $th->getMessage()));
        }
        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update', 'update status'));
    }

    public function changedFileAttachment(Request $request, $id)
    {
        $this->validate($request, [
            'file_path' => 'required|file|max:5120',
        ]);

        $leave = Leave::findOrFail($id);

        $attachment = $this->upload_file($request->file('file_path'), 'leave-attachment');

        DB::beginTransaction();
        try {
            $model = \App\Models\LeaveChangeFile::query()
                ->where('leave_id', $id)
                ->where('status', 'pending')
                ->first();

            if (!$model) {
                $model = new \App\Models\LeaveChangeFile();
            }

            $model->file_path = $attachment;
            $model->file_name = $request->file('file_path')->getClientOriginalName() ?? null;
            $model->leave_id = $id;
            $model->save();

            $leave->status = 'change_file';
            $leave->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: \App\Models\LeaveChangeFile::class,
                model_id: $model->id,
                amount: 0,
                title: "Perubahan File Cuti Pegawai",
                subtitle: Auth::user()->name . " mengajukan perubahan file cuti pegawai " . $model->leave->employee->name,
                link: route('admin.leave.show', $model),
                update_status_link: route('admin.leave.changed-file-attachment.update-status', $model->id),
                division_id: $model->leave->employee->division_id ?? null
            );

            DB::commit();
            return $this->ResponseJsonMessage('Berhasil mengajukan perubahan file attachment', 200);
        } catch (Throwable $th) {
            DB::rollBack();
            return $this->ResponseJsonMessage('Gagal dikarenakan: ' . $th->getMessage(), 500);
        }
    }

    public function updateStatusChangedFile(Request $request, $id)
    {
        $model = \App\Models\LeaveChangeFile::findOrFail($id);

        DB::beginTransaction();
        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(\App\Models\LeaveChangeFile::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);
                if ($request->status !== 'approve') {
                    try {
                        Storage::disk('public')->delete($model->file_path);
                    } catch (\Throwable $th) {
                    }

                    $model->status = $request->status;
                    $model->leave->status = 'approve';
                    $model->leave->save();
                } else {
                    try {
                        Storage::disk('public')->delete($model->leave->attachment);
                    } catch (\Throwable $th) {
                    }

                    $model->status = 'approved';
                    $model->leave->attachment = $model->file_path;
                    $model->leave->status = 'approve';
                    $model->leave->save();
                }

                $model->delete();
            } else {
                $this->create_activity_status_log(\App\Models\LeaveChangeFile::class, $id, $request->note ?? $request->message ?? 'message not available', null, $request->status);
            }

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'update status', $th->getMessage()));
        }
        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update', 'update status'));
    }

    public function checkHaveFileChangePending($id)
    {
        $model = Leave::findOrFail($id);
        $model = $model->changeFile?->where('status', 'pending')->first();
        return $this->ResponseJsonData([
            'have_pending' => $model ? true : false,
            'data' => $model->file_path ?? null,
        ]);
    }
}
