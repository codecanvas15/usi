<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\WareHouse;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use App\Models\PurchaseRequestDetail;
use App\Models\PurchaseRequest as model;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Http\Helpers\PrintHelper;
use App\Models\AccountPayable;
use App\Models\Authorization;
use App\Models\AuthorizationDetail;
use App\Models\Branch;
use App\Models\LockStock;
use App\Models\PurchaseOrderGeneral;
use App\Models\PurchaseOrderService;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PurchaseRequestController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder-general|view $this->view_folder-service|view $this->view_folder-trading", ['only' => ['index', 'show']]);
        $this->middleware("permission:create $this->view_folder-general|create $this->view_folder-service", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit $this->view_folder-general|edit $this->view_folder-service", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete $this->view_folder-general|delete $this->view_folder-service", ['only' => ['destroy']]);
    }

    /**
     * middleware list for this controller
     *
     * @var array middleware
     */
    private array $middleware_list = [
        'purchase-request' => ['view', 'create', 'edit', 'delete', 'approve', 'revert', 'void', 'cancel', 'reject', 'close'],
        'purchase-request-service' => ['view', 'create', 'edit', 'delete', 'approve', 'revert', 'void', 'cancel', 'reject', 'close'],
        'purchase-request-general' => ['view', 'create', 'edit', 'delete', 'approve', 'revert', 'void', 'cancel', 'reject', 'close'],
    ];

    /**
     * check middelware function manual
     *
     * @param string $type
     * @param string $action
     */
    private function check_middleware($type, $action)
    {
        // * conver model type to middleware list keys
        $type_convert = [
            'general' => 'purchase-request-general',
            'jasa' => 'purchase-request-service',
        ];

        // * check if valid data and type
        if (array_key_exists($type, $type_convert)) {
            if (array_key_exists($type_convert[$type], $this->middleware_list)) {
                if (in_array($action, $this->middleware_list[$type_convert[$type]])) {
                    if (Auth::user()->can("{$action} {$type_convert[$type]}")) {
                        return true;
                    }
                }
            }
        }

        return abort(403);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'purchase-request';

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
        return view('admin.' . $this->view_folder . '.index');
    }

    /**
     * get_data_type
     *
     * @param Request  $request
     * @param string $type
     * @return mixed
     */
    public function data(Request $request, $type)
    {
        $columns = [
            'kode',
            'status',
            'tanggal',
            'created_at',
            'updated_at',
        ];

        $checkAuthorizePrint = authorizePrint('purchase_request_' . $type);

        if ($type == 'jasa') {
            $type = 'service';
        }

        if (Auth::user()->hasPermissionTo('view purchase-request-' . $type)) {
            if ($type == 'service') {
                $type = 'jasa';
            }

            $search = $request->input('search.value');

            $query = model::when(get_current_branch()->is_primary && $request->branch_id, fn($q) => $q->where('purchase_requests.branch_id', $request->branch_id))
                ->when(!get_current_branch()->is_primary, fn($q) => $q->where('purchase_requests.branch_id', get_current_branch_id()))
                ->where('type', $type)
                ->with(['created_by_user'])
                ->with('project')
                ->when($request->from_date, fn($q) => $q->whereDate('purchase_requests.tanggal', '>=', Carbon::parse($request->from_date)))
                ->when($request->to_date, fn($q) => $q->whereDate('purchase_requests.tanggal', '<=', Carbon::parse($request->to_date)))
                ->when($request->project_id, fn($q) => $q->whereHas('project', fn($q) => $q->where('id', $request->project_id)))
                ->when(Auth::user()->hasPermissionTo('view-all purchase-request-' . ($type == 'jasa' ? 'service' : $type)) && $request->division_id, fn($q) => $q->where('division_id', $request->division_id))
                ->when(!Auth::user()->hasPermissionTo('view-all purchase-request-' . ($type == 'jasa' ? 'service' : $type)), fn($q) => $q->where('division_id', Auth::user()->division_id))
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('purchase_requests.kode', 'like', "%{$search}%")
                            ->orWhere('purchase_requests.kode', 'like', "%{$search}%")
                            ->orWhere('purchase_requests.tanggal', 'like', "%{$search}%")
                            ->orWhere('purchase_requests.status', 'like', "%{$search}%");
                    });
                })
                ->when(Auth::user()->hasPermissionTo('cant-see-other purchase-request') && $request->type != 'jasa', function ($q) {
                    $q->where('purchase_requests.created_by', Auth::user()->id);
                })
                ->when(Auth::user()->hasPermissionTo('cant-see-other purchase-request-service') && $request->type == 'jasa', function ($q) {
                    $q->where('purchase_requests.created_by', Auth::user()->id);
                })
                ->when($request->status, fn($q) => $q->where('purchase_requests.status', $request->status))
                ->select('*');

            // * calculate some data
            $totalData = $query->count();
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $totalFiltered = $query->count();

            $query->select('purchase_requests.*',)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir);

            $data = $query->get();

            // * make data table
            $results = array();
            if (!empty($data)) {
                foreach ($data as $key => $purchase_order) {
                    $badge = '<div class="badge badge-lg badge-' . purchase_request_status()[$purchase_order->status]['color'] . '">
                                        ' . purchase_request_status()[$purchase_order->status]['label'] . ' - ' . purchase_request_status()[$purchase_order->status]['text'] . '
                                    </div>';

                    $nestedData['DT_RowIndex'] = $key + 1;
                    $nestedData['id'] = $purchase_order->id;
                    $link = route('purchase-request.export.id', ['id' => encryptId($purchase_order->id)]);
                    $link_detail = route("admin.$this->view_folder.index") . '/' . $purchase_order->id;
                    $nestedData['kode'] = '<a href="' . route("admin.$this->view_folder.index") . '/' . $purchase_order->id . '" class="text-primary">' . $purchase_order->kode . '</a>'
                        . '<br><a href="' .  $link . '" class="btn btn-sm btn-info" target="_blank" onclick="show_print_out_modal(event)" ' . ($checkAuthorizePrint ? 'data-model="' . model::class . '" data-id="' . $purchase_order->id . '" data-print-type="purchase_request_' . $purchase_order->type . '" data-link="' . $link_detail . '" data-code="' . $purchase_order->kode . '"' : '') . '>Export</a>';
                    $nestedData['tanggal'] = localDate($purchase_order->tanggal);
                    $nestedData['status'] = $badge;
                    $nestedData['created_at'] = toDayDateTimeString($purchase_order->created_at);
                    $nestedData['user'] = Str::headline($purchase_order->created_by_user?->name);
                    $nestedData['project'] = $purchase_order->project?->name ?? '—';

                    $nestedData['button'] = Blade::render('admin.purchase-request.data-table.modal-btn', [
                        'data' => $purchase_order,
                    ]);

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
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(model::class, $request->type == 'service' ? 'jasa' : $request->type)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }
        $model = [];

        if (!in_array($request->type, ['service', 'general'])) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Tipe invalid"));
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

        $this->validate($request, array_merge(model::rules(), [
            'item_id.*' => 'required|exists:items,id',
            'jumlah.*' => 'required|min:1',
            'tanggal' => 'nullable|date',
            'keterangan' => 'nullable|string|max:16777215',
        ]));

        if (is_array($request->jumlah)) {
            foreach ($request->jumlah as $jumlah) {
                if ($jumlah == 0) {
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Jumlah tidak boleh 0'));
                } elseif ($jumlah == "NaN") {
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Jumlah tidak boleh string'));
                }
            }
        }

        $last_purchase_request = model::
            // ->where('type', $model->type)
            whereMonth('tanggal', Carbon::parse($request->tanggal))
            ->whereYear('tanggal', Carbon::parse($request->tanggal))
            ->orderBy('id', 'desc')
            ->withTrashed()
            ->first();

        // * create data
        $model = new model();

        if ($last_purchase_request) {
            $model->kode = generate_code_purchase_request($last_purchase_request->kode, year: $request->tanggal);
        } else {
            $model->kode = generate_code_purchase_request("0000/0000/00/0000", year: $request->tanggal);
        }
        $model->loadModel([
            'project_id' => $request->project_id ?? null,
            'tanggal' => Carbon::parse($request->tanggal),
            'type' => $request->type,
            'keterangan' => $request->keterangan,
            'division_id' => $request->division_id,
        ]);

        // * saving
        try {
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: 0,
                title: "Purchase Request",
                subtitle: Auth::user()->name . " mengajukan purchase request " . $model->kode,
                link: route('admin.purchase-request.show', $model),
                update_status_link: route('admin.purchase-request.update-status', ['id' => $model->id]),
                type: $model->type,
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        // * create purchase request item
        if (is_array($request->item_id)) {
            foreach ($request->item_id as $key => $value) {
                $item = \App\Models\Item::find($value);
                $purchase_request_detail = new PurchaseRequestDetail();
                $purchase_request_detail->loadModel([
                    'purchase_request_id' => $model->id,
                    'item_id' => $value,
                    'jumlah' => thousand_to_float($request->jumlah[$key]),
                    'unit_id' => $item->unit_id,
                    'keterangan' => $request->keterangan_item[$key] ?? null,
                ]);

                if (isset($request->file('file')[$key])) {
                    $purchase_request_detail->file = $this->upload_file($request->file('file')[$key], 'purchae-request-detail');
                }

                try {
                    $purchase_request_detail->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return $this->ResponseJsonMessageCRUD(false, 'create', 'create purchase order item', $th->getMessage(), 422);
                    }

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'create purchase order item', $th->getMessage()));
                }
            }
        } else {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'masukkan paling tidak satu item'));
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
        $model = model::with(['purchase_request_details'])->findOrFail($id);

        $check_permission = Auth::user()->hasPermissionTo('cant-see-other purchase-request') && $model->type == 'general';
        $check_permission_service = Auth::user()->hasPermissionTo('cant-see-other purchase-request-service') && $model->type == 'jasa';
        if ($check_permission || $check_permission_service) {
            if (Auth::user()->id != $model->created_by) {
                return abort(403);
            }
        }

        $this->check_middleware($model->type, 'view');
        validate_branch($model->branch_id);

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
        $authorization_logs['can_revert'] = $model->check_available_date && in_array($model->status, ['approve', 'partial-approve', 'partial-rejected']);
        $authorization_logs['can_void'] = $model->check_available_date && in_array($model->status, ['approve', 'partial-approve', 'partial-rejected']);
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = $model->check_available_date && in_array($model->status, ['approve', 'partial-approve', 'partial-rejected']);
        $authorization_logs['can_void_request'] = $model->check_available_date && in_array($model->status, ['approve', 'partial-approve', 'partial-rejected']);

        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        $can_approve = AuthorizationDetail::whereHas('authorization', function ($q) use ($model) {
            $q->where('model', model::class)
                ->where('model_id', $model->id);
        })
            ->where('user_id', Auth::user()->id)
            ->exists();

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'can_approve', 'auth_revert_void_button'));
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

        $check_permission = Auth::user()->hasPermissionTo('cant-see-other purchase-request') && $model->type == 'general';
        $check_permission_service = Auth::user()->hasPermissionTo('cant-see-other purchase-request-service') && $model->type == 'jasa';
        if ($check_permission || $check_permission_service) {
            if (Auth::user()->id != $model->created_by) {
                return abort(403);
            }
        }

        validate_branch($model->branch_id);
        $this->check_middleware($model->type, 'edit');

        if (!in_array($model->status, ['pending', 'revert'])) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, 'Tidak dapat mengubah data'));
        }

        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        if ($model->branch_id != get_current_branch_id()) {
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
        DB::beginTransaction();

        $model = model::findOrFail($id);

        // * validate
        validate_branch($model->branch_id);
        $this->check_middleware($model->type, 'edit');


        $this->validate($request, array_merge(model::rules(), [
            // 'item.*' => 'required|string|max:255',
            'jumlah.*' => 'required|min:1',
            // 'type' => 'required',
            'tanggal' => 'nullable|date',
            'keterangan' => 'nullable|string|max:16777215',
            // 'unit_id' => 'required|exists:units,id',
            // 'file' => 'nullable|mimes:jpg,jpeg,png,pdf,docx|max:6144',
        ]));

        // count revision
        $explode_code = explode("/", $model->kode);
        if (array_key_exists(4, $explode_code)) {
            $explode_code = (int) ltrim($explode_code[4], "R");
            if ($explode_code >= 3) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, 'Tidak dapat mengubah data'));
            }
        }


        if (is_array($request->jumlah)) {
            foreach ($request->jumlah as $jumlah) {
                if ($jumlah == 0) {
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Jumlah tidak boleh 0'));
                } elseif ($jumlah == "NaN") {
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Jumlah tidak boleh string'));
                }
            }
        }

        // * saving and make response
        $model->loadModel([
            'project_id' => $request->project_id ?? null,
            'kode' => $model->status == "revert" ? generate_code_purchase_request_update($model->kode) : $model->kode,
            'tanggal' => Carbon::parse($request->tanggal),
            'keterangan' => $request->keterangan,
            'division_id' => $request->division_id,
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
                title: "Purchase Request",
                subtitle: Auth::user()->name . " mengajukan purchase request " . $model->kode,
                link: route('admin.purchase-request.show', $model),
                update_status_link: route('admin.purchase-request.update-status', ['id' => $model->id]),
                type: $model->type,
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        // ! delete purchase request items if not in updated
        $purchase_request_deleted_list = $model->purchase_request_details->whereNotIn('id', $request->purchase_request_detail_id);
        foreach ($purchase_request_deleted_list as $key => $value) {
            try {
                try {
                    $this->delete_file($value->file ?? '');
                } catch (\Throwable $th) {
                    //throw $th;
                }

                $value->delete();
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'edit', 'delete purchase request item', $th->getMessage(), 422);
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', 'delete purchase request item', $th->getMessage()));
            }
        }

        // * create purchase request item
        if (is_array($request->item_id)) {
            foreach ($request->item_id as $key => $value) {
                $item = \App\Models\Item::findOrFail($value);
                if (isset($request->purchase_request_detail_id[$key])) {
                    $purchase_request_detail = PurchaseRequestDetail::findOrFail($request->purchase_request_detail_id[$key]);

                    if ($model->id == $purchase_request_detail->purchase_request_id) {
                        $old_file = $purchase_request_detail->file;
                        $purchase_request_detail->loadModel([
                            'purchase_request_id' => $model->id,
                            'item_id' => $value,
                            'jumlah' => thousand_to_float($request->jumlah[$key]),
                            'unit_id' => $item->unit_id,
                            'keterangan' => $request->keterangan_item[$key] ?? null,
                        ]);

                        if (isset($request->file('file')[$key])) {
                            try {
                                $this->delete_file($old_file);
                            } catch (\Throwable $th) {
                                //throw $th;
                            }

                            $purchase_request_detail->file = $this->upload_file($request->file('file')[$key], 'purchae-request-detail');
                        }

                        try {
                            $purchase_request_detail->save();
                        } catch (\Throwable $th) {
                            DB::rollBack();
                            if ($request->ajax()) {
                                return $this->ResponseJsonMessageCRUD(false, 'edit', 'edit purchase request item', $th->getMessage(), 422);
                            }

                            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', 'edit purchase request item', $th->getMessage()));
                        }
                    }
                } else {
                    $purchase_request_detail = new PurchaseRequestDetail();
                    $purchase_request_detail->loadModel([
                        'purchase_request_id' => $model->id,
                        'item_id' => $value,
                        'jumlah' => thousand_to_float($request->jumlah[$key]),
                        'unit_id' => $request->unit_id[$key] ?? $item->unit_id,
                        'keterangan' => $request->keterangan_item[$key] ?? null,
                    ]);

                    $purchase_request_detail->file = $this->upload_file($request->file('file')[$key], 'purchase-request-detail');

                    try {
                        $purchase_request_detail->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        if ($request->ajax()) {
                            return $this->ResponseJsonMessageCRUD(false, 'create', 'create purchase order item', $th->getMessage(), 422);
                        }

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'create purchase order item', $th->getMessage()));
                    }
                }
            }
        } else {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', 'purchase request item', 'masukkan paling tidak satu item'));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, 'edit'));
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

        validate_branch($model->branch_id);

        $this->check_middleware($model->type, 'delete');
        DB::beginTransaction();
        try {
            $model->delete();

            Authorization::where('model', model::class)
                ->where('model_id', $model->id)
                ->delete();
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
     * Lock stock
     *
     * @param Request $request
     * @param int $id purchase id
     */
    public function lock_stock(Request $request, $id)
    {
        $parent = model::findOrFail($id);
        $model = PurchaseRequestDetail::findOrFail($request->purchase_request_detail_id);

        validate_branch($parent->branch_id);

        DB::beginTransaction();

        $lock_model = \App\Models\LockStock::where('purchase_request_detail_id', $model->id)->first();
        if (!$lock_model) {
            $lock_model = new \App\Models\LockStock();
        }

        $lock_model->fill([
            'purchase_request_detail_id' => $model->id,
            'item_id' => $model->item_id,
            'quantity' => thousand_to_float($request->quantity),
            'status' => $model->status
        ]);

        try {
            $lock_model->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'create lock stock', $th->getMessage()));
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'create', 'create lock stock'));
    }

    /**
     * Unlock stock
     *
     * @param Request $request
     * @param int $id purchase id
     * @return \Illuminate\Http\Response
     */
    public function unlock_stock(Request $request, $id)
    {
        $model = PurchaseRequestDetail::findOrFail($id);

        DB::beginTransaction();

        $lock_model = \App\Models\LockStock::where('purchase_request_detail_id', $model->id)->first();
        if ($lock_model) {
            try {
                $lock_model->delete();
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', 'delete lock stock', $th->getMessage()));
            }
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'delete', 'delete lock stock'));
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request, $type = null)
    {
        $model = model::where(function ($query) {
            $query->orWhere('status', '=', 'approve');
            $query->orWhere('status', '=', 'partial');
            $query->orWhere('status', '=', 'partial-rejected');
            $query->orWhere('status', '=', 'partial-approve');
        })
            ->when($request->selected_id, function ($query) use ($request) {
                $selected_id = explode(',', $request->selected_id);
                $query->whereNotIn('id', $selected_id);
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', '=', $type);
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where('kode', 'like', "%$request->search%");
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                $query->where('branch_id', get_current_branch_id());
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                $query->where('branch_id', $request->branch_id);
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return $this->ResponseJson($model);
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select_global(Request $request)
    {
        $model = model::when($request->search, function ($query) use ($request) {
            $query->where('kode', 'like', "%$request->search%");
        })
            ->when($request->must_filter_project, function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('project_id', $request->project_id)
                        ->whereNotNull('project_id');
                });
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                $query->where('branch_id', get_current_branch_id());
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                $query->where('branch_id', $request->branch_id);
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('kode', 'like', "%$request->search%")
                        ->orWhere('keterangan', 'like', "%$request->search%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return $this->ResponseJsonData($model);
    }

    /**
     * update status (approve, reject, cancel)
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, $id)
    {
        $model = model::findOrFail($id);
        if (!get_current_branch()->is_primary) {
            if ($model->branch_id != get_current_branch_id()) {
                return abort(403);
            }
        }

        DB::beginTransaction();
        try {
            if (is_array($request->jumlah_diapprove)) {
                foreach ($request->jumlah_diapprove as $key => $value) {
                    $purchase_request_detail = PurchaseRequestDetail::findOrFail($request->purchase_request_detail[$key]);
                    $purchase_request_detail->jumlah_diapprove = thousand_to_float($value ?? $purchase_request_detail->jumlah);
                    $purchase_request_detail->approve_desc = $request->approve_desc[$key] ?? null;

                    try {
                        $purchase_request_detail->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        if ($request->ajax()) {
                            return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
                        }

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
                    }
                }
            } else {
                foreach ($model->purchase_request_details as $key => $purchase_request_detail) {
                    $purchase_request_detail->jumlah_diapprove = $purchase_request_detail->jumlah;
                    $purchase_request_detail->save();
                }
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(model::class, $id, $request->close_notes ?? $request->message ?? 'message not available', $model->status, $request->status);
                // * saving and make response
                $model->status = $request->status;
                if ($request->status == 'reject') {
                    $model->keterangan .= $request->message;
                } elseif ($request->status == 'approve') {
                    $model->approved_by = Auth::user()->id;
                }

                if ($request->close_notes) {
                    $model->close_notes = trim($request->close_notes);
                }

                $model->save();
            } else {
                $this->create_activity_status_log(model::class, $id, $request->close_notes ?? $request->message ?? 'message not available', null, $request->status);
            }

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );
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

    /**
     * approve purchase request detail
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve_purchase_request_detail(Request $request, $id, $purchase_request_detail_id)
    {
        DB::beginTransaction();

        $model = model::findOrFail($id);
        $purchase_request_detail = PurchaseRequestDetail::findOrFail($purchase_request_detail_id);

        if ($model->branch_id != get_current_branch_id()) {
            return abort(403);
        }

        $this->check_middleware($model->type, 'approve');

        $purchase_request_detail->status = 'approve';
        $purchase_request_detail->jumlah_diapprove = thousand_to_float($request->jumlah_diapprove ?? 0);
        $purchase_request_detail->approve_desc = $request->approve_desc ?? null;
        try {
            $purchase_request_detail->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        // * check if this status not partial
        if ($model->status != 'partial') {
            // * check if this status not partial-approve
            if ($model->status != 'partial-approve') {
                $model->status = 'partial-approve';
                try {
                    $model->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
                    }

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
                }
            }

            // * check if all purchase request detail is approve update purchase request status to approve
            if ($model->purchase_request_details->where('status', 'approve')->count() == count($model->purchase_request_details)) {
                if ($model->status != 'approve') {
                    $model->status = 'approve';
                    try {
                        $model->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        if ($request->ajax()) {
                            return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
                        }

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
                    }
                }
            }
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, 'edit'));
    }


    /**
     * reject_purchase_request_detail
     *
     * @param Request $request
     * @param int $id
     * @param int $purchase_request_detail_id
     * @return \Illuminate\Http\Response
     */
    public function reject_purchase_request_detail(Request $request, $id, $purchase_request_detail_id)
    {
        DB::beginTransaction();

        $model = model::findOrFail($id);
        $purchase_request_detail = PurchaseRequestDetail::findOrFail($purchase_request_detail_id);

        if ($model->branch_id != get_current_branch_id()) {
            return abort(403);
        }

        $this->check_middleware($model->type, 'reject');

        $purchase_request_detail->status = 'reject';
        $purchase_request_detail->reject_reason = $request->reject_reason;
        try {
            $purchase_request_detail->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        // * check if this status not partial
        if ($model->status != 'partial') {
            // * check if this status not partial-rejected
            if ($model->status != 'partial-rejected') {
                $model->status = 'partial-rejected';
                try {
                    $model->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
                    }

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
                }
            }

            // * check if all purchase request detail is reject update purchase request status to reject
            if ($model->purchase_request_details->where('status', 'reject')->count() == count($model->purchase_request_details)) {
                if ($model->status != 'reject') {
                    $model->status = 'reject';
                    try {
                        $model->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        if ($request->ajax()) {
                            return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
                        }

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
                    }
                }
            }
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    /**
     * revert_purchase_request_detail
     *
     * @param Request $request
     * @param int $id
     * @param int $purchase_request_detail_id
     * @return \Illuminate\Http\Response
     */
    public function revert_purchase_request_detail(Request $request, $id, $purchase_request_detail_id)
    {
        DB::beginTransaction();

        $model = model::findOrFail($id);
        $purchase_request_detail = PurchaseRequestDetail::findOrFail($purchase_request_detail_id);

        if ($model->branch_id != get_current_branch_id()) {
            return abort(403);
        }

        $this->check_middleware($model->type, 'revert');

        $purchase_request_detail->status = 'pending';
        $purchase_request_detail->reject_reason = null;
        try {
            $purchase_request_detail->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        // * check if this status not partial
        if ($model->status != 'partial') {
            // * if all purchase request detail is pending update purchase request status to pending
            if ($model->purchase_request_details()->where('status', 'pending')->count() == $model->purchase_request_details()->count()) {
                if ($model->status != 'pending') {
                    $model->status = 'pending';
                    try {
                        $model->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        if ($request->ajax()) {
                            return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
                        }

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
                    }
                }
            } else {
                if (!in_array($model->status, ['partial-approve', 'partial-rejected'])) {
                    $model->status = 'partial-approve';
                    try {
                        $model->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        if ($request->ajax()) {
                            return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
                        }
                    }
                }
            }
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    /**
     * get detail purchase request
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_detail_purchase_request($id)
    {
        $model = model::with([
            'created_by_user',
            'division'
        ])->findOrFail($id);

        validate_branch($model->branch_id);


        if ($model->type == 'general') {
            $purchase_request_details = $model->purchase_request_details()
                ->whereIn('purchase_request_details.status', ['approve', 'partial'])
                ->with(['unit', 'item_data.unit'])
                ->get();

            $purchase_order_detail_items = \App\Models\PurchaseOrderGeneralDetailItem::whereHas('purchase_order_general_detail', function ($p) {
                $p->whereHas('purchase_order_general', function ($q) {
                    $q->whereNull('deleted_at');
                });
            })
                ->whereIn('purchase_request_detail_id', $purchase_request_details->pluck('id')->toArray())
                ->whereNotIn('status', ['reject', 'void'])
                ->get();

            $lock_stock = \App\Models\LockStock::whereIn('purchase_request_detail_id', $purchase_request_details->pluck('id')->toArray())
                ->where('status', 'approve')
                ->get();

            $purchase_request_details = $purchase_request_details->map(function ($item) use ($purchase_order_detail_items, $lock_stock) {
                $item->qty_po = $purchase_order_detail_items->where('purchase_request_detail_id', $item->id)->sum('quantity') ?? 0;
                $item->qty_lock = $lock_stock->where('purchase_request_detail_id', $item->id)->sum('quantity') - $lock_stock->where('purchase_request_detail_id', $item->id)->sum('quantity_complete') ?? 0;

                return $item;
            });
        } elseif ($model->type == 'jasa') {
            $purchase_request_details = $model->purchase_request_details()
                ->whereIn('purchase_request_details.status', ['approve', 'partial'])
                ->with(['unit', 'item_data.unit'])
                ->get();

            $purchase_order_detail_items = \App\Models\PurchaseOrderServiceDetailItem::whereIn('purchase_request_detail_id', $purchase_request_details->pluck('id')->toArray())
                ->whereHas('purchase_order_service_detail', function ($q) {
                    $q->whereHas('purchase_order_service', function ($q) {
                        $q->whereNull('deleted_at');
                    });
                })
                ->whereNotIn('status', ['reject', 'void'])
                ->get();

            $lock_stock = \App\Models\LockStock::whereIn('purchase_request_detail_id', $purchase_request_details->pluck('id')->toArray())
                ->where('status', 'approve')
                ->get();

            $purchase_request_details = $purchase_request_details->map(function ($item) use ($purchase_order_detail_items, $lock_stock) {
                $item->qty_po = $purchase_order_detail_items->where('purchase_request_detail_id', $item->id)->sum('quantity') ?? 0;
                $item->qty_lock = $lock_stock->where('purchase_request_detail_id', $item->id)->sum('quantity') - $lock_stock->where('purchase_request_detail_id', $item->id)->sum('quantity_complete') ?? 0;

                return $item;
            });
        }

        return $this->ResponseJsonData(compact('model', 'purchase_request_details'));
    }

    /**
     * sale_order export
     *
     * @return \Illuminate\Http\Response
     */
    public function export($id, Request $request)
    {
        $model = model::with(['purchase_request_details', 'created_by_user', 'approved_by_user'])->findOrFail(decryptId($id));

        if (!$request->preview && authorizePrint('purchase_request_' . $model->type)) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                model::class,
                decryptId($id),
                'purchase_request_' . $model->type,
            );

            if (!$result) {
                return abort(403);
            }
        }

        $file = public_path('/pdf_reports/Report-Purchase-Request-' . $model->kode . '.pdf');
        $fileName = 'Report-Purchase-Request-' . $model->kode . '.pdf';

        $qr_url = route('purchase-request.export.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $approval = Authorization::where('model', model::class)
            ->with(['details' => function ($q) {
                $q->where('status', 'approve')
                    ->where('note', 'not like', '%otomatis%');
            }])
            ->where('model_id', $model->id)
            ->first();

        $pdf = PDF::loadview("admin/.$this->view_folder./export", compact('model', 'qr', 'approval'))
            ->setPaper($request->paper ?? 'a4',  $request->orientation ?? 'portrait');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $h = $canvas->get_height();
        $w = $canvas->get_width();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM} / {PAGE_COUNT}", "sans-serif", 8, array(0, 0, 0));

        if ($request->preview) {
            Storage::disk('public')->deleteDirectory('tmp_purchase_request_' . $model->type);
            $tmp_file_name = 'purchase_request_' . $model->type . '_' . time() . '.pdf';
            $path = 'tmp_purchase_request_' . $model->type . '/' . $tmp_file_name;
            Storage::disk('public')->put($path, $pdf->output());

            return response()->json($path);
        }

        return $pdf->stream($fileName);
    }

    public function getWarehouse(Request $request, $item_id)
    {
        if ($request->ajax()) {
            $warehouse = WareHouse::selectRaw('ware_houses.*,sum(COALESCE(sm.in,0)-COALESCE(sm.out,0)) as stock')
                ->leftJoin('stock_mutations as sm', function ($j) use ($item_id) {
                    $j->on('sm.ware_house_id', '=', 'ware_houses.id');
                    $j->where('sm.item_id', '=', $item_id);
                    $j->whereNull('sm.deleted_at');
                })
                ->havingRaw('stock > 0')
                ->groupBy('ware_houses.id')
                ->get();

            $lock_stock = LockStock::where('item_id', $item_id)
                ->whereIn('status', ['approve', 'partial'])
                ->selectRaw('sum(COALESCE(lock_stocks.quantity,0)-COALESCE(lock_stocks.quantity_complete,0)) as stock')
                ->get()
                ->sum('stock');

            return $this->ResponseJsonData(compact('warehouse', 'lock_stock'));
        }
        return abort(403);
    }

    public function getStock(Request $request, $item_id, $warehouse_id)
    {
        if ($request->ajax()) {
            $item = Item::find($item_id);
            $main_stock = $item->mainStock($warehouse_id);

            return $this->ResponseJsonData($main_stock . " {$item->unit?->name}");
        }
        return abort(403);
    }

    /**
     * Select purchase request for stock usage.
     */
    public function select_purchase_request_stock_usage(Request $request)
    {
        $models = PurchaseRequestDetail::leftJoin('purchase_requests', 'purchase_requests.id', '=', 'purchase_request_details.purchase_request_id')
            ->where('purchase_request_details.jumlah_diapprove', '>', 'purchase_request_details.quantity_used')
            ->when($request->branch_id, function ($q) use ($request) {
                $q->where('purchase_requests.branch_id', $request->branch_id);
            })
            ->when($request->division_id, function ($q) use ($request) {
                $q->where('purchase_requests.division_id', $request->division_id);
            })
            ->whereIn('purchase_request_details.status', ['done', 'partial'])
            ->whereIn('purchase_requests.status', ['done', 'partial'])
            ->distinct('purchase_request_details.purchase_request_id')
            ->when($request->search, function ($q) use ($request) {
                return $q->where('purchase_requests.kode', 'like', "%{$request->search}%")
                    ->orWhere('purchase_requests.tanggal', 'like', "%{$request->search}%");
            })
            ->selectRaw('
                purchase_requests.id,
                purchase_requests.kode,
                purchase_requests.tanggal
            ')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return $this->ResponseJsonData($models);
    }

    public function history($id, Request $request)
    {
        try {
            $purhase_requests = DB::table('purchase_requests')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->whereIn('status', ['approve', 'done', 'partial'])
                ->select(
                    'id',
                    'kode as code',
                    'tanggal as date',
                    'type',
                )
                ->get();

            $purhase_requests = $purhase_requests->map(function ($item) {
                $item->link = route('admin.purchase-request.show', $item->id);
                $item->menu = 'purchase request';
                return $item;
            });

            if ($purhase_requests->first()->type == "general") {
                $purchase_orders = DB::table('purchase_order_general_details')
                    ->join('purchase_order_generals', 'purchase_order_generals.id', '=', 'purchase_order_general_details.purchase_order_general_id')
                    ->where('purchase_order_general_details.purchase_request_id', $id)
                    ->whereNull('purchase_order_generals.deleted_at')
                    ->whereIn('purchase_order_generals.status', ['approve', 'done'])
                    ->select(
                        'purchase_order_generals.id',
                        'purchase_order_generals.code',
                        'purchase_order_generals.date'
                    )
                    ->get();

                $purchase_orders = $purchase_orders->map(function ($item) {
                    $item->link = route('admin.purchase-order-general.show', $item->id);
                    $item->menu = 'purchase order general';
                    return $item;
                });
            } else {
                $purchase_orders = DB::table('purchase_order_service_details')
                    ->join('purchase_order_services', 'purchase_order_services.id', '=', 'purchase_order_service_details.purchase_order_service_id')
                    ->where('purchase_order_service_details.purchase_request_id', $id)
                    ->whereNull('purchase_order_services.deleted_at')
                    ->whereIn('purchase_order_services.status', ['approve', 'done'])
                    ->select(
                        'purchase_order_services.id',
                        'purchase_order_services.code',
                        'purchase_order_services.date'
                    )
                    ->get();

                $purchase_orders = $purchase_orders->map(function ($item) {
                    $item->link = route('admin.purchase-order-service.show', $item->id);
                    $item->menu = 'purchase order service';
                    return $item;
                });
            }

            $item_receiving_reports = DB::table('item_receiving_reports')
                ->when($purhase_requests->first()->type == "general", function ($q) {
                    $q->where('item_receiving_reports.reference_model', PurchaseOrderGeneral::class);
                })
                ->when($purhase_requests->first()->type == "service", function ($q) {
                    $q->where('item_receiving_reports.reference_model', PurchaseOrderService::class);
                })
                ->where('reference_model', PurchaseOrderGeneral::class)
                ->whereIn('reference_id', $purchase_orders->pluck('id')->toArray())
                ->whereNull('item_receiving_reports.deleted_at')
                ->whereNotIn('item_receiving_reports.status', ['pending', 'revert', 'void', 'reject'])
                ->select(
                    'item_receiving_reports.id',
                    'item_receiving_reports.kode as code',
                    'item_receiving_reports.date_receive as date',
                    'item_receiving_reports.reference_id',
                    'item_receiving_reports.tipe'
                )
                ->get();

            $item_receiving_reports = $item_receiving_reports->map(function ($item) {
                if ($item->tipe == 'jasa') {
                    $item_type = 'item-receiving-report-service';
                } elseif ($item->tipe == 'general') {
                    $item_type = 'item-receiving-report-general';
                } elseif ($item->tipe == 'trading') {
                    $item_type = 'item-receiving-report-trading';
                } elseif ($item->tipe == 'transport') {
                    $item_type = 'item-receiving-report-transport';
                }

                $item->link = route('admin.' . $item_type . '.show', $item->id);
                $item->menu = 'penerimaan barang ' . $item->tipe;
                return $item;
            });

            $purchase_returns = DB::table('purchase_returns')
                ->whereIn('item_receiving_report_id', $item_receiving_reports->pluck('id')->toArray())
                ->whereIn('status', ['approve', 'done'])
                ->whereNull('purchase_returns.deleted_at')
                ->select(
                    'id',
                    'code',
                    'date',
                    'item_receiving_report_id'
                )
                ->get();

            $purchase_returns = $purchase_returns->map(function ($item) {
                $item->link = route('admin.purchase-return.show', $item->id);
                $item->menu = 'retur pembelian';
                return $item;
            });

            $supplier_invoices = DB::table('supplier_invoice_details')
                ->join('supplier_invoices', 'supplier_invoices.id', '=', 'supplier_invoice_details.supplier_invoice_id')
                ->join('supplier_invoice_parents', function ($j) {
                    $j->on('supplier_invoice_parents.reference_id', '=', 'supplier_invoices.id')
                        ->where('supplier_invoice_parents.model_reference', 'App\Models\SupplierInvoice');
                })
                ->whereNull('supplier_invoices.deleted_at')
                ->whereNull('supplier_invoice_details.deleted_at')
                ->whereIn('supplier_invoices.status', ['approve'])
                ->whereIn('supplier_invoice_details.item_receiving_report_id', $item_receiving_reports->pluck('id')->toArray())
                ->select(
                    'supplier_invoices.id',
                    'supplier_invoices.code',
                    'supplier_invoices.accepted_doc_date as date',
                    'supplier_invoice_details.item_receiving_report_id',
                    'supplier_invoice_parents.id as supplier_invoice_parent_id'
                )
                ->get();

            $supplier_invoices = $supplier_invoices->map(function ($item) {
                $item->link = route('admin.supplier-invoice.show', $item->id);
                $item->menu = 'purchase invoice';
                return $item;
            });

            $fund_submissions = DB::table('fund_submission_supplier_details')
                ->join('fund_submissions', 'fund_submissions.id', '=', 'fund_submission_supplier_details.fund_submission_id')
                ->whereNull('fund_submissions.deleted_at')
                ->whereNull('fund_submission_supplier_details.deleted_at')
                ->whereIn('fund_submissions.status', ['approve'])
                ->whereIn('supplier_invoice_parent_id', $supplier_invoices->pluck('supplier_invoice_parent_id')->toArray())
                ->select(
                    'fund_submissions.id',
                    'fund_submissions.code',
                    'fund_submissions.date',
                    'fund_submission_supplier_details.supplier_invoice_parent_id'
                )
                ->get();

            $fund_submissions = $fund_submissions->map(function ($item) {
                $item->link = route('admin.fund-submission.show', $item->id);
                $item->menu = 'pengajuan dana';
                return $item;
            });

            $account_payables = DB::table('account_payable_details')
                ->join('account_payables', 'account_payables.id', '=', 'account_payable_details.account_payable_id')
                ->leftJoin('bank_code_mutations', function ($j) {
                    $j->on('account_payables.id', '=', 'bank_code_mutations.ref_id')
                        ->where('bank_code_mutations.ref_model', AccountPayable::class);
                })
                ->whereNull('account_payable_details.deleted_at')
                ->whereNull('account_payables.deleted_at')
                ->whereIn('account_payables.status', ['approve'])
                ->whereIn('account_payable_details.supplier_invoice_parent_id', $supplier_invoices->pluck('supplier_invoice_parent_id')->toArray())
                ->select(
                    'account_payables.id',
                    'account_payables.code',
                    'bank_code_mutations.code as bank_code_mutation_code',
                    'account_payables.date',
                    'account_payable_details.supplier_invoice_parent_id'
                )
                ->get()
                ->map(function ($item) {
                    $item->code = $item->bank_code_mutation_code ?? $item->code;
                    return $item;
                });

            $account_payables = $account_payables->map(function ($item) {
                $item->link = route('admin.account-payable.show', $item->id);
                $item->menu = 'pelunasan hutang';
                return $item;
            });

            $histories = $purhase_requests->unique('id')
                ->merge($purchase_orders)
                ->merge($item_receiving_reports)
                ->merge($supplier_invoices)
                ->merge($fund_submissions)
                ->merge($account_payables)
                ->merge($purchase_returns);
            return response()->json([
                'success' => true,
                'data' => $histories
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function check_purchase_request_status($id)
    {
        try {
            $model = PurchaseRequest::find($id);

            if ($model) {
                $details = $model->purchase_request_details;
                if ($model->type == 'general') {
                    $purchase_details =  \App\Models\PurchaseOrderGeneralDetailItem::whereHas('purchase_order_general_detail', function ($p) {
                        $p->whereHas('purchase_order_general', function ($q) {
                            $q->whereNull('deleted_at');
                        });
                    })
                        ->whereIn('purchase_request_detail_id', $details->pluck('id')->toArray())
                        ->whereNotIn('status', ['reject', 'void'])
                        ->get();
                } else {
                    $purchase_details =  \App\Models\PurchaseOrderServiceDetailItem::whereHas('purchase_order_service_detail', function ($p) {
                        $p->whereHas('purchase_order_service', function ($q) {
                            $q->whereNull('deleted_at');
                        });
                    })
                        ->whereIn('purchase_request_detail_id', $details->pluck('id')->toArray())
                        ->whereNotIn('status', ['reject', 'void'])
                        ->get();
                }

                foreach ($details as $key => $detail) {
                    $qty_po = $purchase_details->where('purchase_request_detail_id', $detail->id)->sum('quantity');
                    if ($qty_po == $detail->jumlah_diapprove) {
                        $detail->status = 'done';
                    } elseif ($qty_po == 0) {
                        $detail->status = 'approve';
                    } else {
                        $detail->status = 'partial';
                    }

                    $detail->save();
                }

                $details = $model->purchase_request_details->whereNotIn('status', ['reject', 'pending']);
                if ($details->count() == $details->where('status', 'done')->count()) {
                    $model->status = 'done';
                } else if ($details->count() == $details->where('status', 'approve')->count()) {
                    $model->status = 'approve';
                } else {
                    $model->status = 'partial';
                }

                $model->save();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function regenerate_pr_code()
    {
        DB::beginTransaction();
        try {
            $models = DB::table('purchase_requests')
                ->orderBy('tanggal')
                ->orderBy('id')
                ->get();

            foreach ($models as $key => $model) {
                DB::table('purchase_requests')
                    ->where('id', $model->id)
                    ->update(['kode' => $key]);
            }

            foreach ($models as $key => $model) {

                $last_purchase_request = model::whereMonth('tanggal', Carbon::parse($model->tanggal))
                    ->whereYear('tanggal', Carbon::parse($model->tanggal))
                    ->whereRaw('LENGTH(kode) > 5')
                    ->orderBy('id', 'desc')
                    ->withTrashed()
                    ->first();

                $branch = Branch::find($model->branch_id);
                // * create data

                if ($last_purchase_request) {
                    $kode = generate_code_purchase_request($last_purchase_request->kode, year: $model->tanggal, branch_sort: $branch->sort);
                } else {
                    $kode = generate_code_purchase_request("0000/0000/00/0000", year: $model->tanggal, branch_sort: $branch->sort);
                }

                DB::table('purchase_requests')
                    ->where('id', $model->id)
                    ->update(['kode' => $kode]);
            }


            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th->getMessage());
        }
    }
}
