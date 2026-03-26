<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Authorization;
use App\Models\LaborDemand as model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LaborDemandController extends Controller
{
    use \App\Http\Helpers\ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'labor-demand';

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
            $columns = [
                "code",
                "division_id",
                "branch_id",
                "user_id",
                "status",
                "created_at",
                "action",
            ];

            // * get data with date
            $query = model::with('user', 'branch', 'division');


            if (get_current_branch()->is_primary) {
                if ($request->branch_id) {
                    $query->where('branch_id', $request->branch_id);
                }
            } else {
                $query->where('branch_id', get_current_branch()->id);
            }

            if ($request->from_date) {
                $query->whereDate('labor_demands.created_at', '>=', Carbon::parse($request->from_date));
            } else {
                $query->whereDate('labor_demands.created_at', '>=', Carbon::now()->startOfMonth());
            }

            if ($request->to_date) {
                $query->whereDate('labor_demands.created_at', '<=', Carbon::parse($request->to_date));
            } else {
                $query->whereDate('labor_demands.created_at', '<=', Carbon::now()->endOfMonth());
            }

            // * calculate some data
            $totalData = $query->count();
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
            $search = $request->input('search.value');

            // * search and filter
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('labor_demands.created_at', 'like', "%{$search}%")
                        ->orWhere('labor_demands.code', 'like', "%{$search}%")
                        ->orWhere('labor_demands.status', 'like', "%{$search}%");
                });
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->division_id) {
                $query->where('division_id', $request->division_id);
            }

            $totalFiltered = $query->count();

            $query->select('labor_demands.*',)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir);

            $data = $query->get();

            // * make data table
            $results = array();
            if (!empty($data)) {
                foreach ($data as $key => $labor_demand) {
                    $badge = '<div class="badge badge-lg badge-' . labor_demand_status()[$labor_demand->status]['color'] . '">
                                                ' . labor_demand_status()[$labor_demand->status]['label'] . ' - ' . labor_demand_status()[$labor_demand->status]['text'] . '
                                            </div>';

                    $nestedData['DT_RowIndex'] = $key + 1;
                    $nestedData['code'] = '<a href="' . route("admin.$this->view_folder.index") . '/' . $labor_demand->id . '" class="text-primary">' . $labor_demand->code . '</a>';
                    $nestedData['division_id'] = $labor_demand->division?->name;
                    $nestedData['branch_id'] = $labor_demand->branch?->name;
                    $nestedData['user_id'] = $labor_demand->user?->name . " - " . $labor_demand->user?->email;
                    $nestedData['status'] = $badge;
                    $nestedData['created_at'] = toDayDateTimeString($labor_demand->created_at);

                    $nestedData['action'] = Blade::render('components.datatable.button-datatable', [
                        'row' => $labor_demand,
                        'main' => 'labor-demand',
                        'btn_config' => [
                            'detail' => [
                                'display' => false,
                            ],
                            'edit' => [
                                'display' => $labor_demand->check_available_date ? $labor_demand->status == 'pending' : false,
                            ],
                            'delete' => [
                                'display' => $labor_demand->check_available_date ? $labor_demand->status == 'pending' : false,
                            ],
                        ],
                    ]);

                    $link = route('labor-demand.export.id', ['id' => encryptId($labor_demand->id)]);
                    $nestedData['export'] = '<a href="' . $link . '" class="btn btn-sm btn-flat btn-info" target="_blank" onclick="show_print_out_modal(event)">Export</a>';

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
        $this->validate($request, [
            'branch_id' => 'nullable|exists:branches,id',
            'division_id' => 'required|exists:divisions,id',
            'location' => 'required|string:max:100',

            'position_id.*' => 'required|exists:positions,id',
            'position_name.*' => 'required|string|max:100',
            'education_id.*' => 'required|exists:educations,id',
            'degree_id.*' => 'nullable|exists:degrees,id',
            'gender.*' => 'required|string|max:100',
            'min_age.*' => 'required|numeric',
            'max_age.*' => 'nullable|numeric',
            'quantity.*' => 'required|numeric',
            'long_work_experience.*' => 'nullable|numeric',
            'work_experience.*' => 'nullable|string',
            'skills.*' => 'nullable|string',
            'job_description.*' => 'nullable|string',
            'description.*' => 'nullable|string',
        ]);

        DB::beginTransaction();

        $model = new model();
        $model->fill([
            'branch_id' => $request->branch_id,
            'division_id' => $request->division_id,
            'user_id' => auth()->user()->id,
            'location' => $request->location,
        ]);

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        $data_details = [];
        foreach ($request->position_id as $key => $value) {
            $data_details[] = [
                'labor_demand_id' => $model->id,
                'position_id' => $request->position_id[$key],
                'position_name' => $request->position_name[$key],
                'education_id' => $request->education_id[$key],
                'degree_id' => $request->degree_id[$key] ?? null,
                'gender' => $request->gender[$key],
                'min_age' => $request->min_age[$key],
                'max_age' => $request->max_age[$key] ?? null,
                'quantity' => $request->quantity[$key],
                'long_work_experience' => $request->long_work_experience[$key] ?? null,
                'work_experience' => $request->work_experience[$key] ?? null,
                'skills' => $request->skills[$key] ?? null,
                'job_description' => $request->job_description[$key] ?? null,
                'description' => $request->description[$key] ?? null,
            ];
        }

        try {
            $model->labor_demand_details()->createMany($data_details);

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: 0,
                title: "Permintaan Tenaga Kerja",
                subtitle: Auth::user()->name . " mengajukan Permintaan Tenaga Kerja " . $model->code,
                link: route('admin.labor-demand.show', $model),
                update_status_link: route('admin.labor-demand.update-status', ['id' => $model->id]),
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
        validate_branch($model->branch_id);

        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: model::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );

        $authorization_logs['can_revert'] = in_array($model->status, ['approve', 'partial-approve', 'partial-rejected']);
        $authorization_logs['can_void'] = in_array($model->status, ['approve', 'partial-approve', 'partial-rejected']);
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = $model->check_available_date && in_array($model->status, ['approve', 'partial-approve', 'partial-rejected']);
        $authorization_logs['can_void_request'] = $model->check_available_date && in_array($model->status, ['approve', 'partial-approve', 'partial-rejected']);
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
        $model = model::find($id);

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
            'division_id' => 'required|exists:divisions,id',
            'location' => 'required|string:max:100',

            'position_id.*' => 'required|exists:positions,id',
            'position_name.*' => 'required|string|max:100',
            'education_id.*' => 'required|exists:educations,id',
            'degree_id.*' => 'nullable|exists:degrees,id',
            'gender.*' => 'required|string|max:100',
            'min_age.*' => 'required|numeric',
            'max_age.*' => 'nullable|numeric',
            'quantity.*' => 'required|numeric',
            'long_work_experience.*' => 'nullable|numeric',
            'work_experience.*' => 'nullable|string',
            'skills.*' => 'nullable|string',
            'job_description.*' => 'nullable|string',
            'description.*' => 'nullable|string',
        ]);

        DB::beginTransaction();

        $model = model::findOrFail($id);

        validate_branch($request->branch_id);
        if (!in_array($model->status, ['pending', 'revert'])) {
            return abort(403);
        }

        $model->fill([
            'branch_id' => $request->branch_id,
            'division_id' => $request->division_id,
            'user_id' => auth()->user()->id,
            'location' => $request->location,
        ]);

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        // * delete details old
        try {
            $model->labor_demand_details()->delete();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        $data_details = [];
        foreach ($request->position_id as $key => $value) {
            $data_details[] = [
                'labor_demand_id' => $model->id,
                'position_id' => $request->position_id[$key],
                'position_name' => $request->position_name[$key],
                'education_id' => $request->education_id[$key],
                'degree_id' => $request->degree_id[$key] ?? null,
                'gender' => $request->gender[$key],
                'min_age' => $request->min_age[$key],
                'max_age' => $request->max_age[$key],
                'quantity' => $request->quantity[$key],
                'long_work_experience' => $request->long_work_experience[$key] ?? null,
                'work_experience' => $request->work_experience[$key] ?? null,
                'skills' => $request->skills[$key] ?? null,
                'job_description' => $request->job_description[$key] ?? null,
                'description' => $request->description[$key] ?? null,
            ];
        }

        try {
            $model->labor_demand_details()->createMany($data_details);
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        $authorization->init(
            branch_id: $model->branch_id,
            user_id: auth()->user()->id,
            model: model::class,
            model_id: $model->id,
            amount: 0,
            title: "Permintaan Tenaga Kerja",
            subtitle: Auth::user()->name . " mengajukan Permintaan Tenaga Kerja " . $model->code,
            link: route('admin.labor-demand.show', $model),
            update_status_link: route('admin.labor-demand.update-status', ['id' => $model->id]),
            division_id: auth()->user()->division_id ?? null
        );

        DB::commit();

        return redirect()->route('admin.' . $this->view_folder . '.show', $model)->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = model::findOrFail($id);

        validate_branch($model->branch_id);
        if (!in_array($model->status, ['pending', 'revert'])) {
            return abort(403);
        }

        DB::beginTransaction();

        try {
            $model->delete();

            Authorization::where('model', model::class)->where('model_id', $model->id)->delete();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route('admin.' . $this->view_folder . '.index')->with($this->ResponseMessageCRUD(true, 'delete'));
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
        $model = model::findOrFail($id);
        validate_branch($model->branch_id);

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
                    'status' => $request->status == 'revert' ? 'pending' : $request->status,
                ]);
            } else {
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', null, $request->status);
            }

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update status', 'update status'));
    }

    /**
     * Approve detail status the specified resource from storage.
     *
     * @param  int  $id
     * @param Request $request
     * @param int  $labor_demand_detail_id
     * @return \Illuminate\Http\Response
     */
    public function approve_labor_demand_detail(Request $request, $id, $labor_demand_detail_id)
    {
        $model = model::findOrFail($id);
        $model_detail = $model->labor_demand_details()->findOrFail($labor_demand_detail_id);

        validate_branch($model->branch_id);

        if ($model->id != $model_detail->labor_demand_id) {
            return abort(403);
        }

        DB::beginTransaction();

        $model_detail->status = 'approve';
        try {
            $model_detail->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, $th->getMessage()));
        }

        if ($model->status != 'partial') {
            if ($model->labor_demand_details()->where('status', 'approve')->count() == $model->labor_demand_details()->count()) {
                try {
                    $model->update([
                        'status' => 'approve',
                    ]);
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, $th->getMessage()));
                }
            }

            if (!in_array($model->status, ['partial-approve', 'approve'])) {
                try {
                    $model->update([
                        'status' => 'partial-approve',
                    ]);
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, $th->getMessage()));
                }
            }
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update status', 'update status'));
    }

    /**
     * Reject detail status the specified resource from storage.
     *
     * @param  int  $id
     * @param Request $request
     * @param int  $labor_demand_detail_id
     * @return \Illuminate\Http\Response
     */
    public function reject_labor_demand_detail(Request $request, $id, $labor_demand_detail_id)
    {
        $model = model::findOrFail($id);
        $model_detail = $model->labor_demand_details()->findOrFail($labor_demand_detail_id);

        validate_branch($model->branch_id);

        if ($model->id != $model_detail->labor_demand_id) {
            return abort(403);
        }

        DB::beginTransaction();

        $model_detail->status = 'reject';
        try {
            $model_detail->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, $th->getMessage()));
        }

        if ($model->status != 'partial') {
            if ($model->labor_demand_details()->where('status', 'reject')->count() == $model->labor_demand_details()->count()) {
                try {
                    $model->update([
                        'status' => 'reject',
                    ]);
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, $th->getMessage()));
                }
            }

            if (!in_array($model->status, ['partial-reject', 'reject'])) {
                try {
                    $model->update([
                        'status' => 'partial-reject',
                    ]);
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, $th->getMessage()));
                }
            }
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update status', 'update status'));
    }

    /**
     * Revert detail status the specified resource from storage.
     *
     * @param  int  $id
     * @param Request $request
     * @param int  $labor_demand_detail_id
     * @return \Illuminate\Http\Response
     */
    public function revert_labor_demand_detail(Request $request, $id, $labor_demand_detail_id)
    {
        $model = model::findOrFail($id);
        $model_detail = $model->labor_demand_details()->findOrFail($labor_demand_detail_id);

        validate_branch($model->branch_id);

        if ($model->id != $model_detail->labor_demand_id) {
            return abort(403);
        }

        DB::beginTransaction();

        $model_detail->status = 'pending';
        try {
            $model_detail->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, $th->getMessage()));
        }

        if ($model->status != 'partial') {
            if ($model->labor_demand_details()->where('status', 'pending')->count() == $model->labor_demand_details()->count()) {
                try {
                    $model->update([
                        'status' => 'pending',
                    ]);
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, $th->getMessage()));
                }
            }

            if (!in_array($model->status, ['pending'])) {
                try {
                    $model->update([
                        'status' => 'pending',
                    ]);
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, $th->getMessage()));
                }
            }
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update status', 'update status'));
    }

    /**
     * Close detail status the specified resource from storage.
     *
     * @param Request $request
     * @param  int  $id
     * @param int  $labor_demand_detail_id
     * @return \Illuminate\Http\Response
     */
    public function close_labor_demand_detail(Request $request, $id, $labor_demand_detail_id)
    {
        $model = model::findOrFail($id);
        $model_detail = $model->labor_demand_details()->findOrFail($labor_demand_detail_id);

        validate_branch($model->branch_id);

        DB::beginTransaction();

        $model_detail->status = 'done';
        try {
            $model_detail->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, $th->getMessage()));
        }

        if ($model->status != 'partial') {
            if ($model->labor_demand_details()->where('status', 'done')->count() == $model->labor_demand_details()->count()) {
                try {
                    $model->update([
                        'status' => 'done',
                    ]);
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, $th->getMessage()));
                }
            }

            if (!in_array($model->status, ['partial-done', 'done'])) {
                try {
                    $model->update([
                        'status' => 'partial',
                    ]);
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, $th->getMessage()));
                }
            }
        }



        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update status', 'update status'));
    }

    /**
     * Get labor demand for labor applications.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getLaborDemandForLaborApplication(Request $request)
    {
        $model = model::whereIn('status', ['approve', 'partial', 'partial-approve', 'partial-reject'])
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                $query->where('branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                $query->where('branch_id', get_current_branch()->id);
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where('code', "LIKE", "%$request->search%");
            })
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return $this->ResponseJsonData($model);
    }

    /**
     * Get labor demand details for labor applications.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function getLaborDemandDetailForLaborApplication($id)
    {
        $model = model::findOrFail($id);

        $data_details = $model->labor_demand_details()
            ->whereIn('status', ['approve', 'partial'])
            ->where('quantity', '>', "quantity_complete")
            ->get();

        return $this->ResponseJsonData($data_details);
    }

    public function download()
    {
        return $this->ResponseDownload(public_path('download/5a. FM-HRD-05-01 - Formulir Permintaan Tenaga Kerja.doc'));
    }
    public function export($id, Request $request)
    {
        $model = model::findOrFail(decryptId($id));
        $file = public_path('/pdf_reports/Report-Labor-Demand-' . ucfirst($model->item) . '-' . microtime(true) . '.pdf');
        $fileName = 'Report-Labor-Demand-' . ucfirst($model->item) . '-' . microtime(true) . '.pdf';

        $qr_url = route('labor-demand.export.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));

        // return view('admin.labor-demand.export', compact('model', 'qr'));

        $pdf = PDF::loadview("admin/.$this->view_folder./export", compact('model', 'qr'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');

        return $pdf->stream($fileName);
    }
}
