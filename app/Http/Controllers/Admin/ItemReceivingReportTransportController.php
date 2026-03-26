<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\PrintHelper;
use App\Models\AccountPayable;
use App\Models\Authorization;
use App\Models\ItemReceivingReportPurchaseTransport;
use App\Models\Price;
use App\Models\SupplierInvoiceDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ItemReceivingReportTransportController extends Controller
{
    use \App\Http\Helpers\ActivityStatusLogHelper;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder|view item-receiving-report-transport", ['only' => ['index', 'show']]);
        $this->middleware("permission:create $this->view_folder|create item-receiving-report-transport", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit $this->view_folder|edit item-receiving-report-transport", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete $this->view_folder|delete item-receiving-report-transport", ['only' => ['destroy']]);
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
                ->join('purchase_transports', 'purchase_transports.id', '=', 'item_receiving_reports.reference_id')
                ->leftJoin('sale_orders', 'sale_orders.id', '=', 'purchase_transports.so_trading_id')
                ->leftJoin('customers', 'customers.id', '=', 'sale_orders.customer_id')
                ->select(['item_receiving_reports.*', 'vendors.nama as vendor_name', 'purchase_transports.kode as reference_code', 'customers.nama as customer_name'])
                ->where('item_receiving_reports.tipe', 'transport')
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
                })
                ->groupBy('item_receiving_reports.id');

            $supplier_invoice_details = SupplierInvoiceDetail::whereIn('item_receiving_report_id', $data->pluck('id'))
                ->whereHas('supplier_invoice', function ($q) {
                    return $q->whereIn('status', ['pending', 'approve', 'revert']);
                })
                ->get();

            $checkAuthorizePrint = authorizePrint('lpb_transport');

            return \Yajra\DataTables\DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date_receive', fn($row) => \Carbon\Carbon::parse($row->date_receive)->format('d-m-Y'))
                ->editColumn('kode', function ($row) use ($checkAuthorizePrint) {
                    return view('components.datatable.detail-link', [
                        'field' => $row->kode,
                        'row' => $row,
                        'main' => 'item-receiving-report-transport',
                        'permission_name' => 'item-receiving-report-transport',

                    ]) . '<br>' .
                        // view("components.datatable.export-button", [
                        //     'route' => route('item-receiving-report-transport.export-pdf', ['id' => encryptId($row->id)]),
                        //     'onclick' => "show_print_out_modal(event)"
                        // ]);
                        view('components.button-auth-print', [
                            'type' => 'lpb_transport',
                            'href' => route('item-receiving-report-transport.export-pdf', ['id' => encryptId($row->id)]),
                            'model' => \App\Models\ItemReceivingReport::class,
                            'did' => $row->id,
                            'link' => route('admin.item-receiving-report-transport.show', $row->id),
                            'code' => $row->kode,
                            'condition' => $checkAuthorizePrint,
                            'size' => 'sm',
                        ])->render();
                })
                ->editColumn('reference_code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->reference->kode,
                    'row' => $row->reference,
                    'main' => 'purchase-order-transport',
                    'permission_name' => 'purchase-transport',
                ]))
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . item_report_status()[$row->status]['color'] . '">
                                            ' . item_report_status()[$row->status]['label'] . ' - ' . item_report_status()[$row->status]['text'] . '
                                        </div>';
                    return $badge;
                })
                ->addColumn('action', function ($row) use ($supplier_invoice_details) {
                    $btn = $row->check_available_date;
                    $check_supplier_invoice = $supplier_invoice_details->where('item_receiving_report_id', $row->id)->count();

                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => 'item-receiving-report-transport',
                        'permission_name' => 'item-receiving-report-transport',
                        'btn_config' => [
                            'detail' => [
                                'display' => false,
                            ],
                            'edit' => [
                                'display' => in_array($row->status, ['pending', 'revert']) && $btn && $check_supplier_invoice == 0,
                            ],
                            'delete' => [
                                'display' => in_array($row->status, ['pending', 'revert']) && $btn && $check_supplier_invoice == 0,
                            ],
                        ],
                    ]);
                })
                ->rawColumns(['action', 'status', 'kode', 'export'])
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
        if (!$authorization->is_authoirization_exist(\App\Models\ItemReceivingReport::class, 'transport')) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

        return view("admin.item-receiving-report.create.transport");
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
            'file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5000',
        ]);

        // ? purchase data
        $purchase_order = \App\Models\PurchaseTransport::find($request->purchase_transport_id);

        $purchaseDate = \Carbon\Carbon::parse($purchase_order->target_delivery);
        $itemReceivingDate = \Carbon\Carbon::parse($request->date_receive);

        if ($purchaseDate->gt($itemReceivingDate)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", null, 'Tanggal diterima tidak boleh kurang dari tanggal PO'));
        }

        DB::beginTransaction();

        try {
            $last = \App\Models\ItemReceivingReport::withTrashed()
                ->where('tipe', 'transport')
                ->whereMonth('date_receive', Carbon::parse($request->date_receive))
                ->whereYear('date_receive', Carbon::parse($request->date_receive))
                ->orderBy('id', 'desc')
                ->first();

            $code = generate_code_transaction("BASTTP", $last?->kode, date: $request->date_receive, branch_sort: $purchase_order->branch->sort);

            // * create report
            $item_report = new \App\Models\ItemReceivingReport();
            $item_report->loadModel([
                'kode' => $code,
                'tipe' => 'transport',
                'reference_model' => \App\Models\PurchaseTransport::class,
                'reference_id' => $request->purchase_transport_id,
                'vendor_id' => $purchase_order->vendor_id,
                'currency_id' => $purchase_order->currency_id,
                'exchange_rate' => $purchase_order->exchange_rate,
                'date_receive' => Carbon::parse($request->date_receive),
                'date_receive_time' => Carbon::now()->format("H:i:s"),
                'branch_id' => $request->branch_id,
                'due_date' => Carbon::parse($request->date_receive)->addDays($purchase_order->vendor->top_days),
            ]);

            if ($request->hasFile('file')) {
                $item_report->file = $this->upload_file($request->file('file'), 'item-receiving-report-general');
            }
            $item_report->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $item_report->branch_id,
                user_id: auth()->user()->id,
                model: \App\Models\ItemReceivingReport::class,
                model_id: $item_report->id,
                amount: 0,
                title: "LPB Transport",
                subtitle: Auth::user()->name . " mengajukan LPB Transport " . $item_report->kode,
                link: route('admin.item-receiving-report-transport.show', $item_report),
                update_status_link: route('admin.item-receiving-report-transport.update-status', ['id' => $item_report->id]),
                type: 'transport',
                division_id: auth()->user()->division_id ?? null
            );

            // * create report details
            $item_report_detail = new \App\Models\ItemReceivingReportPurchaseTransport();
            $item_report_detail->loadModel([
                'item_receiving_report_id' => $item_report->id,
                'sended' => 0,
                'received' => 0,
                'item_id' => $purchase_order->item_id,
                'loss_tolerance' => thousand_to_float($request->loss_tolerance),
                'lost_discount' => thousand_to_float($request->lost_discount ?? 0),
                'tax_option' => $request->tax_option,
            ]);
            $item_report_detail->save();

            $sended = 0;
            $received = 0;

            // * create report detail delivery order
            if (!empty($request->delivery_order)) {
                foreach ($request->delivery_order as $delivery_order_key => $delivery_order) {
                    if ($delivery_order == 'on') {
                        $delivery_order_data = \App\Models\DeliveryOrder::find($request->delivery_order_id[$delivery_order_key]);

                        $item_report_detail_delivery_order = new \App\Models\ItemReceivingReportPurchaseTransportDetail();
                        $item_report_detail_delivery_order->loadModel([
                            'item_receiving_report_purchase_transport_id' => $item_report_detail->id,
                            'delivery_order_id' => $delivery_order_data->id,
                            'sended' => $delivery_order_data->load_quantity_realization,
                            'received' => $delivery_order_data->unload_quantity_realization,
                        ]);
                        $item_report_detail_delivery_order->save();

                        $sended += $delivery_order_data->load_quantity_realization;
                        $received += $delivery_order_data->unload_quantity_realization;
                    }
                }
            } else {
                foreach ($purchase_order->purchase_transport_details as $purchase_transport_detail) {
                    $item_report_transport_detail = new \App\Models\ItemReceivingReportPurchaseTransportDetail();
                    $item_report_transport_detail->loadModel([
                        'item_receiving_report_purchase_transport_id' => $item_report_detail->id,
                        'delivery_order_id' => null,
                        'sended' => $purchase_transport_detail->jumlah * $purchase_transport_detail->jumlah_do,
                        'received' => $purchase_transport_detail->jumlah * $purchase_transport_detail->jumlah_do,
                    ]);
                    $item_report_transport_detail->save();

                    $sended += $purchase_transport_detail->jumlah * $purchase_transport_detail->jumlah_do;
                    $received += $purchase_transport_detail->jumlah * $purchase_transport_detail->jumlah_do;
                }
            }

            // * update sended and received
            $item_report_detail->sended = $sended;
            $item_report_detail->received = $received;
            $item_report_detail->save();

            // * create coa
            $lpb_coa = new \App\Http\Helpers\ItemReceivingReportCoaHelpers($item_report->tipe, $item_report->reference_id, $item_report->id);
            $lpb_coa->create_item_receiving_report_coa();

            // * observer
            $item_report->observerAfterCreate();

            DB::commit();

            return redirect()->route('admin.item-receiving-report.index')->with($this->ResponseMessageCRUD(true, 'create', 'report'));
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

        return view("admin.item-receiving-report.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'auth_revert_void_button'));
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
        return view("admin.item-receiving-report.edit.transport", compact('model'));
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

        DB::beginTransaction();

        try {
            // ? purchase data
            $purchase_order = \App\Models\PurchaseTransport::find($request->purchase_transport_id);

            $purchaseDate = \Carbon\Carbon::parse($purchase_order->target_delivery);
            $itemReceivingDate = \Carbon\Carbon::parse($request->date_receive);

            if ($purchaseDate->gt($itemReceivingDate)) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", null, 'Tanggal diterima tidak boleh kurang dari tanggal PO'));
            }

            // * update report
            $item_report = \App\Models\ItemReceivingReport::find($id);
            $item_report->loadModel([
                'tipe' => 'transport',
                'reference_model' => \App\Models\PurchaseTransport::class,
                'reference_id' => $request->purchase_transport_id,
                'vendor_id' => $purchase_order->vendor_id,
                'currency_id' => $purchase_order->currency_id,
                'exchange_rate' => $purchase_order->exchange_rate,
                'date_receive' => Carbon::parse($request->date_receive),
                'date_receive_time' => Carbon::now()->format("H:i:s"),
                'branch_id' => $request->branch_id,
                'due_date' => Carbon::parse($request->date_receive)->addDays($purchase_order->vendor->top_days),
            ]);

            $item_report->rollbackObserverAfterCreate();

            if ($request->hasFile('file')) {
                Storage::delete($item_report->file);
                $item_report->file = $this->upload_file($request->file('file'), 'item-receiving-report-general');
            }
            $item_report->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $item_report->branch_id,
                user_id: auth()->user()->id,
                model: \App\Models\ItemReceivingReport::class,
                model_id: $item_report->id,
                amount: 0,
                title: "LPB Transport",
                subtitle: Auth::user()->name . " mengajukan LPB Transport " . $item_report->kode,
                link: route('admin.item-receiving-report-transport.show', $item_report),
                update_status_link: route('admin.item-receiving-report-transport.update-status', ['id' => $item_report->id]),
                type: 'transport',
                division_id: auth()->user()->division_id ?? null
            );

            // * update report details
            $item_report_detail = ItemReceivingReportPurchaseTransport::find($item_report->item_receiving_report_purchase_transport->id);
            $item_report_detail->loadModel([
                'item_receiving_report_id' => $item_report->id,
                'sended' => 0,
                'received' => 0,
                'item_id' => $purchase_order->item_id,
                'loss_tolerance' => thousand_to_float($request->loss_tolerance),
                'lost_discount' => thousand_to_float($request->lost_discount ?? 0),
                'tax_option' => $request->tax_option,
            ]);
            $item_report_detail->save();

            $sended = 0;
            $received = 0;

            // Hapus detail sebelumnya
            \App\Models\ItemReceivingReportPurchaseTransportDetail::where('item_receiving_report_purchase_transport_id', $item_report_detail->id)->get()
                ->each(function ($item) {
                    if ($item->delivery_order) {
                        $delivery_order = $item->delivery_order;
                        $delivery_order->is_item_receiving_report_created = false;
                        $delivery_order->save();
                    }
                    $item->delete();
                });

            // * create report detail delivery order
            if (!empty($request->delivery_order)) {
                foreach ($request->delivery_order as $delivery_order_key => $delivery_order) {
                    if ($delivery_order == 'on') {
                        $delivery_order_data = \App\Models\DeliveryOrder::find($request->delivery_order_id[$delivery_order_key]);

                        $item_report_detail_delivery_order = new \App\Models\ItemReceivingReportPurchaseTransportDetail();
                        $item_report_detail_delivery_order->loadModel([
                            'item_receiving_report_purchase_transport_id' => $item_report_detail->id,
                            'delivery_order_id' => $delivery_order_data->id,
                            'sended' => $delivery_order_data->load_quantity_realization,
                            'received' => $delivery_order_data->unload_quantity_realization,
                        ]);
                        $item_report_detail_delivery_order->save();

                        $sended += $delivery_order_data->load_quantity_realization;
                        $received += $delivery_order_data->unload_quantity_realization;
                    }
                }
            } else {
                foreach ($purchase_order->purchase_transport_details as $purchase_transport_detail) {
                    $item_report_transport_detail = new \App\Models\ItemReceivingReportPurchaseTransportDetail();
                    $item_report_transport_detail->loadModel([
                        'item_receiving_report_purchase_transport_id' => $item_report_detail->id,
                        'delivery_order_id' => null,
                        'sended' => $purchase_transport_detail->jumlah * $purchase_transport_detail->jumlah_do,
                        'received' => $purchase_transport_detail->jumlah * $purchase_transport_detail->jumlah_do,
                    ]);
                    $item_report_transport_detail->save();

                    $sended += $purchase_transport_detail->jumlah * $purchase_transport_detail->jumlah_do;
                    $received += $purchase_transport_detail->jumlah * $purchase_transport_detail->jumlah_do;
                }
            }

            // * update sended and received
            $item_report_detail->sended = $sended;
            $item_report_detail->received = $received;
            $item_report_detail->save();

            // * create coa
            $lpb_coa = new \App\Http\Helpers\ItemReceivingReportCoaHelpers($item_report->tipe, $item_report->reference_id, $item_report->id);
            $lpb_coa->create_item_receiving_report_coa();

            // * observer
            $item_report->observerAfterCreate();

            DB::commit();

            return redirect()->route('admin.item-receiving-report.index')->with($this->ResponseMessageCRUD(true, 'create', 'report'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
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

            Authorization::where('model', \App\Models\ItemReceivingReport::class)
                ->where('model_id', $model->id)
                ->delete();
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
                    $price = new Price();
                    $price->item_id = $model->reference->item_id;
                    $price->harga_beli = $model->reference->harga * $model->reference?->exchange_rate  ?? 1;

                    try {
                        $price->save();
                    } catch (\Throwable $th) {
                        throw $th;
                    }

                    $model->price_id = $price->id;
                    $model->save();
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

        return redirect()->route("admin.item-receiving-report-transport.show", $model)->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    /**
     * Export PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function exportPdf($id, Request $request)
    {
        if (!$request->preview && authorizePrint('lpb_transport')) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                \App\Models\ItemReceivingReport::class,
                decryptId($id),
                'lpb_transport',
            );

            if (!$result) {
                return abort(403);
            }
        }

        $model = \App\Models\ItemReceivingReport::query()
            ->with(['item_receiving_report_purchase_transport.item_receiving_report_purchase_transport_details.delivery_order'])
            ->findOrFail(decryptId($id));

        $qr_url = route('item-receiving-report-transport.export-pdf', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $approval = Authorization::where('model', \App\Models\ItemReceivingReport::class)
            ->with(['details' => function ($q) {
                $q->where('status', 'approve')
                    ->where('note', 'not like', '%otomatis%');
            }])
            ->where('model_id', $model->id)
            ->first();

        $pdf = Pdf::loadView("admin.$this->view_folder.pdf.transport", compact('model', 'qr', 'approval'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'landscape');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $w = $canvas->get_width();
        $h = $canvas->get_height();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM} / {PAGE_COUNT}", '', 8);

        if ($request->preview) {
            Storage::disk('public')->deleteDirectory('tmp_lpb_transport');
            $tmp_file_name = 'lpb_transport_' . time() . '.pdf';
            $path = 'tmp_lpb_transport/' . $tmp_file_name;
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

        return $this->ResponseJsonData($model);
    }

    public function history($id, Request $request)
    {
        try {
            $item_receiving_reports = DB::table('item_receiving_reports')
                ->where('id', $id)
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

                $item->date = localDate($item->date);
                $item->link = route('admin.' . $item_type . '.show', $item->id);
                $item->menu = 'penerimaan barang ' . $item->tipe;
                return $item;
            });

            $purchase_transports = DB::table('purchase_transports')
                ->whereIn('id', $item_receiving_reports->pluck('reference_id')->toArray())
                ->select(
                    'purchase_transports.id',
                    'purchase_transports.kode as code',
                    'purchase_transports.target_delivery as date',
                    'purchase_transports.status',
                )
                ->get();

            $purchase_transports = $purchase_transports->map(function ($item) {
                $item->date = localDate($item->date);
                $item->link = route('admin.purchase-order-transport.show', $item->id);
                $item->menu = 'purchase order transport';
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

            $histories = $purchase_transports->unique('id')
                ->merge($item_receiving_reports->unique('id'))
                ->merge($supplier_invoices->unique('id'))
                ->merge($fund_submissions->unique('id'))
                ->merge($account_payables->unique('id'))
                ->merge($purchase_returns->unique('id'));
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
