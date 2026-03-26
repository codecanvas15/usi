<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\NotificationHelper;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\LaborTransferForm as model;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LaborTransferFormController extends Controller
{
    use \App\Http\Helpers\ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'labor-transfer-form';

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
            $data = model::with(['employee', 'submitted_by_data'])
                ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                    return $query->where('branch_id', $request->branch_id);
                })
                ->when(!get_current_branch()->is_primary, function ($query) {
                    return $query->where('branch_id', get_current_branch()->id);
                })
                ->when($request->approval_status, function ($query) use ($request) {
                    return $query->where('approval_status', $request->approval_status);
                })
                ->when($request->employee_id, function ($query) use ($request) {
                    return $query->where('employee_id', $request->employee_id);
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->translatedFormat('d F Y');
                })
                ->editColumn('reference', fn ($row) => view('components.datatable.detail-link', [
                    'field' => $row->reference,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('employee_id', function ($row) {
                    return '<a href="' . route("admin.employee.show", $row->employee->id) . '" class="text-primary text-decoration-underline hover_text-dark">' . ucwords(strtolower($row->employee->name)) . '</a>';
                })
                ->editColumn('submitted_by', function ($row) {
                    return '<a href="' . route("admin.employee.show", $row->submitted_by_data->id) . '" class="text-primary text-decoration-underline hover_text-dark">' . ucwords(strtolower($row->submitted_by_data->name)) . '</a>';
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
                        'btn_config' =>
                        [
                            'detail' => [
                                'display' => false,
                            ],
                            'edit' => [
                                'display' => false,
                            ],
                            'delete' => [
                                'display' => true,
                            ],
                        ],
                    ]);
                })->addColumn('export', function ($row) {
                    return view('components.datatable.export-button', [
                        "route" => route("labor-transfer-form.export", ['id' => encryptId($row->id)]),
                        "onclick" => "show_print_out_modal(event)",
                    ]);
                })
                ->rawColumns(['reference', 'employee_id', 'submitted_by', 'approval_status', 'action'])
                ->make(true);
        }
        return view('admin.' . $this->view_folder . '.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
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
        $created_date = Carbon::now()->format('Y-m-d');
        $branch = Branch::find(auth()->user()->branch->id);

        $branch_id = $request->branch_id ?? auth()->user()->branch->id;
        $branch = \App\Models\Branch::findOrFail($branch_id);

        $code = generate_code(model::class, 'reference', 'created_at', "HRD-LT", date: $created_date, branch_sort: $branch->sort);

        // * create data
        $model = new model();
        $model->fill([
            'branch_id' => $branch_id,
            'employee_id' => $request->employee,
            'submitted_by' => $request->submitted_by,
            'reference' => $code,
            'from_company' => $request->from_company,
            'to_company' => $request->to_company,
            'from_branch' => $request->from_branch,
            'to_branch' => $request->to_branch,
            'from_division' => $request->from_division,
            'to_division' => $request->to_division,
            'reason' => $request->reason,
            'created_by' => auth()->user()->id,
        ]);

        // if (!$model->check_available_date) {
        //     return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        // }

        // * saving and make reponse
        DB::beginTransaction();
        try {
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: 0,
                title: "Pemindahan Tenaga Kerja",
                subtitle: auth()->user()->name . " mengajukan pemindahan tenaga kerja " . $model->reference,
                link: route('admin.labor-transfer-form.show', $model->id),
                update_status_link: route('admin.labor-transfer-form.update_status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );

            $notification = new NotificationHelper();
            $notification->send_notification(
                title: "PEMINDAHAN TENAGA KERJA",
                body: $model->reference . ' - ' . $model->employee->name,
                reference_model: get_class($model),
                reference_id: $model->id,
                branch_id: $model->branch_id,
                permissions: ['approve ' . $this->view_folder],
                link: route('admin.labor-transfer-form.show', ['labor_transfer_form' => $model->id]),
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
    public function show(Request $request, $id)
    {
        $model = model::with([
            'branch',
            'from_branch_data',
            'to_branch_data',
            'from_division_data',
            'to_division_data',
            'submitted_by_data',
            'created_by_data',
            'approved_by_data',
        ])->findOrFail($id);

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
    public function edit($id)
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
    public function destroy($id)
    {
        return redirect()->route('admin.' . $this->view_folder . '.index')->with($this->ResponseMessageCRUD(true, 'delete'));
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

        // if (!$model->check_available_date) {
        //     return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, 'Tanggal sudah closing'));
        // }

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

            if ($request->status == 'approve') {
                Employee::findOrFail($request->employee_id)->update([
                    'employee_status' => 'non_aktif',
                ]);
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


    public function export($id, Request $request)
    {
        $model = model::findOrFail(decryptId($id));
        $fileName = 'Labor-Transfer-Form-' . microtime(true) . '.pdf';
        $file = public_path('/pdf_reports/' . $fileName);

        $pdf = Pdf::loadView("admin.$this->view_folder.export", compact('model'))
            ->setPaper($request->paper ?? 'a4', $request->landscape ?? 'portrait');
        $pdf->render();

        return $pdf->stream($fileName);
    }
}
