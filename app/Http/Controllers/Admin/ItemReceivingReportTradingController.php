<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Http\Helpers\PrintHelper;
use App\Models\AccountPayable;
use App\Models\Authorization;
use App\Models\ItemCategoryCoa;
use App\Models\ItemReceivingReport;
use App\Models\ItemReceivingReportCoa;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ItemReceivingReportTradingController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder|view item-receiving-report-trading", ['only' => ['index', 'show']]);
        $this->middleware("permission:create $this->view_folder|create item-receiving-report-trading", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit $this->view_folder|edit item-receiving-report-trading", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete $this->view_folder|delete item-receiving-report-trading", ['only' => ['destroy']]);
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
                ->join('purchase_orders', 'purchase_orders.id', '=', 'item_receiving_reports.reference_id')
                ->select(['item_receiving_reports.*', 'vendors.nama as vendor_name', 'purchase_orders.nomor_po as nomor_po'])
                ->where('tipe', 'trading')
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

            $checkAuthorizePrint = authorizePrint('lpb_trading');

            return \Yajra\DataTables\DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date_receive', fn($row) => \Carbon\Carbon::parse($row->date_receive)->format('d-m-Y'))
                ->editColumn('kode', function ($row) use ($checkAuthorizePrint) {
                    return view('components.datatable.detail-link', [
                        'field' => $row->kode,
                        'row' => $row,
                        'main' => 'item-receiving-report-trading',
                        'permission_name' => 'item-receiving-report-trading',

                    ]) . '<br>' .
                        // view("components.datatable.export-button", [
                        //     'route' => route('item-receiving-report-trading.export-pdf', ['id' => encryptId($row->id)]),
                        //     'onclick' => "show_print_out_modal(event)"
                        // ]);
                        view('components.button-auth-print', [
                            'type' => 'lpb_trading',
                            'href' => route('item-receiving-report-trading.export-pdf', ['id' => encryptId($row->id)]),
                            'model' => \App\Models\ItemReceivingReport::class,
                            'did' => $row->id,
                            'link' => route('admin.item-receiving-report-trading.show', $row->id),
                            'code' => $row->kode,
                            'condition' => $checkAuthorizePrint,
                            'size' => 'sm',
                        ])->render();
                })
                ->editColumn('reference_code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->reference->nomor_po,
                    'row' => $row->reference,
                    'main' => 'purchase-order',
                    'permission_name' => 'purchase-order',
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
                        'main' => 'item-receiving-report-trading',
                        'permission_name' => 'item-receiving-report-trading',
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
        if (!$authorization->is_authoirization_exist(ItemReceivingReport::class, 'trading')) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

        return view("admin.item-receiving-report.create.trading");
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
            'purchase_order_trading_id' => 'required|exists:purchase_orders,id',
            'date_receive' => 'required|date',
            'file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5000',
        ]);

        $purchase_order = \App\Models\PoTrading::findOrFail($request->purchase_order_trading_id);

        $purchaseDate = \Carbon\Carbon::parse($purchase_order->tanggal);
        $itemReceivingDate = \Carbon\Carbon::parse($request->date_receive);

        if ($purchaseDate->gt($itemReceivingDate)) {
            return redirect()->back()->with('error', 'Tanggal diterima tidak boleh kurang dari tanggal PO');
        }

        DB::beginTransaction();

        try {
            // * create report detail
            $code2 = \App\Models\WareHouse::find($request->ware_house_id);

            // * create report
            $model = new \App\Models\ItemReceivingReport();
            $model->loadModel([
                'kode' => generate_code_with_cus_name(
                    model: \App\Models\ItemReceivingReport::class,
                    code: 'LPB',
                    code2: $code2,
                    date_column: 'date_receive',
                    date: $request->date_receive ?? \Carbon\Carbon::now()->format('Y-m-d'),
                    code3: '',
                    filter: [
                        ['tipe', '=', 'trading'],
                    ]
                ),
                'tipe' => 'trading',
                'reference_model' => \App\Models\PoTrading::class,
                'reference_id' => $request->purchase_order_trading_id,
                'ware_house_id' => $request->ware_house_id,
                'vendor_id' => $purchase_order->vendor_id,
                'date_receive' => Carbon::parse($request->date_receive),
                'date_receive_time' => Carbon::now()->format("H:i:s"),
                'currency_id' => $purchase_order->currency_id,
                'exchange_rate' => $purchase_order->exchange_rate,
                'branch_id' => $request->branch_id,
                'due_date' => Carbon::parse($request->date_receive)->addDays($purchase_order->top_day),
            ]);

            if ($request->hasFile('file')) {
                $model->file = $this->upload_file($request->file('file'), 'item-receiving-report-general');
            }
            $model->save();

            // * additional items
            foreach ($request->additional_item_id ?? [] as $key => $additional_item_id) {
                $additional_item = new \App\Models\ItemReceivingPoTradingAdditional();
                $additional_item->loadModel([
                    'item_receiving_report_id' => $model->id,
                    'purchase_order_additional_items_id' => $additional_item_id,
                    'outstanding_qty' => thousand_to_float($request->additional_jumlah_tersedia[$key]),
                    'receive_qty' => thousand_to_float($request->additional_receive_qty[$key]),
                    'subtotal' => 0,
                    'tax_total' => 0,
                    'total' => 0,
                ]);
                $additional_item->save();
            }

            // * create report details
            $item_report_detail = new \App\Models\ItemReceivingPoTrading();
            $purchase_order = \App\Models\PoTrading::with(['po_trading_detail'])->find($request->purchase_order_trading_id);
            $item_report_detail->loadModel([
                'item_id' => $purchase_order->po_trading_detail->item_id,
                'item_receiving_report_id' => $model->id,
                'liter_15' => thousand_to_float($request->liter_15 ?? 0),
                'liter_obs' => thousand_to_float($request->liter_obs ?? 0),
                'ware_house_id' => $request->ware_house_id,
                'loading_order' => $request->loading_order,
            ]);
            $item_report_detail->save();

            // * authorization
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: \App\Models\ItemReceivingReport::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "LPB Trading",
                subtitle: Auth::user()->name . " mengajukan LPB Trading " . $model->kode,
                link: route('admin.item-receiving-report-trading.show', $model),
                update_status_link: route('admin.item-receiving-report-trading.update-status', ['id' => $model->id]),
                type: 'trading',
                division_id: auth()->user()->division_id ?? null
            );

            // * create coa
            $model = \App\Models\ItemReceivingReport::find($model->id);
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

        if (!in_array($model->status, ['pending', 'revert']) || !$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', 'status'));
        }

        return view("admin.item-receiving-report.edit.trading_revert", compact('model'));
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
            'purchase_order_trading_id' => 'required|exists:purchase_orders,id',
            'date_receive' => 'required|date',
            'file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5000',
        ]);

        $model = \App\Models\ItemReceivingReport::findOrFail($id);

        if (!in_array($model->status, ['pending', 'revert']) || !$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', 'status'));
        }

        $purchase_order = \App\Models\PoTrading::findOrFail($request->purchase_order_trading_id);

        $purchaseDate = \Carbon\Carbon::parse($model->reference->tanggal);
        $itemReceivingDate = \Carbon\Carbon::parse($request->date_receive);

        if ($purchaseDate->gt($itemReceivingDate)) {
            return redirect()->back()->with('error', 'Tanggal diterima tidak boleh kurang dari tanggal PO');
        }

        DB::beginTransaction();

        try {
            $model->rollbackObserverAfterCreate();

            // Update report
            $model->fill([
                'kode' => generate_trading_code_update($model->kode),
                'date_receive' => Carbon::parse($request->date_receive),
                'ware_house_id' => $request->ware_house_id,
                'reference_id' => $request->purchase_order_trading_id,
                'vendor_id' => $purchase_order->vendor_id,
                'date_receive_time' => Carbon::now()->format("H:i:s"),
                'currency_id' => $purchase_order->currency_id,
                'exchange_rate' => $purchase_order->exchange_rate,
                'branch_id' => $request->branch_id,
                'due_date' => Carbon::parse($request->date_receive)->addDays($purchase_order->top_day),
            ]);

            if ($request->hasFile('file')) {
                Storage::delete($model->file);
                $model->file = $this->upload_file($request->file('file'), 'item-receiving-report-general');
            }
            $model->save();

            // Update or create additional items
            foreach ($request->additional_item_id ?? [] as $key => $additional_item_id) {
                $additional_item = \App\Models\ItemReceivingPoTradingAdditional::where('item_receiving_report_id', $model->id)
                    ->where('purchase_order_additional_items_id', $additional_item_id)
                    ->first();
                if (!$additional_item) {
                    $additional_item = new \App\Models\ItemReceivingPoTradingAdditional();
                }
                $additional_item->loadModel([
                    'item_receiving_report_id' => $model->id,
                    'purchase_order_additional_items_id' => $additional_item_id,
                    'outstanding_qty' => thousand_to_float($request->additional_jumlah_tersedia[$key]),
                    'receive_qty' => thousand_to_float($request->additional_receive_qty[$key]),
                    'subtotal' => 0,
                    'tax_total' => 0,
                    'total' => 0,
                ]);
                $additional_item->save();
            }

            ItemReceivingReportCoa::where('item_receiving_report_id', $model->id)->delete();

            // Create COA
            $lpb_coa = new \App\Http\Helpers\ItemReceivingReportCoaHelpers($model->tipe, $model->reference_id, $model->id);
            $lpb_coa->create_item_receiving_report_coa();

            // Authorization
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: \App\Models\ItemReceivingReport::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "LPB Trading",
                subtitle: Auth::user()->name . " mengajukan LPB Trading " . $model->kode,
                link: route('admin.item-receiving-report-trading.show', $model),
                update_status_link: route('admin.item-receiving-report-trading.update-status', ['id' => $model->id]),
                type: 'trading',
                division_id: auth()->user()->division_id ?? null
            );

            // Update report detail
            $item_report_detail = $model->item_receiving_report_po_trading;
            $item_report_detail->fill([
                'item_id' => $purchase_order->po_trading_detail->item_id,
                'liter_15' => thousand_to_float($request->liter_15 ?? 0),
                'liter_obs' => thousand_to_float($request->liter_obs ?? 0),
                'loading_order' => $request->loading_order,
                'ware_house_id' => $request->ware_house_id,
            ]);
            $item_report_detail->save();

            $model = \App\Models\ItemReceivingReport::find($id);
            $model->observerAfterCreate();
            DB::commit();

            return redirect()->route('admin.item-receiving-report.index')->with($this->ResponseMessageCRUD(true, 'edit'));
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
                    // * make price
                    if ($model->item_receiving_report_po_trading->liter_obs != 0) {

                        $lpb_trading = \App\Models\ItemReceivingPoTrading::where('item_receiving_report_id', $model->id)->first();
                        $price = new \App\Models\Price();
                        $price->item_id = $model->reference->po_trading_detail->item_id;
                        $price->harga_jual = $model->reference->po_trading_detail->harga * $model->reference?->exchange_rate ?? 1;
                        $price->harga_beli = $model->reference->po_trading_detail->harga * $model->reference?->exchange_rate ?? 1;

                        try {
                            $price->save();
                        } catch (\Throwable $th) {
                            throw $th;
                        }

                        $lpb_trading->price_id = $price->id;
                        $lpb_trading->save();

                        $item = \App\Models\Item::find($model->reference->po_trading_detail->item_id);
                        $item_inventory_coa = ItemCategoryCoa::where('item_category_id', $item->item_category_id)
                            ->whereRaw('LOWER(type) = ?', ['inventory'])
                            ->first();

                        if (!$item_inventory_coa) {
                            DB::rollBack();
                            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, 'Item Category Coa Inventory not found'));
                        }

                        $purchase_order = $model->reference;
                        $price_before_discount = $purchase_order->po_trading_detail->harga;
                        $subtotal_before_discount = $price_before_discount * $model->item_receiving_report_po_trading->liter_15;

                        $tax_with_inventory_coas = ItemReceivingReportCoa::where('item_receiving_report_id', $model->id)->where('coa_id', $item_inventory_coa->coa_id)->get();
                        $inventory_tax_total = 0;
                        foreach ($tax_with_inventory_coas as $key => $tax_with_inventory_coa) {
                            $inventory_tax_total += $subtotal_before_discount * $tax_with_inventory_coa->reference->value;
                        }

                        $price_unit = ($model->item_receiving_report_po_trading->sub_total + $inventory_tax_total) / $model->item_receiving_report_po_trading->liter_obs * $model->exchange_rate;
                        $price_unit = $price_unit;
                        $subtotal = $model->item_receiving_report_po_trading->liter_obs * $price_unit;

                        // * make stock mutation
                        $stock_mutation = new \App\Models\StockMutation();
                        $stock_mutation->ware_house_id = $model->ware_house->id;
                        $stock_mutation->branch_id = $model->branch_id;
                        $stock_mutation->item_id = $model->reference->po_trading_detail->item_id;
                        $stock_mutation->price_id = $price->id;
                        $stock_mutation->document_model = get_class($model);
                        $stock_mutation->document_id = $model->id;
                        $stock_mutation->document_code = $model->kode;
                        $stock_mutation->date = $model->date_receive;
                        $stock_mutation->vendor_model = Vendor::class;
                        $stock_mutation->vendor_id = $model->reference->vendor_id;
                        $stock_mutation->type = 'supplier invoice trading'; // tipe: pembelian trading
                        $stock_mutation->in = replaceComma($model->item_receiving_report_po_trading->liter_obs);
                        $stock_mutation->price_unit = $price_unit;
                        $stock_mutation->subtotal = $subtotal;
                        $stock_mutation->note = 'Penerimaan Barang Purchase Order Trading ';

                        try {
                            $stock_mutation->save();
                        } catch (\Throwable $th) {
                            throw $th;
                        }
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
            throw $th;

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.item-receiving-report-trading.show", $model)->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    /**
     * Export PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function exportPdf($id, Request $request)
    {
        if (!$request->preview && authorizePrint('lpb_trading')) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                \App\Models\ItemReceivingReport::class,
                decryptId($id),
                'lpb_trading',
            );

            if (!$result) {
                return abort(403);
            }
        }

        $model = \App\Models\ItemReceivingReport::findOrFail(decryptId($id));

        $qr_url = route('item-receiving-report-trading.export-pdf', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $approval = Authorization::where('model', \App\Models\ItemReceivingReport::class)
            ->with(['details' => function ($q) {
                $q->where('status', 'approve')
                    ->where('note', 'not like', '%otomatis%');
            }])
            ->where('model_id', $model->id)
            ->first();

        $pdf = Pdf::loadView("admin.$this->view_folder.pdf.trading", compact('model', 'qr', 'approval'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'landscape');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $w = $canvas->get_width();
        $h = $canvas->get_height();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM} / {PAGE_COUNT}", '', 8);

        if ($request->preview) {
            Storage::disk('public')->deleteDirectory('tmp_lpb_trading');
            $tmp_file_name = 'lpb_trading_' . time() . '.pdf';
            $path = 'tmp_lpb_trading/' . $tmp_file_name;
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

            $purchase_orders = DB::table('purchase_orders')
                ->whereIn('id', $item_receiving_reports->pluck('reference_id')->toArray())
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

            $histories = $purchase_orders->unique('id')
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
