<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Http\Helpers\PrintHelper;
use App\Models\AccountPayable;
use App\Models\Authorization;
use App\Models\Branch;
use App\Models\ItemReceivingReportCoa;
use App\Models\Lease;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ItemReceivingReportServiceController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder|view item-receiving-report-service", ['only' => ['index', 'show']]);
        $this->middleware("permission:create $this->view_folder|create item-receiving-report-service", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit $this->view_folder|edit item-receiving-report-service", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete $this->view_folder|delete item-receiving-report-service", ['only' => ['destroy']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'item-receiving-report';

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = \App\Models\ItemReceivingReport::with(['vendor'])
                ->join('vendors', 'vendors.id', '=', 'item_receiving_reports.vendor_id')
                ->join('purchase_order_services', 'purchase_order_services.id', '=', 'item_receiving_reports.reference_id')
                ->select(['item_receiving_reports.*', 'vendors.nama as vendor_name', 'purchase_order_services.code as reference_code'])
                ->where('tipe', 'jasa')
                ->when(get_current_branch()->is_primary && $request->branch_id, function ($query) use ($request) {
                    return $query->where('item_receiving_reports.branch_id', $request->branch_id);
                })
                ->when(!get_current_branch()->is_primary, function ($query) {
                    return $query->where('item_receiving_reports.branch_id', get_current_branch()->id);
                })
                ->when($request->vendor_id, function ($query) use ($request) {
                    return $query->where('item_receiving_reports.vendor_id', $request->vendor_id);
                })
                ->when($request->status, function ($query) use ($request) {
                    return $query->where('item_receiving_reports.status', $request->status);
                })
                ->when($request->from_date, function ($query) use ($request) {
                    return $query->whereDate('item_receiving_reports.date_receive', '>=', \Carbon\Carbon::parse($request->from_date));
                })
                ->when($request->to_date, function ($query) use ($request) {
                    return $query->whereDate('item_receiving_reports.date_receive', '<=', \Carbon\Carbon::parse($request->to_date));
                });
            $checkAuthorizePrint = authorizePrint('lpb_service');

            return \Yajra\DataTables\DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date_receive', fn($row) => \Carbon\Carbon::parse($row->date_receive)->format('d-m-Y'))
                ->editColumn('kode', function ($row) use ($checkAuthorizePrint) {
                    return view('components.datatable.detail-link', [
                        'field' => $row->kode,
                        'row' => $row,
                        'main' => 'item-receiving-report-service',
                        'permission_name' => 'item-receiving-report-service',

                    ]) . '<br>' .
                        // view("components.datatable.export-button", [
                        //     'route' => route('item-receiving-report-service.export-pdf', ['id' => encryptId($row->id)]),
                        //     'onclick' => "show_print_out_modal(event)"
                        // ]);
                        view('components.button-auth-print', [
                            'type' => 'lpb_service',
                            'href' => route('item-receiving-report-service.export-pdf', ['id' => encryptId($row->id)]),
                            'model' => \App\Models\ItemReceivingReport::class,
                            'did' => $row->id,
                            'link' => route('admin.item-receiving-report-service.show', ['item_receiving_report_service' => $row->id]),
                            'code' => $row->kode,
                            'condition' => $checkAuthorizePrint,
                            'size' => 'sm',
                        ])->render();
                })
                ->editColumn('reference_code', fn($row) => view('components.datatable.detail-link', [
                    'field' => ($row->reference->spk_number ? $row->reference->spk_number . ' / <br> ' : '') . $row->reference->code,
                    'row' => $row->reference,
                    'main' => 'purchase-order-service',
                    'permission_name' => 'purchase-service',
                ]))
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . item_report_status()[$row->status]['color'] . '">
                                            ' . item_report_status()[$row->status]['label'] . ' - ' . item_report_status()[$row->status]['text'] . '
                                        </div>';
                    return $badge;
                })
                ->addColumn('action', function ($row) {
                    $btn = $row->check_available_date;

                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => 'item-receiving-report-service',
                        'permission_name' => 'item-receiving-report-service',
                        'btn_config' => [
                            'detail' => [
                                'display' => false,
                            ],
                            'edit' => [
                                'display' => $row->check_available_date ? in_array($row->status, ['pending', 'revert']) && $btn : false,
                            ],
                            'delete' => [
                                'display' => $row->check_available_date ? in_array($row->status, ['pending', 'revert']) && $btn : false,
                            ],
                        ],
                    ]);
                })
                ->rawColumns(['action', 'status', 'kode', 'export', 'reference_code'])
                ->make(true);
        }

        abort(403);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(\App\Models\ItemReceivingReport::class, 'service')) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }
        return view("admin.item-receiving-report.create.service");
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
            'purchase_order_service_id' => 'required|exists:purchase_order_services,id',
            'date_receive' => 'required|date',
            'reference_id' => 'required|exists:purchase_order_service_detail_items,id',
            'file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5000',
        ]);

        // Check main quantity not NaN and 0
        if (is_array($request->jumlah_diterima)) {
            foreach ($request->jumlah_diterima as $quantity) {
                if ($quantity == "NaN") {
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", null, 'Jumlah tidak boleh string'));
                }
            }
        }

        $purchase_order = \App\Models\PurchaseOrderService::findOrFail($request->purchase_order_service_id);

        $purchaseDate = \Carbon\Carbon::parse($purchase_order->date);
        $itemReceivingDate = \Carbon\Carbon::parse($request->date_receive);

        if ($purchaseDate->gt($itemReceivingDate)) {
            return redirect()->back()->with('error', 'Tanggal diterima tidak boleh kurang dari tanggal PO');
        }

        $last = \App\Models\ItemReceivingReport::where('tipe', 'jasa')
            ->withTrashed()
            ->whereMonth('date_receive', Carbon::parse($request->date_receive))
            ->whereYear('date_receive', Carbon::parse($request->date_receive))
            ->orderBy('id', 'desc')
            ->first();

        DB::beginTransaction();

        try {
            // * create report
            $model = new \App\Models\ItemReceivingReport();
            $model->fill([
                'tipe' => 'jasa',
                'kode' => generate_code_transaction("BASTS", $last?->kode, date: $request->date_receive, branch_sort: $purchase_order->branch->sort ?? null),
                'reference_model' => \App\Models\PurchaseOrderService::class,
                'reference_id' => $request->purchase_order_service_id,
                'vendor_id' => $purchase_order->vendor_id,
                'date_receive' => Carbon::parse($request->date_receive),
                'date_receive_time' => Carbon::now()->format("H:i:s"),
                'currency_id' => $purchase_order->currency_id,
                'exchange_rate' => $purchase_order->exchange_rate,
                'branch_id' => $request->branch_id,
                'due_date' => Carbon::parse($request->date_receive)->addDays($purchase_order->term_of_payment_days),
            ]);

            if ($request->hasFile('file')) {
                $model->file = $this->upload_file($request->file('file'), 'item-receiving-report-service');
            }
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: \App\Models\ItemReceivingReport::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "LPB Service",
                subtitle: Auth::user()->name . " mengajukan LPB Service " . $model->kode,
                link: route('admin.item-receiving-report-service.show', $model),
                update_status_link: route('admin.item-receiving-report-service.update-status', ['id' => $model->id]),
                type: 'service',
                division_id: auth()->user()->division_id ?? null
            );

            // * create report details
            $data_details = [];
            foreach ($request->jumlah_diterima as $key => $value) {
                if (thousand_to_float($value ?? 0) > 0) {
                    $po_detail = \App\Models\PurchaseOrderServiceDetailItem::find($request->reference_id[$key]);
                    $data_details[] = [
                        'item_id' => $po_detail->item_id,
                        'item_receiving_report_id' => $model->id,
                        'jumlah_diterima' => thousand_to_float($value ?? 0),
                        'reference_id' => $request->reference_id[$key],
                        'reference_model' => \App\Models\PurchaseOrderServiceDetailItem::class,
                    ];
                }
            }

            if (count($data_details) > 0) {
                \App\Models\ItemReceivingReportDetail::insert($data_details);
            }

            // * create coa
            $lpb_coa = new \App\Http\Helpers\ItemReceivingReportCoaHelpers($model->tipe, $model->reference_id, $model->id);
            $lpb_coa->create_item_receiving_report_coa();

            // * observer
            $model->observerAfterCreate();

            DB::commit();


            return redirect()->route('admin.item-receiving-report.index')->with($this->ResponseMessageCRUD(true, 'create'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = \App\Models\ItemReceivingReport::findOrFail($id);

        validate_branch($model->branch_id);
        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $purchase_requests = $model->reference->purchaseOrderServiceDetails->pluck('purchase_request_id');
        $project_id = \App\Models\PurchaseRequest::whereIn('id', $purchase_requests)->pluck('project_id');
        $projects = \App\Models\Project::whereIn('id', $project_id)->get();

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: \App\Models\ItemReceivingReport::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );

        $is_has_invoice = ItemReceivingReportController::hasInvoice($model->id);

        $authorization_logs['can_revert'] = $model->check_available_date && $model->status == 'approve';
        $authorization_logs['can_void'] = $model->check_available_date && $model->status == 'approve' && !$is_has_invoice;
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = $model->check_available_date && $model->status == 'approve';
        $authorization_logs['can_void_request'] = $model->check_available_date && $model->status == 'approve' && !$is_has_invoice;
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.item-receiving-report.show", compact('model', 'status_logs', 'activity_logs', 'projects', 'authorization_log_view', 'auth_revert_void_button', 'projects'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = \App\Models\ItemReceivingReport::findOrFail($id);

        if (!$model->check_available_date) {
            abort(403);
        }

        if (!in_array($model->status, ['pending', 'revert']) || !$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', 'status'));
        }

        return view("admin.item-receiving-report.edit.service", compact('model'));
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
            'file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5000',
        ]);

        $model = \App\Models\ItemReceivingReport::with('item_receiving_report_details')
            ->findOrFail($id);

        if (!in_array($model->status, ['pending', 'revert']) || !$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', 'status'));
        }

        // Check main quantity not NaN and 0
        if (is_array($request->jumlah_diterima)) {
            foreach ($request->jumlah_diterima as $quantity) {
                if ($quantity == "NaN") {
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", null, 'Jumlah tidak boleh string'));
                }
            }
        }

        $purchase_order = \App\Models\PurchaseOrderService::find($model->reference_id);
        $purchaseDate = \Carbon\Carbon::parse($purchase_order->date);
        $itemReceivingDate = \Carbon\Carbon::parse($request->date_receive);

        if ($purchaseDate->gt($itemReceivingDate)) {
            return redirect()->back()->with('error', 'Tanggal diterima tidak boleh kurang dari tanggal PO');
        }

        DB::beginTransaction();

        try {
            $model->rollbackObserverAfterCreate();

            // Update report
            $model->fill([
                'date_receive' => Carbon::parse($request->date_receive),
                'due_date' => Carbon::parse($request->date_receive)->addDays($purchase_order->term_of_payment_days),
            ]);

            if ($model->status == 'revert') {
                $model->kode = generate_code_update($model->kode);
            }

            if ($request->hasFile('file')) {
                Storage::delete($model->file);
                $model->file = $this->upload_file($request->file('file'), 'item-receiving-report-service');
            }
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: \App\Models\ItemReceivingReport::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "LPB Service",
                subtitle: Auth::user()->name . " mengajukan LPB Service " . $model->kode,
                link: route('admin.item-receiving-report-service.show', $model),
                update_status_link: route('admin.item-receiving-report-service.update-status', ['id' => $model->id]),
                type: 'service',
                division_id: auth()->user()->division_id ?? null
            );

            // Update report details
            foreach ($request->jumlah_diterima as $key => $value) {
                $detail = $model->item_receiving_report_details->where('id', $request->item_receiving_report_detail_id[$key])->first();
                $detail->update([
                    'jumlah_diterima' => thousand_to_float($value ?? 0)
                ]);
            }

            // Update COA
            $model = \App\Models\ItemReceivingReport::find($id);
            ItemReceivingReportCoa::where('item_receiving_report_id', $model->id)->delete();
            $lpb_coa = new \App\Http\Helpers\ItemReceivingReportCoaHelpers($model->tipe, $model->reference_id, $model->id);
            $lpb_coa->create_item_receiving_report_coa();

            // Observer
            $model->observerAfterCreate();

            DB::commit();

            return redirect()->route('admin.item-receiving-report-service.show', $model)->with($this->ResponseMessageCRUD(true, 'edit'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = \App\Models\ItemReceivingReport::findOrFail($id);

        if (!in_array($model->status, ['pending', 'revert']) || !$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', 'status'));
        }

        DB::beginTransaction();

        $model->rollbackObserverAfterCreate();

        // * delete report
        try {
            $model->delete();

            Authorization::where('model', \App\Models\ItemReceivingReport::class)->where('model_id', $model->id)->delete();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route('admin.item-receiving-report.index')->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * update status
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        DB::beginTransaction();

        $model = \App\Models\ItemReceivingReport::findOrFail($id);
        validate_branch($model->branch_id);

        // * saving and make response
        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            $is_has_invoice = ItemReceivingReportController::hasInvoice($model->id);

            if ($request->status == 'void' && $is_has_invoice) {
                throw new \Exception('LPB tidak dapat di void karena sudah terdapat invoice terkait');
            }

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(\App\Models\ItemReceivingReport::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);

                $model->status = $request->status;
                $model->save();

                if ($model->status == 'approve') {
                    foreach ($model->item_receiving_report_details as $key => $value) {
                        $item = $value->item;
                        $reference = $value->reference_model::find($value->reference_id);
                        $purchase_order_service_detail = $reference->purchase_order_service_detail;
                        $purchase_order = $purchase_order_service_detail->purchase_order_service;
                        $exchange_rate = $purchase_order->exchange_rate;

                        if ($item->item_category->item_type->nama == 'biaya dibayar dimuka') {
                            $lease_coa = $item->item_category->item_category_coas
                                ->filter(function ($query) {
                                    return strtolower($query->type) == 'biaya dibayar dimuka';
                                })
                                ->first();

                            $branch = Branch::find($model->branch_id);
                            for ($i = 0; $i < $value->jumlah_diterima; $i++) {
                                $lease = new \App\Models\Lease();
                                $lease->loadModel([
                                    'code' => generate_code(Lease::class, 'code', 'created_at', 'BDM', branch_sort: $branch->sort ?? null, date: $model->date_receive),
                                    'item_receiving_report_detail_id' =>  $value->id,
                                    'item_id' => $item->id,
                                    'division_id' => $reference->purchase_request_detail->purchase_request->division_id,
                                    'date' => $model->date_receive,
                                    'lease_name' => $item->nama,
                                    'value' => $reference->price * $exchange_rate,
                                    'status' => 'pending',
                                    'asset_coa_id' => $lease_coa->coa_id,
                                ]);

                                try {
                                    $lease->save();
                                } catch (\Throwable $th) {
                                    DB::rollBack();

                                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
                                }
                            }
                        }

                        $price = new \App\Models\Price();
                        $price->item_id = $value->reference->item_id;
                        $price->harga_beli = $value->reference->price * $model->reference?->exchange_rate  ?? 1;

                        try {
                            $price->save();
                        } catch (\Throwable $th) {
                            throw $th;
                        }

                        $value->price_id = $price->id;
                        $value->save();
                    }
                }
            } else {
                $this->create_activity_status_log(\App\Models\ItemReceivingReport::class, $id, $request->note ?? $request->message ?? 'message not available', null, $request->status);
            }

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.item-receiving-report-service.show", $model)->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    /**
     * Export PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function exportPdf($id, Request $request)
    {
        if (!$request->preview && authorizePrint('lpb_service')) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                \App\Models\ItemReceivingReport::class,
                decryptId($id),
                'lpb_service',
            );

            if (!$result) {
                return abort(403);
            }
        }

        $model = \App\Models\ItemReceivingReport::findOrFail(decryptId($id));

        $purchase_requests = $model->reference->purchaseOrderServiceDetails->pluck('purchase_request_id');
        $project_id = \App\Models\PurchaseRequest::whereIn('id', $purchase_requests)->pluck('project_id');
        $projects = \App\Models\Project::whereIn('id', $project_id)->get();

        $qr_url = route('item-receiving-report-service.export-pdf', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $approval = Authorization::where('model', \App\Models\ItemReceivingReport::class)
            ->with(['details' => function ($q) {
                $q->where('status', 'approve')
                    ->where('note', 'not like', '%otomatis%');
            }])
            ->where('model_id', $model->id)
            ->first();

        $pdf = Pdf::loadView("admin.$this->view_folder.pdf.jasa", compact('model', 'qr', 'approval', 'projects'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'landscape');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $w = $canvas->get_width();
        $h = $canvas->get_height();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM} / {PAGE_COUNT}", '', 8);

        if ($request->preview) {
            Storage::disk('public')->deleteDirectory('tmp_lpb_service');
            $tmp_file_name = 'lpb_service_' . time() . '.pdf';
            $path = 'tmp_lpb_service/' . $tmp_file_name;
            Storage::disk('public')->put($path, $pdf->output());

            return response()->json($path);
        }

        return $pdf->stream('LPB-' . $model->kode . '.pdf');
    }

    /**
     * Api detail for edit data
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiDetailForEditData($id)
    {
        $model = \App\Models\ItemReceivingReport::findOrFail($id);
        $model_detail = \App\Models\ItemReceivingReportDetail::with(['item.unit'])->where('item_receiving_report_id', $model->id)->get();
        $purchase_order_detail_item = \App\Models\PurchaseOrderServiceDetailItem::with(['item.unit'])->whereIn('id', $model_detail->pluck('reference_id')->toArray())->get();

        $results = $model_detail->map(function ($detail) use ($purchase_order_detail_item) {
            $detail->reference = $purchase_order_detail_item->where('id', $detail->reference_id)->first();

            return $detail;
        });

        return $this->ResponseJsonData($results);
    }

    public function history($id, Request $request)
    {
        try {
            $item_receiving_reports = DB::table('item_receiving_reports')
                ->where('id', $id)
                ->whereNull('item_receiving_reports.deleted_at')
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

                $item->date = localDate($item->date);
                $item->link = route('admin.' . $item_type . '.show', $item->id);
                $item->menu = 'penerimaan barang ' . $item->tipe;
                return $item;
            });

            $purchase_order_services = DB::table('purchase_order_service_details')
                ->join('purchase_order_services', 'purchase_order_services.id', '=', 'purchase_order_service_details.purchase_order_service_id')
                ->whereNull('purchase_order_services.deleted_at')
                ->whereIn('purchase_order_services.id', $item_receiving_reports->pluck('reference_id')->toArray())
                ->select(
                    'purchase_order_services.id',
                    'purchase_order_services.code',
                    'purchase_order_services.date',
                    'purchase_order_services.status',
                    'purchase_order_service_details.purchase_request_id',
                )
                ->get();

            $purchase_order_services = $purchase_order_services->map(function ($item) {
                $item->date = localDate($item->date);
                $item->link = route('admin.purchase-order-service.show', $item->id);
                $item->menu = 'purchase order service';
                return $item;
            });

            $purchase_orders = DB::table('purchase_orders')
                ->whereIn('id', $item_receiving_reports
                    ->where('reference_model', 'App\Models\PoTrading')
                    ->pluck('reference_id')->toArray())
                ->select(
                    'purchase_orders.id',
                    'purchase_orders.nomor_po as code',
                    'purchase_orders.tanggal as date',
                    'purchase_orders.status',
                )
                ->get();

            $purchase_orders = $purchase_orders->map(function ($item) {
                $item->date = localDate($item->date);
                $item->link = route('admin.purchase-order.show', $item->id);
                $item->menu = 'purchase order trading';
                return $item;
            });


            $purhase_requests = DB::table('purchase_requests')
                ->whereIn('id', $purchase_order_services->pluck('purchase_request_id')->toArray())
                ->whereNull('deleted_at')
                ->whereIn('status', ['approve', 'done', 'partial'])
                ->select(
                    'id',
                    'kode as code',
                    'tanggal as date'
                )
                ->get();

            $purhase_requests = $purhase_requests->map(function ($item) {
                $item->date = localDate($item->date);
                $item->link = route('admin.purchase-request.show', $item->id);
                $item->menu = 'purchase request';
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
                $item->date = localDate($item->date);
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
                $item->date = localDate($item->date);
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
                $item->date = localDate($item->date);
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
                $item->date = localDate($item->date);
                $item->link = route('admin.account-payable.show', $item->id);
                $item->menu = 'pelunasan hutang';
                return $item;
            });

            $histories = $purhase_requests->unique('id')
                ->merge($purchase_order_services->unique('id'))
                ->merge($item_receiving_reports->unique('id'))
                ->merge($supplier_invoices->unique('id'))
                ->merge($fund_submissions->unique('id'))
                ->merge($account_payables->unique('id'))
                ->merge($purchase_returns)->unique('id');
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
}
