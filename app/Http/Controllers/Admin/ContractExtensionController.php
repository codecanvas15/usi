<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssesmentContractExtension;
use App\Models\ContractExtension as model;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;

class ContractExtensionController extends Controller
{
    use \App\Http\Helpers\ActivityStatusLogHelper;

    protected string $view_folder = 'contract-extension';
    protected $assesment;
    protected $assesment_rules;

    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder", ['only' => ['index', 'show']]);
        $this->middleware("permission:create $this->view_folder", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit $this->view_folder", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete $this->view_folder", ['only' => ['destroy']]);
        $this->middleware("permission:import $this->view_folder", ['only' => ['import']]);
        $this->middleware("permission:export $this->view_folder", ['only' => ['export']]);
        $this->assesment = ['disiplin', 'kehadiran', 'sikap', 'kerja sama', 'hasil kerja'];
        $this->assesment_rules = [
            'disiplin' => 'required',
            'kehadiran' => 'required',
            'sikap' => 'required',
            'kerja_sama' => 'required',
            'hasil_kerja' => 'required',
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                "code",
                "division_id",
                "branch_id",
                "employee_id",
                "status",
                "created_at",
                "action",
            ];
            $query = model::with(['employee', 'division', 'branch']);
            $totalData = $query->count();
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
            $search = $request->input('search.value');

            if (!get_current_branch()->is_primary) {
                $query->where('contract_extensions.branch_id', get_current_branch_id());
            }

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('code', 'LIKE', "%{$search}%")
                        ->orWhereHas('employee', function($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('division', function($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('branch', function($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%");
                        });
                });
            }

            if (!empty($request->division_id)) {
                $query->where('division_id', $request->division_id);
            }
            if (!empty($request->employee_id)) {
                $query->where('employee_id', $request->employee_id);
            }
            if (!empty($request->from_date)) {
                $query->whereDate('created_at', '>=', Carbon::createFromFormat('d-m-Y', $request->from_date)->format('Y-m-d'));
            }
            if (!empty($request->to_date)) {
                $query->whereDate('created_at', '<=', Carbon::createFromFormat('d-m-Y', $request->to_date)->format('Y-m-d'));
            }
            if (!empty($request->status)) {
                $query->where('contract_extensions.status', $request->status);
            }

            $totalFiltered = $query->count();

            $query->select('contract_extensions.*')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir);

            $data = $query->get();

            // * make data table
            $results = array();
            if (!empty($data)) {
                foreach ($data as $key => $row) {
                    $badge = '<div class="badge badge-lg badge-' . contract_extension_status()[$row->status]['color'] . '">
                                    ' . contract_extension_status()[$row->status]['label'] . ' - ' . contract_extension_status()[$row->status]['text'] . '
                                </div>';
                    $nestedData = [
                        'DT_RowIndex' => $key + 1,
                        'code' => '<a href="' . route("admin.$this->view_folder.index") . '/' . $row->id . '" class="text-primary">' . $row->code . '</a>',
                        'division_id' => $row->division?->name,
                        'branch_id' => $row->branch?->name,
                        'employee_id' => $row->employee?->name,
                        'status' => $badge,
                        'created_at' => Carbon::parse($row->created_at)->format('d-m-Y H:i:s'),
                        'action' => '',
                        'export' => Blade::render('components.datatable.export-button', [
                            "route" => route('contract-extension.export', ['id' => encryptId($row->id)]),
                            "onclick" => 'show_print_out_modal(event)',
                        ]),
                    ];
                    $results[] = $nestedData;
                }
            }
            return $this->ResponseJson([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered ?? $totalData),
                "data" => $results,
            ]);
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
        $assesment = $this->assesment;

        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(model::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

        return view("admin.$this->view_folder.create", compact('model'), compact('assesment'));
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
        if ($request->ajax()) {
            $this->validate_api($request->all(), $this->assesment_rules);
            $this->validate_api($request->all(), model::rules());
        } else {
            $this->validate($request, $this->assesment_rules);
            $this->validate($request, model::rules());
        }
        $model = new model();
        $request_all = $request->all();
        $request_all['from_date'] = Carbon::parse($request->from_date);
        $request_all['to_date'] = Carbon::parse($request->to_date);
        $model->loadModel($request_all);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        }

        try {
            $model->save();
            foreach ($request->assesment as $key => $a) {
                $assesment = new AssesmentContractExtension();
                $assesment->contract_extension_id = $model->id;
                $assesment->type = $request->assesment[$key];
                $assesment->value = $request->{$a};
                $assesment->note = $request->note[$key];
                $assesment->save();
            }

            // if (!$model->check_available_date) {
            //     return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
            // }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: 0,
                title: "Pembaharuan Kontrak",
                subtitle: auth()->user()->name . " mengajukan pembaharuan kontrak " . $model->code,
                link: route('admin.contract-extension.show', $model->id),
                update_status_link: route('admin.contract-extension.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route('admin.' . $this->view_folder . '.index')->with($this->ResponseMessageCRUD(true, 'create'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = model::find($id);

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

        // if (!$model->check_available_date) {
        //     return abort(403);
        // }

        if ($model->status == 'approve') {
            return abort(403);
        }

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
        DB::beginTransaction();
        if ($request->ajax()) {
            $this->validate_api($request->all(), $this->assesment_rules);
            $this->validate_api($request->all(), model::rules());
        } else {
            $this->validate($request, $this->assesment_rules);
            $this->validate($request, model::rules());
        }

        $request_all = $request->all();
        $request_all['from_date'] = Carbon::parse($request->from_date);
        $request_all['to_date'] = Carbon::parse($request->to_date);

        $model =  model::find($id);
        $model->loadModel($request_all);
        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, 'Tanggal sudah closing'));
        }
        try {
            $model->save();
            foreach ($request->assesment as $key => $a) {
                $assesment = AssesmentContractExtension::find($request->assesment_id[$key]);
                $assesment->type = $request->assesment[$key];
                $assesment->value = $request->{$a};
                $assesment->note = $request->note[$key];
                $assesment->save();
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: 0,
                title: "Pembaharuan Kontrak",
                subtitle: auth()->user()->name . " mengajukan pembaharuan kontrak " . $model->code,
                link: route('admin.contract-extension.show', $model->id),
                update_status_link: route('admin.contract-extension.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route('admin.' . $this->view_folder . '.index')->with($this->ResponseMessageCRUD(true, 'create'));
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

    public function findById(Request $request)
    {
        $model = model::with([
            'employee',
            'employee.branch',
            'employee.division',
            'employee.position',
        ])->findOrFail($request->id);

        return response()->json($model);
    }

    public function update_status(Request $request, $id)
    {
        $model = model::findOrFail($id);
        validate_branch($model->branch_id);

        DB::beginTransaction();

        // if (!$model->check_available_date) {
        //     return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, 'Tanggal sudah closing'));
        // }

        try {
            if ($request->status == 'approve' && $model->employee->employment_status->name == 'Kontrak') {
                $model->employee->update([
                    'end_contract' => $model->to_date,
                ]);
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message ?? null,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);
                $model->update([
                    'status' => $request->status,
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

            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update status', 'update status'));
    }

    public function export($id, Request $request)
    {
        $model = model::with(['employee', 'division'])->findOrFail(decryptId($id));
        $file = public_path('/pdf_reports/Contract-extension-' . microtime(true) . '.pdf');
        $fileName = 'Contract-extension-' . microtime(true) . '.pdf';

        $pdf = Pdf::loadView("admin/.$this->view_folder./export", compact('model'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');
        $pdf->render();

        return $pdf->stream($fileName);
    }
}
