<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Branch;
use App\Models\LaborDemandDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Models\LaborApplication as model;
use App\Models\LaborApplication;
use App\Models\LaborApplicationDocument;
use App\Models\LaborApplicationEmergencyContact;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LaborApplicationController extends Controller
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
    protected string $view_folder = 'labor-application';

    /**
     * search  value
     *
     * @var string|null
     */
    protected string|null $search = null;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = \App\Models\LaborApplication::query()
                ->with(['employee'])
                ->orderByDesc('created_at')
                ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                    $query->where('branch_id', $request->branch_id);
                })
                ->when(!get_current_branch()->is_primary, function ($query) {
                    $query->where('branch_id', get_current_branch()->id);
                })
                ->when($request->from_date, fn ($q) => $q->whereDate('date', '>=', Carbon::parse($request->from_date)))
                ->when($request->to_date, fn ($q) => $q->whereDate('date', '<=', Carbon::parse($request->to_date)));

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('code', fn ($row) => view('components.datatable.detail-link', [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('date', fn ($row) => localDate($row->date))
                ->addColumn('employee', function ($row) {
                    if ($row->employee) {
                        return $row->employee->NIK . " - " . $row->employee->name;
                    } else {
                        return "-";
                    }
                })
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . labor_demand_status()[$row->status]['color'] . '">
                                                ' . labor_demand_status()[$row->status]['label'] . ' - ' . labor_demand_status()[$row->status]['text'] . '
                                            </div>';

                    return $badge;
                })
                ->editColumn('export', function ($row) {
                    $link = route("labor-application.export.id", ['id' => encryptId($row->id)]);
                    $export = '<a href="' . $link . '" class="btn btn-sm btn-flat btn-info" target="_blank" onclick="show_print_out_modal(event)">Export</a>';

                    return $export;
                })
                ->rawColumns(['code', 'status', 'export'])
                ->make(true);
        }

        return view("admin.$this->view_folder.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data['labor_demand_detail'] = [];
        if ($request->labor_demand_detail_id) {
            $data['labor_demand_detail'] = LaborDemandDetail::find($request->labor_demand_detail_id);
        }

        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(model::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

        return view("admin.$this->view_folder.create", $data);
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
            'employee_id' => 'nullable|exists:employees,id',
            'labor_demand_detail_id' => 'required|exists:labor_demand_details,id',
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required',
            'religion' => 'nullable|string|max:60',
            'gender' => 'required',
            'marital_status' => 'required',
            'identity_card_number' => 'required',

            'file_type' => 'required|array',
            'file_path' => 'required|array',
            'file_type.*' => 'required|string|max:255',
            'file_path.*' => 'required|mimes:pdf,jpg,jpeg,png|max:4096',

            'emergency_contact_names' => 'required|array',
            'emergency_contact_relationships' => 'required|array',
            'emergency_contact_phones' => 'required|array',
            'emergency_contact_addresses' => 'required|array',

            'emergency_contact_names.*' => 'required|string|max:100',
            'emergency_contact_relationships.*' => 'required|string|max:100',
            'emergency_contact_phones.*' => 'required|string|max:100',
            'emergency_contact_addresses.*' => 'required|string|max:100',
        ]);

        DB::beginTransaction();

        $branch_id = $request->branch_id ?? get_current_branch()->id;
        $branch = \App\Models\Branch::find($branch_id);

        // * parent
        $model = new \App\Models\LaborApplication();
        $branch = Branch::find($request->branch_id);

        $model->fill([
            'branch_id' => $request->branch_id,
            'employee_id' => $request->employee_id,
            'labor_demand_detail_id' => $request->labor_demand_detail_id,
            'code' => generate_code(\App\Models\LaborApplication::class, 'code', 'date', 'LA', branch_sort: $branch->sort ?? null, date: $request->date),
            'date' => Carbon::parse($request->date),
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'phone' => $request->phone,
            'date_of_birth' => Carbon::parse($request->date_of_birth),
            'place_of_birth' => $request->place_of_birth,
            'religion' => $request->religion,
            'gender' => $request->gender,
            'marital_status' => $request->marital_status,
            'identity_card_number' => $request->identity_card_number,
        ]);

        // if (!$model->check_available_date) {
        //     return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        // }

        try {
            $model->save();

            $token = md5(microtime());
            $generated_token = Str::substr($token, 0, 10);

            $qr_url = route('application', ['id' => $generated_token]);

            // * generate qr code
            $qr = QrCode::format('png')->size(250)->merge('/public/images/icon.png', .3)->generate($qr_url);
            $filename = 'labor-application-qr-code/' . $generated_token . '.png';
            Storage::disk('public')->put($filename, $qr);

            // * save token
            $application = new Application();
            $application->fill([
                'labor_application_id' => $model->id,
                'token' => $generated_token,
                'kode_akses' => mt_rand(100000, 999999),
                'qr' => $filename,
                'expiry' => Carbon::now()->addDays(3)->format('Y-m-d'),
            ]);
            $application->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', $th->getMessage()));
        }

        // * documents
        $document_file_data = [];
        foreach ($request->file_type as $key => $file_type) {
            $path = '';
            if (isset($request->file('file_path')[$key])) {
                $path = $this->upload_file($request->file('file_path')[$key], 'labor-application');
            }
            $document_file_data[] = [
                'type' => $file_type,
                'path' =>  $path,
            ];
        }

        try {
            $model->laborApplicationDocuments()->createMany($document_file_data);
        } catch (\Throwable $th) {
            DB::rollBack();

            // * delete uploaded file
            foreach ($document_file_data as $key => $value) {
                $this->delete_file($value['path'] ?? '');
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', $th->getMessage()));
        }

        // * emergency contact
        $emergency_contact_data = [];
        foreach ($request->emergency_contact_names as $key => $emergency_contact_name) {
            $emergency_contact_data[] = [
                'name' => $emergency_contact_name,
                'relationship' => $request->emergency_contact_relationships[$key],
                'phone' => $request->emergency_contact_phones[$key],
                'address' => $request->emergency_contact_addresses[$key],
            ];
        }

        try {
            $model->laborApplicationEmergencyContacts()->createMany($emergency_contact_data);

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: 0,
                title: "Lamaran Pekerjaan",
                subtitle: auth()->user()->name . " mengajukan lamaran pekerjaan  " . $model->code,
                link: route('admin.labor-application.show', $model->id),
                update_status_link: route('admin.labor-application.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', $th->getMessage()));
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
        $model = \App\Models\LaborApplication::findOrFail($id);
        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: model::class,
            model_id: $model->id,
            user_id: auth()->user()->id,
        );
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = model::find($id);


        if (!$model->check_available_date) {
            abort(403);
        }

        if (!in_array($model->status, ['pending', 'revert'])) {
            return abort(403);
        }

        validate_branch($model->branch_id);

        return view('admin.' . $this->view_folder . '.edit', compact('model'));
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
            'branch_id' => 'nullable|exists:branches,id',
            'employee_id' => 'nullable|exists:employees,id',
            'labor_demand_detail_id' => 'required|exists:labor_demand_details,id',
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required',
            'religion' => 'nullable|string|max:60',
            'gender' => 'required',
            'marital_status' => 'required',
            'identity_card_number' => 'required',

            'file_type' => 'required|array',
            'file_path' => 'array',
            'file_type.*' => 'required|string|max:255',
            'file_path.*' => 'mimes:pdf,jpg,jpeg,png|max:4096',

            'emergency_contact_names' => 'required|array',
            'emergency_contact_relationships' => 'required|array',
            'emergency_contact_phones' => 'required|array',
            'emergency_contact_addresses' => 'required|array',

            'emergency_contact_names.*' => 'required|string|max:100',
            'emergency_contact_relationships.*' => 'required|string|max:100',
            'emergency_contact_phones.*' => 'required|string|max:100',
            'emergency_contact_addresses.*' => 'required|string|max:100',
        ]);

        DB::beginTransaction();

        // * parent
        $model = LaborApplication::find($id);
        $model->fill([
            'branch_id' => $request->branch_id,
            'employee_id' => $request->employee_id,
            'labor_demand_detail_id' => $request->labor_demand_detail_id,
            'date' => Carbon::parse($request->date),
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'phone' => $request->phone,
            'date_of_birth' => Carbon::parse($request->date_of_birth),
            'place_of_birth' => $request->place_of_birth,
            'religion' => $request->religion,
            'gender' => $request->gender,
            'marital_status' => $request->marital_status,
            'identity_card_number' => $request->identity_card_number,
        ]);

        // if (!$model->check_available_date) {
        //     return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal sudah closing'));
        // }

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', $th->getMessage()));
        }

        // * documents

        foreach ($request->file_type as $key => $file_type) {
            if (isset($request->file('file_path')[$key])) {
                $document = LaborApplicationDocument::where('labor_application_id', $model->id)->where('type', $file_type)->first();
                $this->delete_file($document->path);
                $document->path = $this->upload_file($request->file('file_path')[$key], 'labor-application');
                try {
                    $document->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', $th->getMessage()));
                }
            }
        }

        // * emergency contact
        try {
            LaborApplicationEmergencyContact::where('labor_application_id', $model->id)->whereNotIn('id', $request->emergency_contact_id)->delete();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', $th->getMessage()));
        }

        foreach ($request->emergency_contact_names as $key => $emergency_contact_name) {
            if ($request->emergency_contact_id[$key]) {
                $emergency_contact = LaborApplicationEmergencyContact::find($request->emergency_contact_id[$key]);
            } else {
                $emergency_contact = new LaborApplicationEmergencyContact();
                $emergency_contact->labor_application_id = $model->id;
            }

            $emergency_contact->name = $emergency_contact_name;
            $emergency_contact->relationship = $request->emergency_contact_relationships[$key];
            $emergency_contact->phone = $request->emergency_contact_phones[$key];
            $emergency_contact->address = $request->emergency_contact_addresses[$key];

            try {
                $emergency_contact->save();
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', $th->getMessage()));
            }
        }

        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        $authorization->init(
            branch_id: $model->branch_id,
            user_id: auth()->user()->id,
            model: model::class,
            model_id: $model->id,
            amount: 0,
            title: "Lamaran Pekerjaan",
            subtitle: auth()->user()->name . " mengajukan lamaran pekerjaan " . $model->code,
            link: route('admin.labor-application.show', $model->id),
            update_status_link: route('admin.labor-application.update-status', ['id' => $model->id]),
            division_id: auth()->user()->division_id ?? null
        );

        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'create'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Update status the specified resource from storage.
     *
     * @param  int  $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function update_status($id, Request $request)
    {
        $model = \App\Models\LaborApplication::findOrFail($id);
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
                $this->create_activity_status_log(\App\Models\LaborApplication::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);
                $model->update([
                    'status' => $request->status == 'revert' ? 'pending' : $request->status,
                ]);
            } else {
                $this->create_activity_status_log(\App\Models\LaborApplication::class, $id, $request->note ?? $request->message ?? 'message not available', null, $request->status);
            }

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message ?? null,
            );
        } catch (\Throwable $th) {

            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update status', 'update status'));
    }

    public function select(Request $request)
    {
        if ($request->search) {
            $data = model::where('code', 'like', "%{$request->search}%")
                ->orWhere('name', 'like', "%{$request->search}%")
                ->limit(10)
                ->orderByDesc('created_at')
                ->when($request->status, function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->get();
        } else {
            $data = model::orderByDesc('created_at')
                ->when($request->status, function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->get();
        }

        return $this->ResponseJsonData($data);
    }

    public function findById(Request $request)
    {
        $model = model::with(['employee', 'laborDemandDetail', 'laborDemandDetail.position', 'laborDemandDetail.labor_demand', 'laborDemandDetail.labor_demand.division'])->findOrFail($request->id);
        return response()->json($model);
    }

    public function download()
    {
        return $this->ResponseDownload(public_path('download/5b. FM-HRD-05-02 - Formulir Lamaran Karyawan.doc'));
    }

    public function export($id, Request $request)
    {
        $model = model::findOrFail(decryptId($id));
        $file = public_path('/pdf_reports/Report-Labor-Application-' . ucfirst($model->item) . '-' . microtime(true) . '.pdf');
        $fileName = 'Report-Labor-Application-' . ucfirst($model->item) . '-' . microtime(true) . '.pdf';

        $qr_url = route('labor-application.export.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));

        // return view('admin.labor-application.export', compact('model', 'qr'));

        $pdf = PDF::loadview("admin/.$this->view_folder./export", compact('model', 'qr'))
            ->setPaper($request->paper ?? 'a4', $request->landscape ?? 'portrait');

        return $pdf->stream($fileName);
    }
}
