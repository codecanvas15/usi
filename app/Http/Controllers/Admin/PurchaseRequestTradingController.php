<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseRequestTrading as model;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Http\Helpers\PrintHelper;
use App\Models\Authorization;
use App\Models\AuthorizationDetail;
use App\Models\Branch;
use App\Models\PurchaseRequestTradingDetail;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PurchaseRequestTradingController extends Controller
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
    protected string $view_folder = 'purchase-request-trading';

    /**
     * search  value
     *
     * @var string|null
     */
    protected string|null $search = null;

    /**
     * get_data_type
     *
     * @param Request  $request
     * @param string $type
     * @return mixed
     */
    public function index(Request $request)
    {
        $query = model::join('customers', 'customers.id', '=', 'purchase_request_tradings.customer_id')
            ->join('users', 'users.id', '=', 'purchase_request_tradings.created_by')
            ->when($request->from_date, function ($query) use ($request) {
                return $query->where('purchase_request_tradings.date', '>=', Carbon::parse($request->from_date));
            })
            ->when($request->to_date, function ($query) use ($request) {
                return $query->where('purchase_request_tradings.date', '<=', Carbon::parse($request->to_date));
            })
            ->select('purchase_request_tradings.*', 'customers.nama as customer_name', 'users.name as created_by_name');

        $authorization_details = AuthorizationDetail::leftJoin('authorizations', 'authorizations.id', 'authorization_details.authorization_id')
            ->select('authorization_details.*', 'authorizations.model_id', 'authorizations.model')
            ->where('authorizations.model', model::class)
            ->whereIn('authorizations.model_id', $query->pluck('id'))
            ->get();

        $checkAuthorizePrint = authorizePrint('purchase_request_trading');

        return datatables($query)
            ->addIndexColumn()
            ->editColumn('date', function ($model) {
                return localDate($model->date);
            })
            ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                'field' => $row->code,
                'row' => $row,
                'main' => $this->view_folder,
            ]))
            ->editColumn('status', function ($row) {
                $badge = '<div class="badge badge-pill badge-' . purchase_request_status()[$row->status]['color'] . '">
                ' . purchase_request_status()[$row->status]['text'] . '
                                </div>';
                $order_badge = '<br><div class="badge badge-pill badge-' . PURCHASE_REQUEST_TRADING_STATUS[$row->order_status]['color'] . '">
                ' . PURCHASE_REQUEST_TRADING_STATUS[$row->order_status]['text'] . '
                                </div>';

                return $badge . $order_badge;
            })
            ->addColumn('action', function ($row) use ($authorization_details) {
                $can_delete_or_void = $authorization_details->where('model_id', $row->id)
                    ->where('status', 'approve')->count() == 0;

                return view('components.datatable.button-datatable', [
                    'row' => $row,
                    'main' => $this->view_folder,
                    'btn_config' => [
                        'detail' => [
                            'display' => true,
                        ],
                        'edit' => [
                            'display' => $row->status != "approve" &&  $row->status != "reject" &&  $row->status != "void" && $can_delete_or_void,
                        ],
                        'delete' => [
                            'display' => $row->status != "approve" &&  $row->status != "reject" &&  $row->status != "void" && $can_delete_or_void,
                        ],
                    ],
                ]);
            })
            ->editColumn('export', function ($row) use ($checkAuthorizePrint) {
                $link = route("purchase-request-trading.export", ['id' => encryptId($row->id)]);
                $link_detail = route("admin.purchase-request-trading.show", ['purchase_request_trading' => $row->id]);
                $export = '<a target="_blank" href="' . $link . '" class="btn btn-sm btn-light" onclick="show_print_out_modal(event)" ' . ($checkAuthorizePrint ? 'data-model="' . model::class . '" data-id="' . $row->id . '" data-print-type="purchase_request_trading" data-link="' . $link_detail . '" data-code="' . $row->code . '"' : '') . '>Export</a>';

                return $export;
            })
            ->rawColumns(
                ['action', 'code', 'export', 'status']
            )
            ->make();
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
        if (!$authorization->is_authoirization_exist(model::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }
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
        $this->validate($request,  [
            'branch_id' => 'required',
            'date' => 'required',
            'customer_id' => 'required',
            'sh_number_id' => 'required',
            'item_id' => 'required',
            'qty' => 'required',
            'note' => 'required',
        ]);

        $branch = Branch::find($request->branch_id);

        // * create data
        $model = new model();
        $model->code = generate_code(model::class, 'code', 'date', 'PRT', $branch->sort, $request->date);

        $model->loadModel([
            'branch_id' => $request->branch_id,
            'customer_id' => $request->customer_id,
            'sh_number_id' => $request->sh_number_id,
            'date' => Carbon::parse($request->date),
            'note' => $request->note,
            'created_by' => auth()->user()->id,
            'status' => 'pending',
            'order_status' => 'pending',
        ]);

        // * saving
        try {
            $model->save();

            $purchase_request_trading_detail = new PurchaseRequestTradingDetail();
            $purchase_request_trading_detail->loadModel([
                'purchase_request_trading_id' => $model->id,
                'item_id' => $request->item_id,
                'qty' => thousand_to_float($request->qty),
            ]);
            $purchase_request_trading_detail->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: 0,
                title: "Purchase Request Trading",
                subtitle: Auth::user()->name . " mengajukan purchase request Trading " . $model->code,
                link: route('admin.purchase-request-trading.show', $model),
                update_status_link: route('admin.purchase-request-trading.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
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

        return redirect()->route("admin.purchase-request.index")->with($this->ResponseMessageCRUD());
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
        $model = model::with(['purchase_request_trading_details.item', 'customer', 'sh_number.sh_number_details'])->findOrFail($id);

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
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = in_array($model->status, ['approve']) && $model->order_status == 'pending';
        $authorization_logs['can_void_request'] = in_array($model->status, ['approve']) && $model->order_status == 'pending';

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

        validate_branch($model->branch_id);

        if (!in_array($model->status, ['pending', 'revert'])) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, 'Tidak dapat mengubah data'));
        }

        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
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

        // * validate
        $this->validate($request,  [
            'branch_id' => 'required',
            'date' => 'required',
            'customer_id' => 'required',
            'sh_number_id' => 'required',
            'item_id' => 'required',
            'qty' => 'required',
            'note' => 'required',
        ]);

        // * create data
        $model = model::findOrFail($id);
        $model->loadModel([
            'branch_id' => $request->branch_id,
            'customer_id' => $request->customer_id,
            'sh_number_id' => $request->sh_number_id,
            'date' => Carbon::parse($request->date),
            'note' => $request->note,
        ]);

        // * saving
        try {
            $model->save();

            $purchase_request_trading_detail = PurchaseRequestTradingDetail::where('purchase_request_trading_id', $model->id)->first();
            $purchase_request_trading_detail->loadModel([
                'purchase_request_trading_id' => $model->id,
                'item_id' => $request->item_id,
                'qty' => thousand_to_float($request->qty),
            ]);
            $purchase_request_trading_detail->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: 0,
                title: "Purchase Request Trading",
                subtitle: Auth::user()->name . " mengajukan purchase request Trading " . $model->code,
                link: route('admin.purchase-request-trading.show', $model),
                update_status_link: route('admin.purchase-request-trading.update-status', ['id' => $model->id]),
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

        return redirect()->route('admin.purchase-request-trading.show', $model)->with($this->ResponseMessageCRUD());
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

        return redirect()->route("admin.purchase-request.index")->with($this->ResponseMessageCRUD());
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
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);
                // * saving and make response
                $model->status = $request->status;
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
     * sale_order export
     *
     * @return \Illuminate\Http\Response
     */
    public function export($id, Request $request)
    {
        if (!$request->preview && authorizePrint('purchase_request_trading')) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                model::class,
                decryptId($id),
                'purchase_request_trading',
            );

            if (!$result) {
                return abort(403);
            }
        }

        $model = model::with(['purchase_request_trading_details', 'created_by_user'])->findOrFail(decryptId($id));

        $fileName = 'purchase-request-trading-' . $model->code . '.pdf';

        $qr_url = route('purchase-request-trading.export', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));

        $approval = Authorization::where('model', model::class)
            ->with(['details' => function ($q) {
                $q->where('status', 'approve')
                    ->where('note', 'not like', '%otomatis%');
            }])
            ->where('model_id', $model->id)
            ->first();

        $pdf = PDF::loadview("admin/.$this->view_folder./export", compact('model', 'qr', 'approval'))->setPaper($request->paper ?? 'a4', $request->orientation ?? 'potrait');;
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $h = $canvas->get_height();
        $w = $canvas->get_width();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM} / {PAGE_COUNT}", "sans-serif", 8, array(0, 0, 0));

        if ($request->preview) {
            Storage::disk('public')->deleteDirectory('tmp_purchase_request_trading');
            $tmp_file_name = 'purchase_request_trading_' . time() . '.pdf';
            $path = 'tmp_purchase_request_trading/' . $tmp_file_name;
            Storage::disk('public')->put($path, $pdf->output());

            return response()->json($path);
        }

        return $pdf->stream($fileName);
    }

    public function select(Request $request)
    {
        $model = model::join('customers', 'customers.id', '=', 'purchase_request_tradings.customer_id')
            ->where('purchase_request_tradings.status', 'approve')
            ->where('purchase_request_tradings.order_status', '!=', 'complete')
            ->when($request->search, function ($query) use ($request) {
                $query->where('purchase_request_tradings.code', 'like', '%' . $request->search . '%')
                    ->orWhere('customers.nama', 'like', '%' . $request->search . '%');
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                $query->where('purchase_request_tradings.branch_id', get_current_branch_id());
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                $query->where('purchase_request_tradings.branch_id', $request->branch_id);
            })
            ->orderByDesc('purchase_request_tradings.created_at')
            ->select('purchase_request_tradings.*', 'customers.nama as customer_name')
            ->where(function ($q) use ($request) {
                $q->where('purchase_request_tradings.order_status', '!=', 'done')
                    ->when($request->purchase_request_trading_id, function ($q) use ($request) {
                        $q->orWhere('purchase_request_tradings.id', $request->purchase_request_trading_id);
                    });
            })
            ->paginate(10);

        return $this->ResponseJson($model);
    }
}
