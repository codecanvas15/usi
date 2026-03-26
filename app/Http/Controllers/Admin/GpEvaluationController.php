<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Authorization;
use App\Models\Employee;
use App\Models\GpEvaluation as model;
use App\Models\GpEvaluationDetail as detail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class GpEvaluationController extends Controller
{
    use \App\Http\Helpers\ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'gp-evaluation';

    /**
     * search  value
     *
     * @var string|null
     */
    protected string|null $search = null;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = model::with(['branch', 'employee', 'created_by_data'])
                ->when(!get_current_branch()->is_primary, fn ($q) => $q->where('branch_id', get_current_branch_id()))
                ->when($request->from_date, fn ($q) => $q->where('gp_evaluations.date', Carbon::parse($request->from_date)))
                ->when($request->to_date, fn ($q) => $q->where('gp_evaluations.date', Carbon::parse($request->to_date)))
                ->orderByDesc('created_at')->select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('reference', fn ($row) => view('components.datatable.detail-link', [
                    'field' => $row->reference,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)->format('d F Y');
                })
                ->editColumn('employee_id', function ($row) {
                    return '<a href="' . route("admin.employee.show", $row->employee_id) . '" class="text-primary text-decoration-underline hover_text-dark">' . ucwords(strtolower($row->employee->name)) . '</a>';
                })
                ->editColumn('created_by', function ($row) {
                    return '<a href="' . route("admin.employee.show", $row->created_by_data->id) . '" class="text-primary text-decoration-underline hover_text-dark">' . ucwords(strtolower($row->created_by_data->name)) . '</a>';
                })
                ->addColumn('approval_status', function ($row) {
                    if ($row->approval_status == 'approve') {
                        return '<span class="badge badge-info">Approved</span>';
                    } elseif ($row->approval_status == 'pending') {
                        return '<span class="badge badge-warning">Pending - waiting approval</span>';
                    } else {
                        return '<span class="badge badge-dark">Reject - Assessment rejected</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'permission_name' => 'evaluation',
                        'btn_config' => [
                            'detail' => [
                                'display' => true,
                            ],
                            'edit' => [
                                'display' => false,
                            ],
                            'delete' => [
                                'display' => true,
                            ],
                        ],
                    ]);
                })
                ->rawColumns(['reference', 'employee_id', 'created_by', 'approval_status', 'action'])
                ->make(true);
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
        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(model::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

        return view('admin.' . $this->view_folder . '.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $created_by = Employee::with('branch')->findOrFail(auth()->user()->employee_id);

        $code = generate_code(model::class, 'reference', 'date', "HRD-GPE", date: Carbon::parse($request->date)->format('Y-m-d'), branch_sort: $created_by->branch->sort ?? null);
        // * create data
        $model = new model();
        $model->fill([
            'branch_id' => $created_by->branch->id ?? null,
            'employee_id' => $request->employee_id,
            'created_by' => $created_by->id,
            'reference' => $code,
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'total_score' => $request->total_score,
            'notes' => $request->notes,
        ]);

        // * saving and make reponse
        DB::beginTransaction();
        try {
            $model->save();

            foreach ($request->master_gp_evaluation_id as $key => $id) {
                $detail = new detail();
                $detail->gp_evaluation_id = $model->id;
                $detail->master_gp_evaluation_id = $id;
                $detail->score = $request->score[$key];
                $detail->notes = $request->detail_notes[$key];
                $detail->save();
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: 0,
                title: "Assessment Karyawan",
                subtitle: auth()->user()->name . " mengajukan assessment karyawan " . $model->reference,
                link: route('admin.gp-evaluation.show', $model->id),
                update_status_link: route('admin.gp-evaluation.update_status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
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

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'create'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = model::with(['branch', 'employee', 'created_by_data', 'approved_by_data', 'detail'])->findOrFail($id);

        validate_branch($model->branch_id);

        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: model::class,
            model_id: $model->id,
            user_id: auth()->user()->id,
        );
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        return view('admin.' . $this->view_folder . '.show', compact('model', 'status_logs', 'activity_logs', 'authorization_log_view'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $detail = detail::where('gp_evaluation_id', $id);
        $model = model::findOrFail($id);

        DB::beginTransaction();
        try {
            $detail->delete();
            $model->delete();

            Authorization::where('model', model::class)->where('model_id', $model->id)->delete();
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
     * Update status the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, $id)
    {
        $model = model::findOrFail($id);

        validate_branch($model->branch_id);

        DB::beginTransaction();
        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message ?? null,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', $model->approval_status, $request->status);
                $model->update([
                    'approval_status' => $request->status == 'revert' ? 'pending' : $request->status,
                    'approved_by' => auth()->user()->employee->id ?? null,
                ]);
            } else {
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', null, $request->status);
            }

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message ?? null,
            );
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "update", null, $th->getMessage()));
        }

        DB::commit();
        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, "update"));
    }
}
