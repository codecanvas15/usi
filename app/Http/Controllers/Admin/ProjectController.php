<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Authorization;
use App\Models\Branch;
use App\Models\Project as model;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProjectController extends Controller
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
    protected string $view_folder = 'project';

    /**
     * search  value
     *
     * @var string|null
     */
    protected string|null $search = null;

    /**
     *
     * approval_permission
     * @var array
     */
    private $approval_permission = [
        'approve' => 'approve project',
        'inactive' => 'inactive project',
        'close' => 'close project',
        'reopen' => 'reopen project',
    ];

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = model::orderByDesc('created_at');

            if (!get_current_branch()->is_primary) {
                $data->where('branch_id', get_current_branch_id());
            }

            if ($request->branch_id) {
                $data->where('branch_id', $request->branch_id);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('status', function ($model) {
                    $badge = '<div class="badge badge-lg badge-' . project_status()[$model->status]['color'] . '">
                                            ' . project_status()[$model->status]['label'] . ' - ' . project_status()[$model->status]['text'] . '
                                        </div>';
                    return $badge;
                })
                ->rawColumns(['status'])
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
        $model = [];
        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(model::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

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

        $branch_id = get_current_branch()->is_primary ? $request->branch_id : get_current_branch_id();
        $branch = Branch::findOrFail($branch_id);
        $last_data = model::withTrashed()
            ->where('branch_id', $branch_id)
            ->whereMonth('created_at', date('m'))
            ->orderBy('id', 'desc')
            ->first();

        // * create data
        $model = new model();

        if ($last_data) {
            $model->code = generate_code_transaction("PRJ", $last_data->code, branch_sort: $branch->sort);
        } else {
            $model->code = generate_code_transaction("PRJ", "0000-0000-0000-0000", branch_sort: $branch->sort);
        }
        $model->loadModel(array_merge($request->all(), [
            'branch_id' => $branch_id,
        ]));
        if ($request->hasFile('file')) {
            $model->file = $this->upload_file($request->file('file'), 'project');
        }

        // * saving and make response
        try {
            $model->date = Carbon::parse($request->date);
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        $authorization->init(
            branch_id: $model->branch_id,
            user_id: auth()->user()->id,
            model: Project::class,
            model_id: $model->id,
            amount: 0,
            title: "Project " . $model->name,
            subtitle: Auth::user()->name . " mengajukan Project " . $model->name,
            link: route('admin.' . $this->view_folder . '.show', $model),
            update_status_link: route('admin.' . $this->view_folder . '.update-status', ['id' => $model->id]),
            division_id: auth()->user()->division_id ?? null
        );

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

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: model::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );

        $authorization_logs['can_revert'] = $model->status == 'active';
        $authorization_logs['can_void'] = $model->status == 'active';
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] =  $model->status == 'active';
        $authorization_logs['can_void_request'] = $model->status == 'active';
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'auth_revert_void_button'));
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

        if (in_array($model->status, ['pending', 'inactive', 'revert'])) {
            if ($request->ajax()) {
                return $this->ResponseJsonData($model);
            }

            return view("admin.$this->view_folder.edit", compact('model'));
        }

        throw new \Exception("Can't edit project with {$model->status}", 1);
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
        $old_file = $model->file;

        if (in_array($model->status, ['pending', 'inactive', 'revert'])) {
            DB::beginTransaction();
            // * validate
            if ($request->ajax()) {
                $this->validate($request, model::rules());
            } else {
                $this->validate_api($request->all(), model::rules());
            }

            // * update data
            $model->loadModel(array_merge($request->all(), [
                'branch_id' => get_current_branch()->is_primary ? $request->branch_id : get_current_branch_id(),
            ]));

            if ($request->hasFile('file')) {
                $this->delete_file($old_file ?? '', 'project');
                $model->file = $this->upload_file($request->file('file'), 'project');
            }

            // * saving and make reponse
            try {
                $model->date = Carbon::parse($request->date);
                $model->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
            }


            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: Project::class,
                model_id: $model->id,
                amount: 0,
                title: "Project " . $model->name,
                subtitle: Auth::user()->name . " mengajukan Project " . $model->name,
                link: route('admin.' . $this->view_folder . '.show', $model),
                update_status_link: route('admin.' . $this->view_folder . '.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );

            DB::commit();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(true, 'edit');
            }

            return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'edit'));
        }

        throw new \Exception("Can't edit project with {$model->status}", 1);
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

        if (in_array($model->status, ['pending', 'inactive'])) {
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

            Authorization::where('model', model::class)
                ->where('model_id', $model->id)
                ->delete();
            DB::commit();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'delete');
            }

            return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'delete'));
        }

        throw new \Exception("Can't delete project with status {$model->status}", 1);
    }

    /**
     * update_status
     *
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @return mixed
     */
    public function update_status(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
        ]);

        $status_bind = [
            'approve' => 'active',
            'reject' => 'reject',
            'activate' => 'active',
            'deactivate' => 'inactive',
            'cancel' => 'cancel',
            'close' => 'done',
            'revert' => 'revert',
            'void' => 'void',
        ];

        if (!array_key_exists($request->status, $status_bind)) {
            throw new \Exception("Invalid Status");
        }

        $status = $status_bind[$request->status];
        $model = model::findOrFail($id);

        DB::beginTransaction();
        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                // * create status log
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);

                $model->loadModel([
                    'status' => $status,
                ]);

                $model->save();
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

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update_status', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, 'update'));
    }

    /**
     * select 2 form search
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function select(Request $request): \Illuminate\Http\JsonResponse
    {
        $branch_id = $request->branch_id;
        $model = model::where(function ($query) use ($request) {
            $query->where('name', 'like', "%$request->search%");
            $query->orWhere('code', 'like', "%$request->search%");
        })
            ->when(!get_current_branch()->is_primary && $branch_id, function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            });

        $model = $model->where('status', 'active')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return $this->ResponseJsonData($model);
    }

    /**
     * get detail
     *
     * @param int|null $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id = null)
    {
        $model = model::findOrFail($id);
        return $this->ResponseJsonData($model);
    }
}
