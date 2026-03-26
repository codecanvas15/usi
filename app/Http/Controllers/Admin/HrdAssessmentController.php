<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Authorization;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\HrdAssessment as model;
use App\Models\HrdAssessmentDetail as detail;
use App\Models\LaborApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class HrdAssessmentController extends Controller
{
    use \App\Http\Helpers\ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'hrd-assessment';

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
            $data = model::with(['candidate_data', 'candidate_data.employee', 'interviewer_data'])->orderByDesc('created_at')
                ->when(!get_current_branch()->is_primary, fn ($q) => $q->where('hrd_assessments.branch_id', get_current_branch_id()))
                ->when($request->from_date, fn ($q) => $q->whereDate('hrd_assessments.created_at', '>=', Carbon::parse($request->from_date)))
                ->when($request->to_date, fn ($q) => $q->whereDate('hrd_assessments.created_at', '<=', Carbon::parse($request->to_date)))
                ->select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('reference', fn ($row) => view('components.datatable.detail-link', [
                    'field' => $row->reference,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('assessment_date', function ($row) {
                    return Carbon::parse($row->assessment_date)->format('d F Y');
                })
                ->editColumn('candidate_data', function ($row) {
                    return '<a href="' . route("admin.labor-application.show", $row->candidate_data) . '" class="text-primary text-decoration-underline hover_text-dark">' . $row->candidate_data->name . '</a>';
                })
                ->editColumn('interviewer_data', function ($row) {
                    return '<a href="' . route("admin.employee.show", $row->interviewer_data->id) . '" class="text-primary text-decoration-underline hover_text-dark">' . $row->interviewer_data->name . '</a>';
                })
                ->editColumn('assessment_status', function ($row) {
                    if ($row->assessment_status == 'y') {
                        return '<span class="badge badge-info">Lanjut Tahap 2</span>';
                    } elseif ($row->assessment_status == 'r') {
                        return '<span class="badge badge-primary">Lanjut Dengan Reservasi</span>';
                    } else {
                        return '<span class="badge badge-danger">Tidan Lanjut</span>';
                    }
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
                        'btn_config' => [
                            'detail' => [
                                'display' => false,
                            ],
                            'edit' => [
                                'display' => false,
                            ],
                            'delete' => [
                                'display' => $row->check_available_date ? true : false,
                            ],
                        ],
                    ]);
                })
                ->editColumn('export', function ($row) {
                    $link = route("hrd-assessment.export.id", ['id' => encryptId($row->id)]);
                    $export = '<a href="' . $link . '" class="btn btn-sm btn-flat btn-info" target="_blank" onclick="show_print_out_modal(event)">Export</a>';

                    return $export;
                })
                ->rawColumns(['reference', 'candidate_data', 'interviewer_data', 'assessment_status', 'approval_status', 'action', 'export'])
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
        $interviewerBranchId = Employee::with('branch')->findOrFail($request->interviewer);
        $branch = Branch::find($interviewerBranchId->branch_id);


        $code = generate_code(model::class, 'reference', 'assessment_date', "HRD-HA", branch_sort: $branch->sort ?? null, date: Carbon::parse($request->date)->format('Y-m-d'));

        // * create data
        $model = new model();
        $model->fill([
            'branch_id' => $interviewerBranchId->branch->id ?? null,
            'interviewer' => $request->interviewer,
            'candidate' => $request->candidate,
            'position' => $request->position,
            'reference' => $code,
            'assessment_date' => Carbon::parse($request->date)->format('Y-m-d'),
            'assessment_status' => $request->assessment_status,
            'notes' => $request->notes,
        ]);


        // if (!$model->check_available_date) {
        //     return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        // }

        // * saving and make reponse
        DB::beginTransaction();
        try {
            $model->save();

            foreach ($request->master_hrd_assessment_id as $key => $id) {
                $detail = new detail();
                $detail->hrd_assessment_id = $model->id;
                $detail->master_hrd_assessment_id = $id;
                $detail->rating = $request->rating[$key];
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
                title: "HRD Assessment",
                subtitle: auth()->user()->name . " mengajukan hrd assessment  " . $model->reference,
                link: route('admin.hrd-assessment.show', $model->id),
                update_status_link: route('admin.hrd-assessment.update_status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

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
    public function show(Request $request, $id)
    {
        $model = model::with(['position_data', 'candidate_data', 'interviewer_data', 'detail'])->findOrFail($id);

        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

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
    public function edit(Request $request, $id)
    {
        return view('admin.' . $this->view_folder . '.edit');
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
        return view('admin.' . $this->view_folder . '.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $detail = detail::where('hrd_assessment_id', $id);
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

    public function selectCandidate(Request $request)
    {
        $approved_candidate_ids = [];
        $approved_assessments = model::where('approval_status', 'approve')->get();

        if (count($approved_assessments) > 0) {
            foreach ($approved_assessments as $assessment) {
                array_push($approved_candidate_ids, $assessment->candidate);
            }
        }

        if ($request->search) {
            $data = LaborApplication::with('employee')->where('code', 'like', "%{$request->search}%");

            if (count($approved_candidate_ids) > 0) {
                $data->whereNotIn('id', $approved_candidate_ids);
            }

            $data->where('status', 'approve')
                ->limit(10)
                ->orderByDesc('created_at')
                ->get();
        } else {
            if (count($approved_candidate_ids) > 0) {
                $data = LaborApplication::with('employee')
                    ->whereNotIn('id', $approved_candidate_ids)
                    ->where('status', 'approve')
                    ->orderByDesc('created_at')
                    ->get();
            } else {
                $data = LaborApplication::with('employee')
                    ->where('status', 'approve')
                    ->orderByDesc('created_at')
                    ->get();
            }
        }

        return $this->ResponseJsonData($data);
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
                    'approved_by' => auth()->user()->id,
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

    public function download()
    {
        return $this->ResponseDownload(public_path('download/5d. FM-HRD-05-04 - CANDIDATE - HRD ASSESSMENT.xlsx'));
    }

    public function export($id, Request $request)
    {
        $model = model::findOrFail(decryptId($id));
        $file = public_path('/pdf_reports/Report-HRD-Assessment-' . ucfirst($model->item) . '-' . microtime(true) . '.pdf');
        $fileName = 'Report-HRD-Assessment-' . ucfirst($model->item) . '-' . microtime(true) . '.pdf';

        $qr_url = route('hrd-assessment.export.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));

        $pdf = PDF::loadview("admin/.$this->view_folder./export", compact('model', 'qr'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');

        return $pdf->stream($fileName);
    }
}
