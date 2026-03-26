<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermissionLetterEmployee as model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Helpers\ActivityStatusLogHelper;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PermissionLetterEmployeeController extends Controller
{
    use ActivityStatusLogHelper;

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
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'permission-letter-employee';

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
            $data = model::orderByDesc('permission_letter_employees.created_at')
                ->where('letter_type', $request->type)
                ->when(!get_current_branch()->is_primary, fn ($q) => $q->where('branch_id', get_current_branch_id()))
                ->when($request->from_date, fn ($q) => $q->whereDate('created_at', '>=', Carbon::parse($request->from_date)))
                ->when($request->to_date, fn ($q) => $q->whereDate('created_at', '<=', Carbon::parse($request->to_date)))
                ->with('employee')
                ->when(!Auth::user()->can('create employee'), function ($query) {
                    if (Auth::user()->employee) {
                        $query->where('employee_id', Auth::user()->employee->id);
                    }
                })
                ->select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('letter_date_start', fn ($row) => localDate($row->letter_date_start))
                ->editColumn('letter_date_end', fn ($row) => localDate($row->letter_date_end))
                ->editColumn('letter_number', fn ($row) => view('components.datatable.detail-link', [
                    'field' => $row->letter_number,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('letter_status', function ($model) {
                    $badge = '<div class="badge badge-lg badge-' . permission_letter_status()[$model->letter_status]['color'] . '">
                                            ' . permission_letter_status()[$model->letter_status]['label'] . ' - ' . permission_letter_status()[$model->letter_status]['text'] . '
                                        </div>';

                    return $badge;
                })
                ->addColumn('division', function ($row) {
                    return $row->employee->division?->name;
                })
                ->addColumn('export', function ($row) {
                    $link = route("permission-letter-employee.export", ['id' => encryptId($row->id)]);
                    $export = '<a target="_blank" href="' . $link . '" class="btn btn-sm btn-light" onclick="show_print_out_modal(event)" data-module="permission-letter-employee"><i class="fa fa-file-pdf"></i></a>';

                    return $export;
                })
                ->rawColumns(['letter_status', 'letter_number', 'export'])
                ->make(true);
        }

        return view('admin.' . $this->view_folder . '.index');
    }

    /**
     * data
     *
     * @param Request $request
     * @return mixed
     */
    public function data(Request $request)
    {
        // body
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
    public function store(Request $request)
    {
        DB::beginTransaction();
        // * validate
        if ($request->ajax()) {
            $this->validate_api($request->all(), model::rules());
        } else {
            $this->validate($request, model::rules());
        }

        $types = [
            'came too late' => 'T',
            'leave during working hours' => 'K',
            'leave early' => 'P',
            'not work' => 'N'
        ];

        $last_model = model::where('letter_type', $request->type)
            ->where('branch_id', get_current_branch_id())
            ->orderByDesc('created_at')
            ->first();

        // * create data
        $model = new model();
        $model->loadModel($request->all());
        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        }
        $model->branch_id = get_current_branch_id();
        $model->letter_type = $request->type;
        if ($request->file) {
            $model->file = $this->upload_file($request->file('file'), 'permission-letter-employee');
        }
        $model->letter_number = generate_code_transaction("PRO-HRD-{$types[$request->type]}", $last_model->letter_number ?? "0000-0000-0000-0000");
        $types = [
            'came too late' => 'T',
            'leave during working hours' => 'K',
            'leave early' => 'P',
            'not work' => 'N',
        ];

        if ($types[$request->type] == 'T') {
            $letter_date_start = Carbon::parse($request->letter_date)->format('Y-m-d') . " " . date("H:i:s", strtotime($request->letter_date_start));
            $model->letter_date_start = $letter_date_start;
        } elseif ($types[$request->type] == 'K') {
            $letter_date_start = Carbon::parse($request->letter_date)->format('Y-m-d') . " " . date("H:i:s", strtotime($request->letter_date_start));
            $letter_date_end = Carbon::parse($request->letter_date)->format('Y-m-d') . " " . date("H:i:s", strtotime($request->letter_date_end));
            $model->letter_date_start = $letter_date_start;
            $model->letter_date_end = $letter_date_end;
        } elseif ($types[$request->type] == 'P') {
            $letter_date_end = Carbon::parse($request->letter_date)->format('Y-m-d') . " " . date("H:i:s", strtotime($request->letter_date_end));
            $model->letter_date_end = $letter_date_end;
        } elseif ($types[$request->type] == 'N') {
            $model->letter_date_start = Carbon::parse($request->letter_date)->format('Y-m-d') . " " . date("H:i:s", strtotime($request->letter_date));
            $model->letter_date_end = $request->letter_date_end . " " . date("H:i:s", strtotime($request->letter_date_end));
        }

        // * saving and make reponse
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD());
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $int
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $model = model::findOrFail($id);
        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $model = model::findOrFail($id);
        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }
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
        $model = model::findOrFail($id);
        DB::beginTransaction();
        // * validate
        if ($request->ajax()) {
            $this->validate($request, model::rules());
        } else {
            $this->validate_api($request->all(), model::rules());
        }
        // * update data
        $old_file = $model->file;
        $model->loadModel($request->all());
        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal sudah closing'));
        }
        if (null !== $request->file('file')) {
            try {
                $this->delete_file($old_file);
                $model->file = $this->upload_file($request->file('file'), 'permission-letter-employee');
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        $model->branch_id = get_current_branch_id();
        $types = [
            'came too late' => 'T',
            'leave during working hours' => 'K',
            'leave early' => 'P',
            'not work' => 'N'
        ];
        $model->letter_type = $request->type;
        $model->letter_status = "pending";
        if ($types[$request->type] == 'T') {
            $letter_date_start = Carbon::parse($request->letter_date)->format('Y-m-d') . " " . date("H:i:s", strtotime($request->letter_date_start));
            $model->letter_date_start = $letter_date_start;
        } elseif ($types[$request->type] == 'K') {
            $letter_date_start = Carbon::parse($request->letter_date)->format('Y-m-d') . " " . date("H:i:s", strtotime($request->letter_date_start));
            $letter_date_end = Carbon::parse($request->letter_date)->format('Y-m-d') . " " . date("H:i:s", strtotime($request->letter_date_end));
            $model->letter_date_start = $letter_date_start;
            $model->letter_date_end = $letter_date_end;
        } elseif ($types[$request->type] == 'P') {
            $letter_date_end = Carbon::parse($request->letter_date)->format('Y-m-d') . " " . date("H:i:s", strtotime($request->letter_date_end));
            $model->letter_date_end = $letter_date_end;
        } elseif ($types[$request->type] == 'N') {
            $model->letter_date_start = Carbon::parse($request->letter_date)->format('Y-m-d') . " " . date("H:i:s", strtotime($request->letter_date));
            $model->letter_date_end = $request->letter_date_end . " " . date("H:i:s", strtotime($request->letter_date_end));
        }

        // * saving and make reponse
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'edit'));
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
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'delete', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
        }
        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD(false, 'delete');
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request)
    {
        if ($request->search) {
            $model = model::where('name', 'like', "%$request->search%")->orderByDesc('created_at')->limit(10)->get();
        } else {
            $model = model::orderByDesc('created_at')->limit(10)->get();
        }

        return $this->ResponseJsonData($model);
    }

    public function update_status(Request $request, $id)
    {
        Db::beginTransaction();

        $model = model::findOrfail($id);
        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, 'Tanggal sudah closing'));
        }
        if ($model->branch_id != get_current_branch_id()) {
            return abort(403);
        }

        $this->create_activity_status_log(model::class, $id, $request->message ?? 'message not available', $model->letter_status, $request->status);
        // * saving and make response
        $model->letter_status = $request->status;
        if ($request->status == 'reject') {
            $model->keterangan .= $request->message;
        }

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    public function export($id, Request $request)
    {
        $model = model::findOrFail(decryptId($id));
        $fileName = 'SURAT IZIN PEGAWAI ' . strtoupper($model->item) . '.pdf';

        $viewPath = "admin/$this->view_folder";
        if ($request->paper == 'a4') {
            $viewPath .= '/export';
        } else if ($request->paper == 'a5') {
            $viewPath .= '/export-a5';
        }

        $pdf = FacadePdf::loadview($viewPath, compact('model'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');

        return $pdf->stream($fileName);
    }
}
