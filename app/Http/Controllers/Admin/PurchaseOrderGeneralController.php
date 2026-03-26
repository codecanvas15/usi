<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Http\Helpers\PrintHelper;
use App\Models\AccountPayable;
use App\Models\Authorization;
use App\Models\Project;
use App\Models\PurchaseOrderGeneral;
use App\Models\PurchaseOrderGeneralDetailItem;
use App\Models\PurchaseOrderGeneralDetailItemTax;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use App\Models\SaleOrderGeneral;
use App\Models\SaleOrderGeneralDetail;
use App\Models\StockMutation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PurchaseOrderGeneralController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view purchase-general", ['only' => ['index', 'show', 'data']]);
        $this->middleware("permission:create purchase-general", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit purchase-general", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete purchase-general", ['only' => ['destroy']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'purchase-order-general';

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $checkAuthorizePrint = authorizePrint('purchase_order_general');

            $data = \App\Models\PurchaseOrderGeneral::with([
                'purchaseOrderGeneralDetails.purchase_order_general_detail_items.item',
                'purchaseOrderGeneralDetails.purchase_order_general_detail_items.unit'
            ])
                ->select('purchase_order_generals.*')
                ->join('purchases', function ($query) {
                    $query->on('purchases.model_id', 'purchase_order_generals.id')
                        ->where('model_reference', \App\Models\PurchaseOrderGeneral::class);
                })
                ->leftJoin('cash_advance_payments', function ($query) {
                    $query->on('cash_advance_payments.purchase_id', 'purchases.id')
                        ->whereNull('cash_advance_payments.deleted_at')
                        ->where('cash_advance_payments.status', 'approve');
                })
                ->with(['vendor'])
                ->when($request->branch_id && get_current_branch()->is_primary, function ($query) use ($request) {
                    $query->where('purchase_order_generals.branch_id', $request->branch_id);
                })
                ->when(!get_current_branch()->is_primary, function ($query) {
                    $query->where('purchase_order_generals.branch_id', get_current_branch()->id);
                })
                ->when($request->status, function ($query) use ($request) {
                    $query->where('purchase_order_generals.status', $request->status);
                })
                ->when($request->from_date, function ($query) use ($request) {
                    $query->whereDate('purchase_order_generals.date', '>=', Carbon::parse($request->from_date));
                })
                ->when($request->to_date, function ($query) use ($request) {
                    $query->whereDate('purchase_order_generals.date', '<=', Carbon::parse($request->to_date));
                })
                ->when($request->vendor_id, function ($query) use ($request) {
                    $query->where('purchase_order_generals.vendor_id', $request->vendor_id);
                })
                ->select('purchase_order_generals.*', 'cash_advance_payments.id as cash_advance_payment_id');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', fn($row) => \Carbon\Carbon::parse($row->date)->format('d-m-Y'))
                ->editColumn('code', function ($row) use ($checkAuthorizePrint) {
                    $link = route('purchase-order-general.export-pdf', ['id' => encryptId($row->id)]);
                    $linkDetail = route('admin.purchase-order-general.show', $row->id);

                    $button = view('components.datatable.detail-link', [
                        'field' => $row->code,
                        'row' => $row,
                        'main' => $this->view_folder,
                        'permission_name' => 'purchase-general',

                    ]);

                    $button .= '<br>' . view('components.button-auth-print', [
                        'type' => 'purchase_order_general',
                        'href' => $link,
                        'model' => PurchaseOrderGeneral::class,
                        'did' => $row->id,
                        'link' => $linkDetail,
                        'code' => $row->code,
                        'condition' => $checkAuthorizePrint,
                        'size' => 'xs',
                    ]);

                    if ($row->status == 'close') {
                        $link_closed = route('purchase-order-general.export-pdf', ['id' => encryptId($row->id)]) . '?type=closed';
                        $button .= '<br>' . view('components.button-auth-print', [
                            'type' => 'purchase_order_general_closed',
                            'model' => PurchaseOrderGeneral::class,
                            'did' => $row->id,
                            'href' => $link_closed,
                            'label' => 'Print Close PO',
                            'icon' => 'edit',
                            'color' => 'primary',
                            'symbol' => '&',
                            'size' => 'xs',
                        ]);
                    }

                    return $button;
                    // view("components.datatable.export-button", [
                    //     'route' => route('purchase-order-general.export-pdf', ['id' => encryptId($row->id)]),
                    //     'onclick' => "show_print_out_modal(event)",
                    // ]);
                })
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . purchase_order_general_status()[$row->status]['color'] . '">
                                            ' . purchase_order_general_status()[$row->status]['label'] . ' - ' . purchase_order_general_status()[$row->status]['text'] . '
                                        </div>';

                    if (!in_array($row->status, ['done']) && $row->cash_advance_payment_id) {
                        $badge .= '<br><div class="badge badge-pill badge-danger  mt-1 animate__animated animate__pulse animate__infinite infinite">Uang Muka Telah dibayar!</div>';
                    }
                    return $badge;
                })
                ->addColumn('action', function ($row) {
                    return view('admin.purchase-order-general.data-table.btn', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'permission_name' => 'purchase-general',
                        'btn_config' => [
                            'detail' => [
                                'display' => false,
                            ],
                            'edit' => [
                                'display' =>  $row->check_available_date ? in_array($row->status, ['pending', 'revert']) : false,
                            ],
                            'delete' => [
                                'display' => $row->check_available_date ? in_array($row->status, ['pending', 'revert']) : false,
                            ],
                        ],
                    ]);
                })
                ->rawColumns(['action', 'status', 'code', 'export'])
                ->make(true);
        }

        return abort(404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(PurchaseOrderGeneral::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

        if ($request->type == 'sales-order') {
            return view("admin.$this->view_folder.sales-order.create");
        }

        return view("admin.$this->view_folder.purchase-request.create");
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

        try {
            $service = new \App\Services\PurchaseOrderGeneralService();
            $service->create($request);
        } catch (\Throwable $th) {
            DB::rollback();

            if ($request->ajax()) {
                return $this->ResponseJsonMessage($th->getMessage(), 500);
            }
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", null, $th->getMessage()))->withInput();
        }

        DB::commit();

        if ($request->ajax()) {
            return $this->ResponseJsonMessage('Success create purchase order general', 200);
        }
        return redirect()->route("admin.purchase.index")->with($this->ResponseMessageCRUD(true, "delete"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = \App\Models\PurchaseOrderGeneral::with([
            'branch',
            'vendor',
            'currency',
            'create',
            'approve',
            'purchaseOrderGeneralDetails.purchase_request.division',
            'purchaseOrderGeneralDetails.purchase_order_general_detail_items.item',
            'purchaseOrderGeneralDetails.purchase_order_general_detail_items.unit',
            'purchaseOrderGeneralDetails.purchase_order_general_detail_items.purchase_order_general_detail_item_taxes.tax',
        ])
            ->findOrFail($id);

        $project_id = $model->purchaseOrderGeneralDetails->map(function ($purchase_order_general_detail) {
            return $purchase_order_general_detail->purchase_request->project_id ?? null;
        })->unique()->toArray();

        $projects = Project::whereIn('id', $project_id)->get();

        validate_branch($model->branch_id);

        $parent_status_logs = $model->logs_data['status_logs'] ?? [];
        $parent_activity_logs = $model->logs_data['activity_logs'] ?? [];
        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: PurchaseOrderGeneral::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );
        $authorization_logs['can_revert'] = $model->check_available_date && in_array($model->status, ['approve', 'partial-approve', 'partial-rejected']);
        $authorization_logs['can_void'] = $model->check_available_date && in_array($model->status, ['approve', 'partial-approve', 'partial-rejected']);

        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $is_has_down_payment = $model->purchase->purchase_down_payments()->whereNotIn('status', ['void', 'reject'])->count() > 0;
        $authorization_logs['can_revert_request'] = $model->check_available_date && in_array($model->status, ['approve', 'partial-approve', 'partial-rejected']) && !$is_has_down_payment;
        $authorization_logs['can_void_request'] = $model->check_available_date && in_array($model->status, ['approve', 'partial-approve', 'partial-rejected']) && !$is_has_down_payment;
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        if ($model->type == 'purchase-request') {
            return view("admin.$this->view_folder.purchase-request.show", compact('model', 'parent_status_logs', 'parent_activity_logs', 'authorization_log_view', 'auth_revert_void_button', 'projects'));
        }

        return view("admin.$this->view_folder.sales-order.show", compact('model', 'parent_status_logs', 'parent_activity_logs', 'authorization_log_view', 'auth_revert_void_button', 'projects'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = \App\Models\PurchaseOrderGeneral::findOrFail($id);

        $is_has_down_payment = $model->purchase->purchase_down_payments()->whereNotIn('status', ['void', 'reject'])->count() > 0;
        if ($is_has_down_payment) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'PO telah memiliki uang muka'));
        }

        if (!$model->check_available_date) {
            abort(403);
        }

        validate_branch($model->branch_id);

        if (!in_array($model->status, ['pending', 'revert'])) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "edit", null, "Data tidak dapat diubah karena status sudah bukan pending"));
        }

        if ($model->type == 'sales-order') {
            return view("admin.$this->view_folder.sales-order.edit", compact('model'));
        }

        return view("admin.$this->view_folder.purchase-request.edit", compact('model'));
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

        $model = \App\Models\PurchaseOrderGeneral::findOrFail($id);

        try {
            $service = new \App\Services\PurchaseOrderGeneralService();
            $service->update($request, $id);
        } catch (\Throwable $th) {
            DB::rollback();

            if ($request->ajax()) {
                return $this->ResponseJsonMessage($th->getMessage(), 500);
            }
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "edit", null, $th->getMessage()))->withInput();
        }

        DB::commit();

        if ($request->ajax()) {
            return $this->ResponseJsonMessage('Success update purchase order general', 200);
        }

        return redirect()->route("admin.purchase-order-general.show", $model)->with($this->ResponseMessageCRUD(true, "edit"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = \App\Models\PurchaseOrderGeneral::findOrFail($id);
        $is_has_down_payment = $model->purchase->purchase_down_payments()->whereNotIn('status', ['void', 'reject'])->count() > 0;
        if ($is_has_down_payment) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'PO telah memiliki uang muka'));
        }

        validate_branch($model->branch_id);

        if (!in_array($model->status, ['pending', 'revert'])) {
            return redirect()->route("admin.purchase.index")->with($this->ResponseMessageCRUD(false, "delete", null, "Status tidak dapat dihapus"));
        }

        $purchase_request_ids = $model->purchaseOrderGeneralDetails->pluck('purchase_request_id')->unique()->toArray();
        DB::beginTransaction();
        try {
            $model->purchaseOrderGeneralDetails->map(function ($purchas_order_general_detail) {
                $purchas_order_general_detail
                    ->purchase_order_general_detail_items
                    ->each(function ($detail) {
                        // * update purchase request status
                        if ($detail->purchase_request_detail) {
                            $detail->purchase_request_detail->status = 'approve';
                            $detail->purchase_request_detail->save();
                        }

                        // * update sale order general status
                        if ($detail->sale_order_general_detail) {
                            $detail->sale_order_general_detail->amount_paired -= $detail->quantity;
                            $detail->sale_order_general_detail->status_pairing = $detail->sale_order_general_detail->amount_paired > 0 ? 'partial' : 'unpaired';
                            $detail->sale_order_general_detail->save();
                        }
                    });
            });

            $model->delete();

            foreach ($purchase_request_ids as $key => $purchase_request_id) {
                app('App\Http\Controllers\Admin\PurchaseRequestController')->check_purchase_request_status($purchase_request_id);
            }

            Authorization::where('model', PurchaseOrderGeneral::class)
                ->where('model_id', $model->id)
                ->delete();
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->route("admin.purchase.index")->with($this->ResponseMessageCRUD(false, "delete", null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.purchase.index")->with($this->ResponseMessageCRUD(true, "delete"));
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
        $model = \App\Models\PurchaseOrderGeneral::findOrFail($id);
        validate_branch($model->branch_id);

        $is_has_down_payment = $model->purchase->purchase_down_payments()->whereNotIn('status', ['void', 'reject'])->count() > 0;
        if (in_array($request->status, ['revert', 'void']) && $is_has_down_payment) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'PO telah memiliki uang muka'));
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
                $this->create_activity_status_log(\App\Models\PurchaseOrderGeneral::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);
                $model->update([
                    'status' => $request->status,
                    'close_note' => $request->close_note ?? null,
                    'approved_by' => $request->status == 'approve' ? Auth::user()->id : null
                ]);
            } else {
                $this->create_activity_status_log(\App\Models\PurchaseOrderGeneral::class, $id, $request->note ?? $request->message ?? 'message not available', null, $request->status);
            }

            $model->purchaseOrderGeneralDetails()
                ->each(function ($query) {
                    app('App\Http\Controllers\Admin\PurchaseRequestController')->check_purchase_request_status($query->purchase_request_id);
                });

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "update", null, $th->getMessage()));
        }
        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, "update"));
    }

    /**
     * Detail api for edit
     *
     * @param int|null $id
     * @return \Illuminate\Http\Response
     */
    public function detail_api_for_edit($id)
    {
        $model = \App\Models\PurchaseOrderGeneral::with([
            'branch',
            'vendor',
            'currency',
        ])
            ->findOrFail($id);

        if ($model->type == 'purchase-request') {
            $main = $model->purchaseOrderGeneralDetails()->where('type', 'main')->with([
                'purchase_request.division',
                'purchase_order_general_detail_items.purchase_request_detail.unit',
                'purchase_order_general_detail_items.purchase_request_detail.lock_stock',
                'purchase_order_general_detail_items.item.unit',
                'purchase_order_general_detail_items.purchase_order_general_detail_item_taxes.tax',
            ])->get();

            $additional = $model->purchaseOrderGeneralDetails()->where('type', 'additional')->with([
                'purchase_order_general_detail_items.item.unit',
                'purchase_order_general_detail_items.purchase_order_general_detail_item_taxes.tax',
            ])->get();

            if (!in_array($model->status, ['pending', 'revert'])) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, "edit", null, "Data tidak dapat diubah karena status sudah bukan pending"));
            }

            validate_branch($model->branch_id);

            return $this->ResponseJsonData(compact('model', 'main', 'additional'));
        }

        if ($model->type == 'sales-order') {
            $main = $model->purchaseOrderGeneralDetails()->where('type', 'main')->with([
                'purchase_order_general_detail_items.item.unit',
                'purchase_order_general_detail_items.purchase_order_general_detail_item_taxes.tax',
                'purchase_order_general_detail_items.sale_order_general_detail',
                'sale_order_general.branch',
                'sale_order_general.customer',
                'sale_order_general.sale_order_general_details.sale_order_general_detail_taxes.tax',
                'sale_order_general.sale_order_general_details.item.unit',
                'sale_order_general.sale_order_general_details.sale_order_general_detail_taxes.tax',
            ])->get();

            $additional = $model->purchaseOrderGeneralDetails()->where('type', 'additional')->with([
                'purchase_order_general_detail_items.item.unit',
                'purchase_order_general_detail_items.purchase_order_general_detail_item_taxes.tax',
            ])->get();

            if (!in_array($model->status, ['pending', 'revert'])) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, "edit", null, "Data tidak dapat diubah karena status sudah bukan pending"));
            }

            validate_branch($model->branch_id);

            return $this->ResponseJsonData(compact('model', 'main', 'additional'));
        }
    }

    /**
     * Select api for item receiving report
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function select_api_for_item_receiving(Request $request)
    {
        $model = \App\Models\PurchaseOrderGeneral::whereIn('status', ['approve', 'partial-approve', 'partial-rejected', 'partial'])
            ->leftJoin('vendors', 'vendors.id', '=', 'purchase_order_generals.vendor_id')
            ->when($request->branch_id && get_current_branch()->is_primary, function ($query) use ($request) {
                $query->where('purchase_order_generals.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                $query->where('purchase_order_generals.branch_id', get_current_branch()->id);
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('purchase_order_generals.code', "LIKE", "%{$request->search}%")
                        ->orWhere('purchase_order_generals.date', "LIKE", "%{$request->search}%")
                        ->orWhere('vendors.nama', "LIKE", "%{$request->search}%");
                });
            })
            ->orderByDesc('purchase_order_generals.created_at')
            ->select('purchase_order_generals.id', 'purchase_order_generals.code', 'vendors.nama')
            ->paginate(10);

        return $this->ResponseJson($model);
    }

    /**
     * Detail api for item receiving report
     *
     * @param int|null $id
     * @return \Illuminate\Http\Response
     */
    public function detail_api_for_item_receiving($id = null)
    {
        $model = \App\Models\PurchaseOrderGeneral::with('vendor')->findOrFail($id);

        // * validate
        validate_branch($model->branch_id);

        $details = \App\Models\PurchaseOrderGeneralDetail::where('purchase_order_general_id', $model->id)
            ->leftJoin('purchase_order_general_detail_items', 'purchase_order_general_detail_items.purchase_order_general_detail_id', 'purchase_order_general_details.id')
            ->whereIn('purchase_order_general_details.status', ['approve', 'partial-approve', 'partial-rejected', 'partial'])
            // ->where('purchase_order_general_details.type', 'main')
            ->with([
                'purchase_order_general_detail_items.item',
                'purchase_order_general_detail_items.unit',
                'purchase_order_general_detail_items' => function ($q) {
                    $q->where('quantity', '>', 'quantity_received');
                    $q->whereIn('status', ['approve', 'partial-approve', 'partial-rejected', 'partial']);
                },
            ])
            ->select('purchase_order_general_details.*')
            ->distinct('purchase_order_general_details.id')
            ->get();

        return $this->ResponseJsonData(compact('model', 'details'));
    }

    /**
     * Export data pdf
     *
     * @param int|string $id
     * @return \Illuminate\Http\Response
     */
    public function export_pdf($id, Request $request)
    {
        if (!$request->preview && authorizePrint('purchase_order_general')) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                \App\Models\PurchaseOrderGeneral::class,
                decryptId($id),
                'purchase_order_general',
            );

            if (!$result) {
                return abort(403);
            }
        }

        $model = \App\Models\PurchaseOrderGeneral::with('purchaseOrderGeneralDetails.purchase_order_general_detail_items.purchase_order_general_detail_item_taxes')
            ->findOrFail(decryptId($id));

        $project_id = $model->purchaseOrderGeneralDetails->map(function ($purchase_order_general_detail) {
            return $purchase_order_general_detail->purchase_request->project_id ?? null;
        })->unique()->toArray();

        $projects = Project::whereIn('id', $project_id)->get();

        $purchase_request_code = PurchaseRequest::whereIn('id', $model->purchaseOrderGeneralDetails->pluck('purchase_request_id')->toArray())->pluck('kode')->toArray();
        $purchase_order_general_item = PurchaseOrderGeneralDetailItem::whereIn('purchase_order_general_detail_id', $model->purchaseOrderGeneralDetails->pluck('id')->toArray())->get();
        $before_discount = $purchase_order_general_item->map(function ($purchase_order_general_detail_item) {
            return $purchase_order_general_detail_item->quantity * $purchase_order_general_detail_item->price_display;
        })->sum();

        $discount_total = $purchase_order_general_item->map(function ($purchase_order_general_detail_item) {
            return $purchase_order_general_detail_item->quantity * $purchase_order_general_detail_item->discount;
        })->sum();

        $subtotal = $purchase_order_general_item->sum('sub_total');

        $taxes = PurchaseOrderGeneralDetailItemTax::whereIn('purchase_order_general_detail_item_id', $purchase_order_general_item->pluck('id')->toArray())->get();
        $tax_data = $taxes->unique('tax_id')->map(function ($tax) use ($taxes) {
            $tax->total =  $taxes->where('tax_id', $tax->tax_id)->sum('total');
            return $tax;
        });

        $qr_url = route('purchase-order-general.export-pdf', ['id' => $id]);
        if ($request->type == 'closed') {
            $qr_url .= '?type=closed';
        }
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));

        $approval = Authorization::where('model', \App\Models\PurchaseOrderGeneral::class)
            ->with(['details' => function ($q) {
                $q->where('status', 'approve')
                    ->where('note', 'not like', '%otomatis%');
            }])
            ->where('model_id', $model->id)
            ->first();

        if ($request->type == 'closed') {
            $taxes = $taxes->unique('tax_id');
            $pdf = PDF::loadView("admin.$this->view_folder.pdf.export-closed", compact('model', 'qr', 'purchase_request_code', 'before_discount', 'discount_total', 'subtotal', 'approval', 'tax_data', 'projects'));
        } else {
            $pdf = PDF::loadView("admin.$this->view_folder.pdf.export", compact('model', 'qr', 'purchase_request_code', 'before_discount', 'discount_total', 'subtotal', 'approval', 'tax_data', 'projects'));
        }
        $pdf->setPaper($request->paper ?? 'a4', $request->orientation ?? 'potrait');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $w = $canvas->get_width();
        $h = $canvas->get_height();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM} / {PAGE_COUNT}", '', 8);

        if ($request->preview) {
            Storage::disk('public')->deleteDirectory('tmp_purchase_order_general');
            $tmp_file_name = 'purchase_order_general_' . time() . '.pdf';
            $path = 'tmp_purchase_order_general/' . $tmp_file_name;
            Storage::disk('public')->put($path, $pdf->output());

            return response()->json($path);
        }

        return $pdf->stream("PURCHASE ORDER GENERAL $model->date - $model->code.pdf");
    }

    public function history($id, Request $request)
    {
        try {
            $purchase_order_generals = DB::table('purchase_order_general_details')
                ->join('purchase_order_generals', 'purchase_order_generals.id', '=', 'purchase_order_general_details.purchase_order_general_id')
                ->whereNull('purchase_order_generals.deleted_at')
                ->where('purchase_order_general_id', $id)
                ->select(
                    'purchase_order_generals.id',
                    'purchase_order_generals.code',
                    'purchase_order_generals.date',
                    'purchase_order_generals.status',
                    'purchase_order_general_details.purchase_request_id',
                )
                ->get();

            $purchase_order_generals = $purchase_order_generals->map(function ($item) {
                $item->link = route('admin.purchase-order-general.show', $item->id);
                $item->menu = 'purchase order general';
                return $item;
            });

            $purhase_requests = DB::table('purchase_requests')
                ->whereIn('id', $purchase_order_generals->pluck('purchase_request_id')->toArray())
                ->whereNull('deleted_at')
                ->whereIn('status', ['approve', 'done', 'partial'])
                ->select(
                    'id',
                    'kode as code',
                    'tanggal as date'
                )
                ->get();

            $purhase_requests = $purhase_requests->map(function ($item) {
                $item->link = route('admin.purchase-request.show', $item->id);
                $item->menu = 'purchase request';
                return $item;
            });

            $item_receiving_reports = DB::table('item_receiving_reports')
                ->where('reference_model', PurchaseOrderGeneral::class)
                ->whereIn('reference_id', $purchase_order_generals->pluck('id')->toArray())
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
                ->merge($purchase_order_generals->unique('id'))
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


    /**
     * Select option for purchase order general
     */
    public function selectForPurchaseOrderGeneral(Request $request)
    {
        if ($request->ajax()) {
            $data = \App\Models\SaleOrderGeneral::whereIn('sale_order_generals.status', ['approve', 'done'])
                ->join('sale_order_general_details', function ($q) {
                    return $q->on('sale_order_generals.id', 'sale_order_general_details.sale_order_general_id')
                        ->whereIn('sale_order_general_details.status', ['approve'])
                        ->whereRaw('sale_order_general_details.amount_paired < sale_order_general_details.amount');
                })
                ->join('customers', function ($q) {
                    return $q->on('sale_order_generals.customer_id', 'customers.id');
                })
                ->when($request->search, function ($q) use ($request) {
                    $q->where(function ($query) use ($request) {
                        $query->where('sale_order_generals.kode', 'like', "%$request->search%")
                            ->orWhereDate('sale_order_generals.tanggal', 'like', "%$request->search%")
                            ->orWhere('customers.nama', 'like', "%$request->search%");
                    });
                })
                ->when($request->selected_id, function ($q) use ($request) {
                    $selected_id = explode(',', $request->selected_id);
                    return $q->whereNotIn('sale_order_generals.id', $selected_id);
                })
                ->orderByDesc('sale_order_generals.created_at')
                ->distinct('sale_order_generals.id')
                ->select([
                    'sale_order_generals.id',
                    'sale_order_generals.kode',
                    'sale_order_generals.tanggal',
                    'customers.nama',
                ])
                ->paginate(10);

            return $this->ResponseJson($data);
        }

        return abort(403);
    }

    /**
     * Get detail for sales order
     */
    public function getDetailForPurchaseOrderGeneral(string $id)
    {
        $data = DB::table('sale_order_generals')
            ->where('sale_order_generals.id', $id)
            ->whereIn('sale_order_generals.status', ['approve', 'done'])
            ->join('sale_order_general_details', function ($q) {
                return $q->on('sale_order_generals.id', 'sale_order_general_details.sale_order_general_id')
                    ->whereIn('sale_order_general_details.status', ['approve', 'done'])
                    ->whereRaw('sale_order_general_details.amount_paired < sale_order_general_details.amount');
            })
            ->join('branches', function ($q) {
                return $q->on('sale_order_generals.branch_id', 'branches.id');
            })
            ->join('customers', function ($q) {
                return $q->on('sale_order_generals.customer_id', 'customers.id');
            })
            ->distinct('sale_order_generals.id')
            ->select([
                'sale_order_generals.id',
                'sale_order_generals.kode',
                'sale_order_generals.tanggal',
                'customers.nama',
                'branches.name',
            ])
            ->first();

        $dataDetails = DB::table('sale_order_general_details')
            ->join('items', function ($q) {
                return $q->on('sale_order_general_details.item_id', 'items.id');
            })
            ->join('units', function ($q) {
                return $q->on('sale_order_general_details.unit_id', 'units.id');
            })
            ->where('sale_order_general_details.sale_order_general_id', $id)
            ->whereIn('sale_order_general_details.status', ['approve'])
            ->whereRaw('sale_order_general_details.amount_paired < sale_order_general_details.amount')
            ->select([
                'sale_order_general_details.id',
                'sale_order_general_details.amount',
                'sale_order_general_details.amount_paired',
                'items.id as item_id',
                'items.nama as item_name',
                'items.kode as item_code',
                'units.id as unit_id',
                'units.name as unit_name',
            ])
            ->get();

        return $this->ResponseJsonData([
            'oarent' => $data,
            'details' => $dataDetails,
        ]);
    }

    public function so_outstanding_data(Request $request)
    {
        $so_generals = SaleOrderGeneralDetail::whereHas('sale_order_general', function ($q) {
            return $q->whereNotIn('status', ['pending', 'revert', 'reject', 'void', 'done']);
        })
            ->leftJoin('purchase_order_general_detail_items', function ($query) {
                $query->on('purchase_order_general_detail_items.sale_order_general_detail_id', 'sale_order_general_details.id')
                    ->join('purchase_order_general_details', 'purchase_order_general_details.id', 'purchase_order_general_detail_items.purchase_order_general_detail_id')
                    ->join('purchase_order_generals', 'purchase_order_generals.id', 'purchase_order_general_details.purchase_order_general_id')
                    ->whereNotIn('purchase_order_generals.status', ['void', 'reject'])
                    ->whereNull('purchase_order_generals.deleted_at');
            })
            ->leftJoin('delivery_order_general_details', function ($query) {
                $query->on('delivery_order_general_details.sale_order_general_detail_id', 'sale_order_general_details.id')
                    ->join('delivery_order_generals', 'delivery_order_generals.id', 'delivery_order_general_details.delivery_order_general_id')
                    ->whereNotIn('delivery_order_generals.status', ['void', 'reject'])
                    ->whereNull('delivery_order_generals.deleted_at');
            })
            ->join('items', 'items.id', 'sale_order_general_details.item_id')
            ->join('sale_order_generals', 'sale_order_general_details.sale_order_general_id', 'sale_order_generals.id')
            ->join('customers', 'sale_order_generals.customer_id', 'customers.id')
            ->join('branches', 'sale_order_generals.branch_id', 'branches.id')
            ->selectRaw(
                'sale_order_general_details.*,
                sale_order_generals.quotation,
                sale_order_generals.tanggal,
                sale_order_generals.kode,
                items.kode as item_code,
                items.nama as item_name,
                customers.nama as customer_name,
                branches.name as branch_name,
                coalesce(sum(purchase_order_general_detail_items.quantity), 0) as purchased_qty,
                coalesce(sum(delivery_order_general_details.quantity), 0) as sent_qty,
                sale_order_general_details.amount - (coalesce(sum(purchase_order_general_detail_items.quantity), 0) + coalesce(sum(delivery_order_general_details.quantity), 0)) as outstanding_qty'
            )
            ->havingRaw('outstanding_qty > 0')
            ->groupBy('sale_order_general_details.id');

        $stock_mutations = StockMutation::whereIn('item_id', $so_generals->pluck('item_id'))
            ->get();

        $selected_ids = explode(',', $request->selected_ids) ?? [];

        return DataTables::of($so_generals)
            ->addIndexColumn()
            ->editColumn('tanggal', fn($row) => \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y'))
            ->editColumn('amount', fn($row) => formatNumber($row->amount))
            ->editColumn('purchased_qty', fn($row) => formatNumber($row->purchased_qty))
            ->editColumn('sent_qty', fn($row) => formatNumber($row->sent_qty))
            ->editColumn('outstanding_qty', fn($row) => formatNumber($row->outstanding_qty))
            ->editColumn('quotation', function ($row) {
                $link = asset('storage/' . $row->quotation);
                return $row->quotation ? "<a href='$link' target='_blank'>Lihat File</a>" : '';
            })
            ->addColumn('stock', function ($row) use ($stock_mutations) {
                $stock = $stock_mutations->where('item_id', $row->item_id)->sum('in') - $stock_mutations->where('item_id', $row->item_id)->sum('out');
                return formatNumber($stock);
            })
            ->addColumn('check', function ($row) use ($selected_ids) {
                $is_checked = in_array($row->id, $selected_ids) ? 'checked' : '';
                return "<input type='checkbox' name='selected_sale_order_general_detail_id[]' value='$row->id' class='checkbox-select' onclick='checkThis(this)' $is_checked>";
            })

            ->rawColumns(['quotation', 'check'])
            ->make(true);
    }

    public function get_selected_sale_order_general(Request $request)
    {
        $selected_ids = $request->selected_ids ?? [];
        $data = SaleOrderGeneralDetail::whereIn('id', $selected_ids)
            ->with(['item.unit'])
            ->get();

        $sale_order_generals = SaleOrderGeneral::whereIn('id', $data->pluck('sale_order_general_id'))
            ->get();

        $purchase_order_detail_items = \App\Models\PurchaseOrderGeneralDetailItem::whereHas('purchase_order_general_detail', function ($p) {
            $p->whereHas('purchase_order_general', function ($q) {
                $q->whereNotIn('status', ['reject', 'void'])
                    ->whereNull('deleted_at');
            });
        })
            ->whereIn('sale_order_general_detail_id', $data->pluck('id')->toArray())
            ->whereNotIn('status', ['reject', 'void'])
            ->get();

        $data = $data->map(function ($d) {
            $d->quantity = 0;
            $d->price = 0;
            $d->qty_po = $d->amount_paired;
            $d->outstanding_amount = $d->amount - $d->amount_paired;

            return $d;
        });

        $collections = new Collection();
        $sale_order_generals->map(function ($sale_order_general) use ($data, $collections) {
            $collections->push([
                'parent' => $sale_order_general,
                'children' => $data->where('sale_order_general_id', $sale_order_general->id)->values()->all(),
            ]);
        });

        return response()->json($collections);
    }

    public function pr_outstanding_data(Request $request)
    {
        $purchase_request_details = PurchaseRequestDetail::whereHas('purchase_request', function ($q) {
            return $q->whereNotIn('status', ['pending', 'revert', 'reject', 'void']);
        })
            ->join('items', 'items.id', 'purchase_request_details.item_id')
            ->join('purchase_requests', 'purchase_request_details.purchase_request_id', 'purchase_requests.id')
            ->leftJoin('projects', 'projects.id', 'purchase_requests.project_id')
            ->join('branches', 'purchase_requests.branch_id', 'branches.id')
            ->leftJoin('purchase_order_general_detail_items', function ($q) {
                $q->on('purchase_request_details.id', 'purchase_order_general_detail_items.purchase_request_detail_id')
                    ->join('purchase_order_general_details', 'purchase_order_general_detail_items.purchase_order_general_detail_id', 'purchase_order_general_details.id')
                    ->join('purchase_order_generals', 'purchase_order_general_details.purchase_order_general_id', 'purchase_order_generals.id')
                    ->whereNotIn('purchase_order_generals.status', ['revert', 'reject', 'void'])
                    ->whereNull('purchase_order_generals.deleted_at');
            })
            ->when($request->type, function ($q) use ($request) {
                return $q->where('purchase_requests.type', $request->type);
            })
            ->where('purchase_request_details.status', '!=', 'done')
            ->selectRaw(
                'purchase_request_details.*,
                purchase_requests.tanggal,
                purchase_requests.kode,
                projects.code as project_code,
                projects.name as project_name,
                items.kode as item_code,
                items.nama as item_name,
                branches.name as branch_name,
                COALESCE(SUM(purchase_order_general_detail_items.quantity), 0) as po_amount,
                purchase_request_details.jumlah_diapprove - COALESCE(SUM(purchase_order_general_detail_items.quantity), 0) as outstanding_amount'
            )
            ->havingRaw('jumlah_diapprove > po_amount')
            ->groupBy('purchase_request_details.id');

        $stock_mutations = StockMutation::whereIn('item_id', $purchase_request_details->pluck('item_id'))
            ->get();

        $selected_ids = explode(',', $request->selected_ids) ?? [];

        return DataTables::of($purchase_request_details)
            ->addIndexColumn()
            ->editColumn('tanggal', fn($row) => \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y'))
            ->editColumn('jumlah', fn($row) => formatNumber($row->jumlah))
            ->editColumn('jumlah_diapprove', fn($row) => formatNumber($row->jumlah_diapprove))
            ->editColumn('po_amount', fn($row) => formatNumber($row->po_amount))
            ->editColumn('outstanding_amount', fn($row) => formatNumber($row->outstanding_amount))
            ->addColumn('stock', function ($row) use ($stock_mutations) {
                $stock = $stock_mutations->where('item_id', $row->item_id)->sum('in') - $stock_mutations->where('item_id', $row->item_id)->sum('out');
                return formatNumber($stock);
            })
            ->addColumn('check', function ($row) use ($selected_ids) {
                $is_checked = in_array($row->id, $selected_ids) ? 'checked' : '';
                return "<input type='checkbox' name='selected_sale_order_general_detail_id[]' value='$row->id' class='checkbox-select' onclick='checkThis(this)' $is_checked>";
            })
            ->addColumn('project', function ($row) {
                return $row->project_code ? $row->project_code . ' - ' . $row->project_name : 'Tidak ada project';
            })
            ->rawColumns(['check'])
            ->make(true);
    }

    public function get_selected_purchase_request(Request $request)
    {
        $selected_ids = $request->selected_ids ?? [];
        $data = PurchaseRequestDetail::whereIn('id', $selected_ids)
            ->with(['item_data.unit'])
            ->get();

        $purchase_order_detail_items = \App\Models\PurchaseOrderGeneralDetailItem::whereHas('purchase_order_general_detail', function ($p) {
            $p->whereHas('purchase_order_general', function ($q) {
                $q->whereNotIn('status', ['reject', 'void'])
                    ->whereNull('deleted_at');
            });
        })
            ->whereIn('purchase_request_detail_id', $data->pluck('id')->toArray())
            ->whereNotIn('status', ['reject', 'void'])
            ->get();

        $lock_stock = \App\Models\LockStock::whereIn('purchase_request_detail_id', $data->pluck('id')->toArray())
            ->where('status', 'approve')
            ->get();

        $data = $data->map(function ($item) use ($purchase_order_detail_items, $lock_stock) {
            $item->qty_po = $purchase_order_detail_items->where('purchase_request_detail_id', $item->id)->sum('quantity') ?? 0;
            $item->qty_lock = $lock_stock->where('purchase_request_detail_id', $item->id)->sum('quantity') - $lock_stock->where('purchase_request_detail_id', $item->id)->sum('quantity_complete') ?? 0;

            return $item;
        });

        $purchase_requests = PurchaseRequest::whereIn('id', $data->pluck('purchase_request_id'))
            ->get();

        $data = $data->map(function ($d) {
            $d->quantity = 0;
            $d->price = 0;

            return $d;
        });

        $collections = new Collection();
        $purchase_requests->map(function ($purchase_request) use ($data, $collections) {
            $collections->push([
                'parent' => $purchase_request,
                'children' => $data->where('purchase_request_id', $purchase_request->id)->values()->all(),
            ]);
        });

        return response()->json($collections);
    }
}
