<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Authorization;
use App\Models\ContractExtension;
use App\Models\Employee;
use App\Models\LaborApplication;
use App\Models\MasterLetter;
use App\Models\SpecificTimeWorkAgreement as model;
use App\Models\SpecificTimeWorkAgreement;
use App\Models\UserAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

// use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SpecificTimeWorkAgreementController extends Controller
{
    use \App\Http\Helpers\ActivityStatusLogHelper;

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
    public $view_folder = "specific-time-work-agreement";

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = model::with('employee')
                ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                    return $query->where('branch_id', $request->branch_id);
                })
                ->when(!get_current_branch()->is_primary, function ($query) {
                    return $query->where('branch_id', get_current_branch()->id);
                })
                ->when($request->status, function ($query) use ($request) {
                    return $query->where('status', $request->status);
                })
                ->when($request->employee_id, function ($query) use ($request) {
                    return $query->where('employee_id', $request->employee_id);
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('employee_id', function ($row) {
                    return '<a href="' . route("admin.employee.show", $row->employee_id) . '" class="text-primary text-decoration-underline hover_text-dark">' . $row->employee->name . '</a>';
                })
                ->editColumn('date', fn($row) => localDate($row->date))
                ->editColumn('reference_id', function ($row) {
                    if ($row->second_employee_type == 'new') {
                        $ua = UserAssessment::withTrashed()->with('candidate_data')->find($row->reference_id);
                        if ($ua) {
                            return '<a href="' . route("admin.labor-application.show", $ua->candidate) . '" class="text-primary text-decoration-underline hover_text-dark">' . $ua->candidate_data->name . '</a>';
                        } else {
                            return '-';
                        }
                    } else {
                        $ce = ContractExtension::withTrashed()->with('employee')->findOrFail($row->reference_id);
                        if ($ce) {
                            return '<a href="' . route("admin.employee.show", $ce->employee->id) . '" class="text-primary text-decoration-underline hover_text-dark">' . $ce->employee->name . '</a>';
                        } else {
                            return '-';
                        }
                    }
                })
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . specific_time_work_agreement_status()[$row->status]['color'] . '">
                                            ' . specific_time_work_agreement_status()[$row->status]['label'] . ' - ' . specific_time_work_agreement_status()[$row->status]['text'] . '
                                        </div>';
                    return $badge;
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
                                'display' => in_array($row->status, ['pending', 'revert']),
                            ],
                            'delete' => [
                                'display' => in_array($row->status, ['pending', 'revert']),
                            ],
                        ],
                    ]);
                })
                ->editColumn('export', function ($row) {
                    $link = route('specific-time-work-agreement.export.id', ['id' => encryptId($row->id)]);
                    $export = '<a href="' . $link . '" target="_blank"   class="btn btn-sm btn-flat btn-info" onclick="show_print_out_modal(event)">Export</a>';

                    return $export;
                })
                ->rawColumns(['code', 'employee_id', 'reference_id', 'status', 'action', 'export'])
                ->make(true);
        }

        return view("admin.$this->view_folder.index");
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

        return view("admin.$this->view_folder.create");
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

            'employee_id' => 'required|exists:employees,id',
            'division_id' => 'nullable|exists:divisions,id',
            'position_id' => 'nullable|exists:positions,id',
            'second_division_id' => 'nullable|exists:divisions,id',
            'second_position_id' => 'nullable|exists:positions,id',

            'date' => 'required|date',
            'title' => 'required|string|max:100',
            'second_employee_type' => 'required|string',
            'description' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,docs,xlsx',
        ]);

        DB::beginTransaction();

        $refModel = $request->second_employee_type == 'new' ? \App\Models\UserAssessment::class : \App\Models\ContractExtension::class;

        $model = new \App\Models\SpecificTimeWorkAgreement();
        $model->fill([
            'branch_id' => $request->branch_id ?? get_current_branch()->id,
            'employee_id' => $request->employee_id,
            'division_id' => $request->division_id,
            'position_id' => $request->position_id,
            'second_division_id' => $request->second_division_id,
            'second_position_id' => $request->second_position_id,
            'second_employee_type' => $request->second_employee_type,
            'reference_id' => $request->reference_id,
            'reference_model' => $refModel,
            'date' => Carbon::parse($request->date),
            'title' => $request->title,
            'work_agreement_type' => $request->second_employee_type,
            'description' => $request->description,
            'attachment' => $request->attachment ? $this->upload_file($request->file, $this->view_folder) : null,
        ]);

        try {
            $model->save();

            $update_description = str_replace('{nomor_dokumen}', $model->code, $model->description);
            DB::table('specific_time_work_agreements')
                ->where('id', $model->id)
                ->update(['description' => $update_description]);

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: 0,
                title: "PKWT",
                subtitle: Auth::user()->name . " mengajukan PKWT " . $model->code,
                link: route('admin.specific-time-work-agreement.show', $model),
                update_status_link: route('admin.specific-time-work-agreement.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );

            $employeeName = '';
            if ($model->second_employee_type == 'new') {
                $ua = UserAssessment::findOrFail($model->reference_id);
                $reference = LaborApplication::findOrfail($ua->candidate);
                $employeeName = $reference->name;
            } else {
                $ce = ContractExtension::findOrFail($model->reference_id);
                $employeeName = $ce->employee->name;
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = model::with([
            'branch',
            'employee',
            'division',
            'position',
            'second_division',
            'second_position',
            'created_by_data',
            'approved_by_data',
        ])->findOrFail($id);

        if ($model->second_employee_type == 'new') {
            $ua = UserAssessment::withTrashed()->with('candidate_data')->find($model->reference_id);
            $model->reference = $ua;
        } else {
            $ce = ContractExtension::withTrashed()->with('employee')->find($model->reference_id);
            $model->reference = $ce;
        }

        validate_branch($model->branch_id);

        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: model::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );

        $authorization_logs['can_revert'] = in_array($model->status, ['approve']);
        $authorization_logs['can_void'] = in_array($model->status, ['approve']);
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = $model->check_available_date && in_array($model->status, ['approve']);
        $authorization_logs['can_void_request'] = $model->check_available_date && in_array($model->status, ['approve']);
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'auth_revert_void_button'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = \App\Models\SpecificTimeWorkAgreement::with([
            'branch',
            'employee',
            'division',
            'position',
            'second_division',
            'second_position',
            'created_by_data',
            'approved_by_data',
        ])->findOrFail($id);

        validate_branch($model->branch_id);

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
        $model = \App\Models\SpecificTimeWorkAgreement::findOrFail($id);
        validate_branch($model->branch_id);

        $this->validate($request, [
            'branch_id' => 'nullable|exists:branches,id',
            'employee_id' => 'required|exists:employees,id',
            'division_id' => 'nullable|exists:divisions,id',
            'position_id' => 'nullable|exists:positions,id',
            'second_division_id' => 'nullable|exists:divisions,id',
            'second_position_id' => 'nullable|exists:positions,id',
            'date' => 'required|date',
            'title' => 'required|string|max:100',
            'second_employee_type' => 'required|string',
            'description' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,docs,xlsx',
        ]);

        $old_file = $model->attachment;

        $refModel = $request->second_employee_type == 'new' ? \App\Models\UserAssessment::class : \App\Models\ContractExtension::class;

        DB::beginTransaction();
        $model->fill([
            'branch_id' => $request->branch_id,
            'employee_id' => $request->employee_id,
            'division_id' => $request->division_id,
            'position_id' => $request->position_id,
            'second_division_id' => $request->second_division_id,
            'second_position_id' => $request->second_position_id,
            'second_employee_type' => $request->second_employee_type,
            'reference_id' => $request->reference_id,
            'reference_model' => $refModel,
            'date' => Carbon::parse($request->date),
            'title' => $request->title,
            'work_agreement_type' => $request->second_employee_type,
            'description' => str_replace('{nomor_dokumen}', $model->code, $request->description),
            'attachment' => $request->attachment ? $this->upload_file($request->file, $this->view_folder) : null,
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
                title: "PKWT",
                subtitle: Auth::user()->name . " mengajukan PKWT " . $model->code,
                link: route('admin.specific-time-work-agreement.show', $model),
                update_status_link: route('admin.specific-time-work-agreement.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        // delete old file
        if ($request->hasFile('attachment') && $old_file) {
            $this->delete_file($old_file ?? '');
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = \App\Models\SpecificTimeWorkAgreement::findOrFail($id);

        validate_branch($model->branch_id);

        DB::beginTransaction();
        try {
            $model->delete();

            Authorization::where('model', model::class)->where('model_id', $model->id)->delete();
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "delete", null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, "delete"));
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
        // * get model
        $model = \App\Models\SpecificTimeWorkAgreement::findOrFail($id);

        // * validate
        validate_branch($model->branch_id);

        // * update status
        DB::beginTransaction();

        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);
                $model->update([
                    'status' => $request->status == 'revert' ? 'pending' : $request->status
                ]);
            } else {
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', null, $request->status);
            }

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            $employeeName = '';
            if ($model->second_employee_type == 'new') {
                $ua = UserAssessment::findOrFail($model->reference_id);
                $reference = LaborApplication::findOrfail($ua->candidate);
                $employeeName = $reference->name;
            } else {
                $ce = ContractExtension::findOrFail($model->reference_id);
                $employeeName = $ce->employee->name;
            }
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "update", null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, "update"));
    }

    public function selectSecondEmployee(Request $request)
    {
        $data = [];

        if ($request->search) {
            if ($request->second_employee_type == 'new') {
                $data = UserAssessment::with('candidate_data')
                    ->where('approval_status', 'approve')
                    ->orWhere('candidate_data.name', 'like', "%{$request->search}%")
                    ->orWhere('candidate_data.code', 'like', "%{$request->search}%")
                    ->orderBy('updated_at', 'DESC')
                    ->limit(10)
                    ->get();
            } else {
                $data = ContractExtension::with('employee')
                    ->where('status', 'approve')
                    ->orWhere('employee.name', 'like', "%{$request->search}%")
                    ->orderBy('updated_at', 'DESC')
                    ->limit(10)
                    ->get();
            }
        } else {
            if ($request->second_employee_type == 'new') {
                $data = UserAssessment::with('candidate_data')
                    ->where('approval_status', 'approve')
                    ->orderBy('updated_at', 'DESC')
                    ->get();
            } else {
                $data = ContractExtension::with('employee')
                    ->where('status', 'approve')
                    ->orderBy('updated_at', 'DESC')
                    ->get();
            }
        }

        return $this->ResponseJsonData($data);
    }
    public function export($id, Request $request)
    {
        $model = model::findOrFail(decryptId($id));
        $file = public_path('/pdf_reports/Report-PKWT-' . ucfirst($model->item) . '-' . microtime(true) . '.pdf');
        $fileName = 'Report-PKWT-' . ucfirst($model->item) . '-' . microtime(true) . '.pdf';

        $pdf = PDF::loadview("admin/.$this->view_folder./export", compact('model'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $w = $canvas->get_width();
        $h = $canvas->get_height();
        $canvas->page_text($w - 40, $h - 40, "{PAGE_NUM}", "sans-serif", 8, array(0, 0, 0));
        return $pdf->stream($fileName);
    }

    public function generate(Request $request)
    {
        try {
            $code = $request->code;
            $first_employee = Employee::find($request->first_employee_id);
            if ($request->second_employee_type == 'new') {
                $ua = UserAssessment::findOrFail($request->reference_id);
                $reference = LaborApplication::findOrfail($ua->candidate);
                $employeeName = $reference->name;
            } else {
                $ce = ContractExtension::findOrFail($request->reference_id);
                $second_employee = $ce->employee;
                $employeeName = $ce->employee->name;
            }

            $letter = MasterLetter::findOrFail($request->master_letter_id);
            $document_template = $letter->template;

            $document_template = str_replace('{nama_perusahaan}', getCompany()->name, $document_template);
            $document_template = str_replace('{nama_hrd}', $first_employee->name, $document_template);
            $document_template = str_replace('{nomor_ktp_hrd}', $first_employee->no_ktp, $document_template);
            $document_template = str_replace('{alamat_hrd}', $first_employee->alamat, $document_template);
            $document_template = str_replace('{jabatan_hrd}', $first_employee->position->nama ?? '', $document_template);
            $document_template = str_replace('{nama_pegawai}', $second_employee->name ?? $employeeName, $document_template);
            $document_template = str_replace('{nomor_ktp_pegawai}', $second_employee?->no_ktp, $document_template);
            $document_template = str_replace('{alamat_pegawai}', $second_employee?->alamat, $document_template);
            $document_template = str_replace('{jabatan_pegawai}', $second_employee?->position->nama ?? '', $document_template);

            return response()->json($document_template);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }

    public function generate_pkwt_code()
    {
        DB::beginTransaction();
        try {
            $data = SpecificTimeWorkAgreement::all();
            foreach ($data as $key => $value) {
                $get_pkwt_code_from_description  = explode('PKWT-', $value->description);
                $code = 'PKWT-' . substr($get_pkwt_code_from_description[1], 0, 15);

                $new_description = str_replace($code, $value->code, $value->description);

                DB::table('specific_time_work_agreements')
                    ->where('id', $value->id)
                    ->update(['description' => $new_description]);
            }

            DB::commit();

            return response()->json('success');
        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json($th->getMessage());
        }
    }
}
