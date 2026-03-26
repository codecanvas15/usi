<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Http\Helpers\PrintHelper;
use App\Http\Resources\Admin\SoTradingCoaResource;
use App\Models\Authorization;
use App\Models\Currency;
use App\Models\DeliveryOrder;
use App\Models\PairingSoToPo;
use App\Models\PoTradingDetail;
use App\Models\SaleOrderAdditional;
use App\Models\SaleOrderAdditionalTax;
use App\Models\SaleOrderTax;
use App\Models\ShNumber;
use App\Models\SoTrading as model;
use App\Models\SoTrading;
use App\Models\SoTradingDetail;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SoTradingController extends Controller
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
        $this->middleware("permission:approve $this->view_folder|reject $this->view_folder|cancel $this->view_folder|close $this->view_folder", ['only' => ['update_status']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'sales-order';

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
    public function index()
    {
        return redirect()->route('admin.sales.index');
    }

    /**
     * data table
     *
     * @param \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function data(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                'sale_orders.id',
                'sale_orders.nomor_so',
                'sale_orders.nomor_po_external',
                'customers.nama',
                'sale_orders.tanggal',
                'sale_order_details.jumlah',
                'sale_order_details.jumlah',
                'sale_order_details.jumlah',
                'sale_order_details.jumlah',
                'sale_orders.status',
                'sale_orders.pairing_status',
                'sale_orders.id',
            ];

            // * get data with date
            $query = model::with(['customer'])
                ->join('customers', 'customers.id', 'sale_orders.customer_id')
                ->join('sale_order_details', 'sale_orders.id', 'sale_order_details.so_trading_id')
                ->when($request->status, function ($q) use ($request) {
                    $q->where('sale_orders.status', $request->status);
                })
                ->when($request->customer_id, function ($q) use ($request) {
                    $q->where('customer_id', $request->customer_id);
                })
                ->when($request->from_date, function ($q) use ($request) {
                    $q->whereDate('sale_orders.tanggal', '>=', Carbon::parse($request->from_date));
                })
                ->when($request->to_date, function ($q) use ($request) {
                    $q->whereDate('sale_orders.tanggal', '<=', Carbon::parse($request->to_date));
                })
                ->when(!get_current_branch()->is_primary, function ($q) {
                    $q->where('branch_id', get_current_branch_id());
                })
                ->when($request->branch_id, function ($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                })
                ->when($request->input('search.value'), function ($q) use ($request) {
                    $q->where('sale_orders.nomor_so', 'like', "%{$request->input('search.value')}%")
                        ->orWhere('sale_orders.status', 'like', "%{$request->input('search.value')}%")
                        ->orWhere('sale_orders.nomor_po_external', 'like', "%{$request->input('search.value')}%")
                        ->orWhere('customers.nama', 'like', "%{$request->input('search.value')}%");
                })
                ->select('sale_orders.*');


            // * calculate some data
            $totalData = $query->count();
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $totalFiltered = $query->count();

            $query->select('sale_orders.*',)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir);

            $checkAuthorizePrint = authorizePrint('sale_order_trading');

            $data = $query->get();

            // * make data table
            $results = array();
            if (!empty($data)) {
                foreach ($data as $key => $purchase_order) {
                    $badge = '<div class="badge badge-lg badge-' . status_sale_orders()[$purchase_order->status]['color'] . '">
                                ' . status_sale_orders()[$purchase_order->status]['label'] . '
                            </div>';
                    $badge .= '<div class="badge badge-lg badge-' . payment_status()[$purchase_order->payment_status]['color'] . ' ms-5">
                                ' . payment_status()[$purchase_order->payment_status]['label'] . '
                            </div>';

                    $pairing_badge = '<div class="badge badge-lg badge-' . pairing_status()[$purchase_order->pairing_status]['color'] . '">
                                    ' . pairing_status()[$purchase_order->pairing_status]['label'] . '
                                </div>';

                    $linkDetail = route("admin.sales-order.show", $purchase_order->id);
                    $linkExportPdf = route('sales-order.export.id', ['id' => encryptId($purchase_order->id)]);

                    $model_class = get_class($purchase_order);
                    $contentLink = "
                        <div class='d-flex flex-column'>
                            <a href='$linkDetail' class='text-primary'>$purchase_order->nomor_so</a>
                            <a href='$linkExportPdf' class='btn btn-sm btn-flat btn-info btn-print-out' target='_blank' onclick='show_print_out_modal(event)' " . ($checkAuthorizePrint ? "data-model='$model_class' data-id='$purchase_order->id' data-print-type='sale_order' data-link='$linkDetail' data-code='$purchase_order->nomor_so'" : "") . " >Export</a>
                        </div>
                    ";

                    $nestedData['DT_RowIndex'] = $key + 1;
                    $nestedData['id'] = $purchase_order->id;
                    $nestedData['nomor_so'] = $contentLink;
                    $nestedData['nomor_po_external'] = $purchase_order->nomor_po_external;
                    $nestedData['customer']['nama'] = $purchase_order->customer->nama;
                    $nestedData['tanggal'] = localDate($purchase_order->tanggal);
                    $nestedData['jumlah'] = formatNumber($purchase_order->so_trading_detail?->jumlah);
                    $nestedData['jumlah_sudah_do'] = formatNumber(
                        DeliveryOrder::where('so_trading_id', $purchase_order->id)
                            ->whereIn('status', ['approve', 'done'])
                            ->where('type', 'delivery-order')
                            ->sum('load_quantity_realization') ?? '0'
                    );
                    $nestedData['jumlah_selesai_dikirim'] = formatNumber(
                        DeliveryOrder::where('so_trading_id', $purchase_order->id)
                            ->whereIn('status', ['done'])
                            ->where('type', 'delivery-order')
                            ->sum('unload_quantity_realization') ?? '0'
                    );
                    $nestedData['jumlah_invoice'] = formatNumber(
                        \App\Models\InvoiceTrading::where('so_trading_id', $purchase_order->id)
                            ->whereIn('status', ['approve', 'done'])
                            ->sum('jumlah') ?? '0'
                    );
                    $nestedData['created_at'] = $purchase_order->created_at;
                    $nestedData['status'] = $badge;
                    $nestedData['pairing_status'] = $pairing_badge;
                    $link = route('sales-order.export.id', ['id' => encryptId($purchase_order->id)]);
                    $nestedData['type'] = $purchase_order->type;
                    $nestedData['action'] = Blade::render('admin.sales-order.btn-data-table', [
                        'row' => $purchase_order
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

        return abort(403);
    }

    /**
     * Get sale order trading invoice not created yet
     */
    public function getSaleOrderTradingInvoice(Request $request)
    {
        if ($request->ajax()) {
            $model = \App\Models\SoTrading::with('customer')
                ->whereHas('delivery_orders', function ($q) {
                    $q->whereNotIn('status', ['revert', 'reject', 'void', 'pending']);
                    $q->whereDoesntHave('invoice_trading_detail', function ($q) {
                        $q->whereHas('invoice_trading', function ($q) {
                            $q->whereIn('status', ['approve', 'pending', 'revert']);
                        });
                    });
                })
                ->join('customers', 'customers.id', 'sale_orders.customer_id')
                ->when($request->status, function ($query, $status) {
                    $query->where('sale_orders.status', $status);
                })
                ->when($request->status, function ($q) use ($request) {
                    $q->where('sale_orders.status', $request->status);
                })
                ->when($request->customer_id, function ($q) use ($request) {
                    $q->where('sale_orders.customer_id', $request->customer_id);
                })
                ->when($request->from_date, function ($q) use ($request) {
                    $q->whereDate('sale_orders.tanggal', '>=', Carbon::parse($request->from_date));
                })
                ->when($request->to_date, function ($q) use ($request) {
                    $q->whereDate('sale_orders.tanggal', '<=', Carbon::parse($request->to_date));
                })
                ->when(!get_current_branch()->is_primary, function ($q) {
                    $q->where('sale_orders.branch_id', get_current_branch_id());
                })
                ->when($request->branch_id, function ($q) use ($request) {
                    $q->where('sale_orders.branch_id', $request->branch_id);
                })
                ->when($request->input('search.value'), function ($q) use ($request) {
                    $q->where('sale_orders.nomor_so', 'like', "%{$request->input('search.value')}%")
                        ->orWhere('sale_orders.status', 'like', "%{$request->input('search.value')}%")
                        ->orWhere('sale_orders.nomor_po_external', 'like', "%{$request->input('search.value')}%")
                        ->orWhere('customers.nama', 'like', "%{$request->input('search.value')}%");
                })
                ->whereNotIn('sale_orders.status', ['revert', 'reject', 'void', 'pending'])
                ->distinct('sale_orders.id')
                ->select('sale_orders.*');

            return DataTables::of($model)
                ->addIndexColumn()
                ->addColumn('jumlah', fn($row) => formatNumber($row->so_trading_detail?->jumlah))
                ->editColumn('tanggal', fn($row) => localDate($row->tanggal))
                ->addColumn('jumlah_sudah_do', fn($row) => formatNumber(
                    DeliveryOrder::where('so_trading_id', $row->id)
                        ->whereIn('status', ['approve', 'done'])
                        ->where('type', 'delivery-order')
                        ->sum('load_quantity_realization') ?? '0'
                ))
                ->addColumn('jumlah_selesai_dikirim', fn($row) => formatNumber(
                    DeliveryOrder::where('so_trading_id', $row->id)
                        ->whereIn('status', ['done'])
                        ->where('type', 'delivery-order')
                        ->sum('unload_quantity_realization') ?? '0'
                ))
                ->addColumn('generate_invoice', fn($row) => view("admin.sales-order.btn-generate-invoice", compact('row')))
                ->editColumn('status', fn($row) => '<div class="badge badge-lg badge-' . status_sale_orders()[$row->status]['color'] . '">
                                                    ' . status_sale_orders()[$row->status]['label'] . '
                                                </div>')
                ->editColumn('pairing_status', fn($row) => '<div class="badge badge-lg badge-' . pairing_status()[$row->pairing_status]['color'] . '">
                                                    ' . pairing_status()[$row->pairing_status]['label'] . '
                                                </div>')
                ->rawColumns(['generate_invoice', 'status', 'pairing_status'])
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
        if (!$authorization->is_authoirization_exist(model::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }
        $model = [];
        $currency = Currency::where('is_local', true)->first();


        return view("admin.$this->view_folder.create", compact('model', 'currency'));
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

        // Check  availailable date close
        if (!checkAvailableDate($request->tanggal)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal yang anda pilih sudah close'));
        }

        // * create data
        $model = new model();
        $model->loadModel([
            'branch_id' => $request->branch_id,
            'customer_id' => $request->customer_id,
            'tanggal' => Carbon::parse($request->tanggal),
            'nomor_po_external' => $request->nomor_po_external,
            'currency_id' => $request->currency_id,
            'exchange_rate' => thousand_to_float($request->exchange_rate ?? 0),
            'sh_number_id' => $request->sh_number_id,
            'quotation' => $request->hasFile('quotation') ? $this->upload_file($request->file('quotation'), 'sale-order/quotation') : null,
        ]);

        // * saving and make reponse
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        // * save so details
        $total = 0;
        foreach ($request->item_id as $key => $value) {
            $so_detail = new SoTradingDetail();
            $so_detail->loadModel([
                'so_trading_id' => $model->id,
                'item_id' => $value,
                'jumlah' => thousand_to_float($request->jumlah[$key]),
                'harga' => thousand_to_float($request->price[$key]),
            ]);

            try {
                $so_detail->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'create', 'create sales order items', $th->getMessage(), 422);
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'create sales order items', $th->getMessage()));
            }

            $total += thousand_to_float($request->price[$key]) * thousand_to_float($request->jumlah[$key]);
        }

        $tax_list = [];
        if ($request->tax_id != null) {
            foreach ($request->tax_id as $key => $value) {
                $tax = Tax::find($value);
                array_push($tax_list, $tax->value);

                $model_tax = new SaleOrderTax();
                $model_tax->loadModel([
                    'tax_id' => $tax->id,
                    'so_trading_id' => $model->id,
                    'value' => $tax->value,
                    'total' => $total * $tax->value,
                ]);

                try {
                    $model_tax->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return $this->ResponseJsonMessageCRUD(false, 'create', 'create tax', $th->getMessage(), 422);
                    }

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'create tax', $th->getMessage()));
                }
            }
        }

        // * sale order additional =============================================================================================================
        $additional_total = 0;
        if ($request->additional_item) {
            foreach ($request->additional_item as $key => $value) {
                if ($value and $request->jumlah[0] and $request->additional_price[$key]) {
                    $single_additional = thousand_to_float($request->jumlah[0]) * thousand_to_float($request->additional_price[$key]);

                    $so_additional = new SaleOrderAdditional();
                    $so_additional->loadModel([
                        'sale_order_id' => $model->id,
                        'item_id' => $value,
                        'quantity' => thousand_to_float($request->jumlah[0]),
                        'price' => thousand_to_float($request->additional_price[$key]),
                        'sub_total' => $single_additional,
                        'total' => 0,
                    ]);

                    try {
                        $so_additional->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'create sales order additional', $th->getMessage()));
                    }

                    // * sale order additional taxes
                    $single_tax_total = 0;
                    if ($request->additional_tax[$key]) {
                        $additional_tax_id_list = explode(',', $request->additional_tax[$key]);
                        foreach ($additional_tax_id_list as $key2 => $value2) {
                            $tax = Tax::find($value2);
                            if ($tax) {
                                $tax_total = $single_additional * $tax->value;
                                $model_tax = new SaleOrderAdditionalTax();
                                $model_tax->loadModel([
                                    'tax_id' => $tax->id,
                                    'sale_order_additional_id' => $so_additional->id,
                                    'value' => $tax->value,
                                    'total' => $tax_total,
                                ]);

                                try {
                                    $model_tax->save();
                                } catch (\Throwable $th) {
                                    DB::rollBack();

                                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'create tax', $th->getMessage()));
                                }

                                $single_tax_total += $tax_total;
                            }
                        }
                    }

                    // * set total sale order additional
                    $single_additional += $single_tax_total;
                    $additional_total += $single_additional;

                    $so_additional->total = $single_additional;
                    try {
                        $so_additional->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'create tax', $th->getMessage()));
                    }
                }
            }
        }
        // * sale order additional =============================================================================================================

        // * update total and sub total in so trading
        // !=============================================================================================================
        $sub_total = $total;
        $total += (count($tax_list) > 0 ? $total * array_reduce($tax_list, fn($a, $b) => $a + $b, 0) : 0);

        $model->sub_total = $sub_total;
        $model->sub_total_after_tax = $total;
        $model->other_cost = $additional_total;
        $model->total = $total + $additional_total;

        try {
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "SO Trading",
                subtitle: Auth::user()->name . " mengajukan SO Trading " . $model->nomor_so,
                link: route('admin.sales-order.show', $model),
                update_status_link: route('admin.sales-order.update_status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }
        // !=============================================================================================================

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.sales.index")->with($this->ResponseMessageCRUD());
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
        $model = model::with(['customer', 'so_trading_detail.item', 'sh_number.sh_number_details'])
            ->findOrFail($id);
        validate_branch($model->branch_id);

        if ($request->ajax()) {
            return $this->ResponseJsonData(compact('model'));
        }

        $purchase_orders = PoTradingDetail::whereIn('id', $model->so_trading_detail->pairing_so_to_pos->pluck('po_trading_detail_id'))
            ->get();

        $pairing_pos = $purchase_orders->map(function ($po) {
            return $po->po_trading;
        });

        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: model::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );

        $authorization_logs['can_revert'] = $model->check_available_date && in_array($model->status, ['approve']) and in_array($model->pairing_status, ['pending']);
        $authorization_logs['can_void'] = $model->check_available_date && in_array($model->status, ['approve']) and in_array($model->pairing_status, ['pending']);
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = $model->check_available_date && $model->status == 'approve';
        $authorization_logs['can_void_request'] = $model->check_available_date && $model->status == 'approve';
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'pairing_pos', 'auth_revert_void_button'));
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

        if (!$model->check_available_date) {
            abort(403);
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
        $model = model::findOrFail($id);

        validate_branch($model->branch_id);

        DB::beginTransaction();

        // * validate
        if ($request->ajax()) {
            $this->validate($request, model::rules());
        } else {
            $this->validate_api($request->all(), model::rules());
        }


        // * CREATE DATA
        $old_file = $model->quotation;
        $model->fill([
            'branch_id' => $request->branch_id,
            'tanggal' => Carbon::parse($request->tanggal),
            'sh_number_id' => $request->sh_number_id,
            'nomor_so' => generate_trading_code_update($model->nomor_so),
            'nomor_po_external' => $request->nomor_po_external,
            'currency_id' => $request->currency_id,
            'exchange_rate' => thousand_to_float($request->exchange_rate ?? 0),
            'quotation' => $request->hasFile('quotation') ? $this->upload_file($request->file('quotation'), 'sale-order/quotation') : $old_file,
        ]);

        // * DELETE OLD FILE
        if ($request->hasFile("quatation")) {
            $this->delete_file($old_file ?? '');
        }

        // Check  availailable date close
        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal yang anda pilih sudah close'));
        }

        // * SAVING
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()));
        }

        // * UPDATE DETAIL DATA
        $total = 0;

        $detail = $model->so_trading_detail;
        $detail->fill([
            'jumlah' => thousand_to_float($request->jumlah[0]),
            'harga' => thousand_to_float($request->price[0]),
            'item_id' => $request->item_id[0],
        ]);

        $total += thousand_to_float($request->price[0]) * thousand_to_float($request->jumlah[0]);

        try {
            $detail->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()));
        }


        // * DELETE ADD MAKE NEW TAX DATA
        $model->sale_order_taxes()->delete();

        $tax_list = [];
        if ($request->tax_id != null) {
            foreach ($request->tax_id as $key => $value) {
                $tax = Tax::find($value);
                array_push($tax_list, $tax->value);

                $model_tax = new SaleOrderTax();
                $model_tax->loadModel([
                    'tax_id' => $tax->id,
                    'so_trading_id' => $model->id,
                    'value' => $tax->value,
                    'total' => $total * $tax->value,
                ]);

                try {
                    $model_tax->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return $this->ResponseJsonMessageCRUD(false, 'create', 'create tax', $th->getMessage(), 422);
                    }

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'create tax', $th->getMessage()));
                }
            }
        }

        // * DELETE ADDITIONAL AND MAKE NEW ADDITIONAL DATA
        $data = $model->sale_order_additionals->pluck('id')->toArray();
        SaleOrderAdditionalTax::wherein('sale_order_additional_id', $data)->delete();
        $model->sale_order_additionals()->delete();

        $additional_total = 0;
        if ($request->additional_item) {
            foreach ($request->additional_item as $key => $value) {
                if ($value and $request->jumlah[0] and $request->additional_price[$key]) {
                    $single_additional = thousand_to_float($request->jumlah[0]) * thousand_to_float($request->additional_price[$key]);

                    $so_additional = new SaleOrderAdditional();
                    $so_additional->loadModel([
                        'sale_order_id' => $model->id,
                        'item_id' => $value,
                        'quantity' => thousand_to_float($request->jumlah[0]),
                        'price' => thousand_to_float($request->additional_price[$key]),
                        'sub_total' => $single_additional,
                        'total' => 0,
                    ]);

                    try {
                        $so_additional->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'create sales order additional', $th->getMessage()));
                    }

                    // * sale order additional taxes
                    $single_tax_total = 0;
                    if ($request->additional_tax[$key]) {
                        $additional_tax_id_list = explode(',', $request->additional_tax[$key]);
                        foreach ($additional_tax_id_list as $key2 => $value2) {
                            $tax = Tax::find($value2);
                            if ($tax) {
                                $tax_total = $single_additional * $tax->value;
                                $model_tax = new SaleOrderAdditionalTax();
                                $model_tax->loadModel([
                                    'tax_id' => $tax->id,
                                    'sale_order_additional_id' => $so_additional->id,
                                    'value' => $tax->value,
                                    'total' => $tax_total,
                                ]);

                                try {
                                    $model_tax->save();
                                } catch (\Throwable $th) {
                                    DB::rollBack();

                                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'create tax', $th->getMessage()));
                                }

                                $single_tax_total += $tax_total;
                            }
                        }
                    }

                    // * set total sale order additional
                    $single_additional += $single_tax_total;
                    $additional_total += $single_additional;

                    $so_additional->total = $single_additional;
                    try {
                        $so_additional->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'create tax', $th->getMessage()));
                    }
                }
            }
        }

        $sub_total = $total;
        $total += (count($tax_list) > 0 ? $total * array_reduce($tax_list, fn($a, $b) => $a + $b, 0) : 0);

        $model->sub_total = $sub_total;
        $model->sub_total_after_tax = $total;
        $model->other_cost = $additional_total;
        $model->total = $total + $additional_total;

        try {
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "SO Trading",
                subtitle: Auth::user()->name . " mengajukan SO Trading " . $model->nomor_so,
                link: route('admin.sales-order.show', $model),
                update_status_link: route('admin.sales-order.update_status', ['id' => $model->id]),
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


        return redirect()->route("admin.sales.index")->with($this->ResponseMessageCRUD(true, "update"));
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

        return redirect()->route("admin.sales.index")->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * delete so trading details
     *
     * @param  int  $id
     * @returnn \Illuminate\Http\Response
     */
    public function delete_so_trading_details(Request $request, $id)
    {
        $model = SoTradingDetail::findOrFail($id);

        // Check  availailable date close
        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal yang anda pilih sudah close'));
        }


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
        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD(false, 'delete');
        }

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * update_po_external
     *
     * @param Request $request,
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update_po_external(Request $request, int $id)
    {
        $model = model::findOrFail($id);

        // Check  availailable date close
        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal yang anda pilih sudah close'));
        }


        $this->validate($request, [
            'nomor_po_external' => 'required'
        ]);

        DB::beginTransaction();
        $model->nomor_po_external = $request->nomor_po_external;

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()));
        }

        DB::commit();
        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update'));
    }

    /**
     * update status purchase order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, $id)
    {
        DB::beginTransaction();

        $model = model::findOrFail($id);
        // Check  availailable date close
        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal yang anda pilih sudah close'));
        }

        validate_branch($model->branch_id);

        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);
                $model->status = $request->status;
                $model->approved_by = ($request->status == 'approve') ? auth()->user()->id : null;
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
                return $this->ResponseJsonMessageCRUD(false, 'update', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD(true, 'update', 'update status');
        }

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, 'update', 'update status'));
    }

    /**
     * select api for create update order
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function select_for_delivery_order(Request $request)
    {
        $model = model::leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->with(['customer'])
            ->whereIn('sale_orders.status', [
                'paired',
                'partial_sent',
                'partial-sended',
                'not_yet_send',
                'do_not_created',
                'ready'
            ])
            ->when(!get_current_branch()->is_primary, function ($q) {
                $q->where('sale_orders.branch_id', auth()->user()->branch_id);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($q) use ($request) {
                $q->where('sale_orders.branch_id', $request->branch_id);
            })
            ->when($request->has_available_qty, function ($q) {
                $q->whereHas('so_trading_detail', function ($q) {
                    $q->whereColumn('sudah_dialokasikan', '>', 'sudah_dikirim');
                });
            })
            ->select('sale_orders.*', 'customers.nama as customer_name')
            ->orderByDesc('sale_orders.created_at')
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('sale_orders.nomor_so', 'like', "%{$request->search}%")
                        ->orWhere('customers.nama', 'like', "%{$request->search}%");
                });
            })
            ->distinct('sale_orders.id');

        return $this->ResponseJson($model->paginate(10));
    }


    /**
     * Detail for delivery order
     *
     * @param string|null $id
     */
    public function detail_for_delivery_order($id = null)
    {
        $model = model::with([
            'customer',
            'sh_number.sh_number_details',
            'so_trading_detail.item'
        ])
            ->whereIn('status', [
                'paired',
                'partial_sent',
                'not_yet_send',
                'partial-sended',
                'do_not_created',
                'ready'
            ])->findOrFail($id);

        return $this->ResponseJsonData($model);
    }

    /**
     * find sh number from so
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function select_sh_number_from_so(Request $request, $id = null)
    {
        $model = model::with(['sh_number.sh_number_details'])->findOrFail($id);
        validate_branch($model->branch_id);

        return $this->ResponseJsonData($model->sh_number);
    }

    /**
     * find po trading from so
     *
     * @param  Request  $request
     * @param int|null $id
     */
    public function select_po_trading_from_so(Request $request, $id = null)
    {
        $model = model::findOrFail($id);
        validate_branch($model->branch_id);

        // find po paired
        $pairings = $model->so_trading_detail->pairing_so_to_pos;
        $data = [];
        foreach ($pairings as $pairing) {
            $pairing->po_trading_detail->po_trading;
            array_push($data, $pairing->po_trading_detail->po_trading);
        }

        return $this->ResponseJsonData($data);
    }

    /**
     * get jumlah api
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sh_number_so($id = null)
    {
        $model = model::findOrFail($id);
        validate_branch($model->branch_id);
        $sh_numbers = ShNumber::where('customer_id', $model->customer_id)->with(['sh_number_details'])->get();

        return $this->ResponseJsonData($sh_numbers);
    }

    /**
     * get jumlah api
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_jumlah_so($id = null)
    {
        $model = model::findOrFail($id);
        $unit = $model->so_trading_detail->item->unit->name ?? '';
        validate_branch($model->branch_id);

        return $this->ResponseJsonData([
            'jumlah' => $model->so_trading_detail->jumlah - $model->so_trading_detail->sudah_dikirim . " " . $unit,
            'jumlah_int' => $model->jumlah_number,
            'jumlah_dikirim' => $model->so_trading_detail->sudah_dikirim,
            'tanggal' => $model->tanggal,
            'unit' => $unit,
        ]);
    }

    /**
     * datatable delivery order for a sale order
     *
     * @param integer $id
     * @return DataTables
     */
    public function delivery_order(Request $request, $id)
    {
        $model = model::findOrFail($id);
        validate_branch($model->branch_id);
        $data = DeliveryOrder::where('so_trading_id', $id)
            ->when($request->type, function ($q) use ($request) {
                $q->where('type', $request->type);
            });

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('code', function ($row) use ($model) {
                $code = '<a href="' . route("admin.delivery-order.list-delivery-order.show", ['sale_order_id' => $model->id, 'delivery_order_id' => $row->id]) . '" target="_blank" class="text-primary text-decoration-underline hover_text-dark">' . $row->code . '</a>';
                return $code;
            })
            ->editColumn('target_delivery', fn($row) => $row->target_delivery ? localDate($row->target_delivery) : "-")
            ->editColumn('load_date', fn($row) => $row->load_date ? localDate($row->load_date) : "-")
            ->editColumn('unload_date', fn($row) => $row->unload_date ? localDate($row->unload_date) : "-")
            ->editColumn('load_quantity', function ($row) {
                $unit = $row->so_trading->so_trading_detail->item->unit->name ?? '';
                return formatNumber($row->load_quantity) . ' ' . $unit;
            })
            ->editColumn('load_quantity_realization', function ($row) {
                $unit = $row->so_trading->so_trading_detail->item->unit->name ?? '';
                return formatNumber($row->load_quantity_realization) . ' ' . $unit;
            })
            ->editColumn('unload_quantity', function ($row) {
                $unit = $row->so_trading->so_trading_detail->item->unit->name ?? '';
                return formatNumber($row->unload_quantity) . ' ' . $unit;
            })
            ->editColumn('unload_quantity_realization', function ($row) {
                $unit = $row->so_trading->so_trading_detail->item->unit->name ?? '';
                return formatNumber($row->unload_quantity_realization) . ' ' . $unit;
            })
            ->editColumn('status', fn($row) => view('admin.delivery-order.status', compact('row')))
            ->editColumn('export', function ($row) {
                $link = route("sales-order.export.id", ['id' => encryptId($row->id)]);
                $export = '<a href="' . $link . '" class="btn btn-sm btn-flat btn-info" onclick="show_print_out_modal(event)">Export</a>';

                return $export;
            })
            ->addColumn('kapasitas_do', fn($row) => $row->purchase_transport_detail ? formatNumber($row->purchase_transport_detail->jumlah) : formatNumber($row->kuantitas_kirim))
            ->addColumn('moda_transport', fn($row) => $row->purchase_transport_id ? 'Transportir' : 'Own Use')
            ->rawColumns(['code', 'status'])
            ->make(true);
    }

    /**
     * select 2 for sale order fone
     *
     * @return \Illuminate\Http\Response
     */
    public function select_sale_order_delivery_complete()
    {
        $model = model::where('status', 'delivery_complete')->with('customer')->orderByDesc('created_at')->get();
        // validate_branch($model->branch_id);

        return $this->ResponseJsonData($model);
    }

    /**
     * sale order done detail
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show_sale_order_delivery_complete($id = null)
    {
        $sale_order = model::with(['customer', 'so_trading_detail'])->findOrFail($id);
        validate_branch($sale_order->branch_id);

        $delivery_order = DeliveryOrder::where('status', 'done')->where('so_trading_id', $id)->with(['sh_number', 'purchase_transport_detail.purchase_transport.vendor'])->get();

        if ($sale_order->status != 'delivery_complete') {
            return abort(404);
        }

        $sale_order->so_trading_detail->item;
        $sale_order->currency;
        foreach ($sale_order->so_trading_detail->pairing_so_to_pos  as $item) {
            $item->po_trading_detail->po_trading->sh_number;
        }

        foreach ($sale_order->sale_order_taxes as $item) {
            $item->tax;
        }

        $jumlah_diterima = 0.00;
        foreach ($delivery_order as $key => $value) {
            $jumlah_diterima += $value->kuantitas_diterima;
        }

        return $this->ResponseJsonData(compact('sale_order', 'delivery_order', 'jumlah_diterima'));
    }

    /**
     * get were house
     *
     * @param int|null $id
     * @return \Illuminate\Http\Response
     */
    public function so_ware_house($id = null)
    {
        $model = model::findOrFail($id);
        validate_branch($model->branch_id);

        foreach ($model->so_trading_detail->s_so_to_pos as $pairing) {
            $pairing->po_trading_detail->po_trading->purchase_order_ware_house?->ware_house;
        }

        $data = [];
        foreach ($model['so_trading_detail']['pairing_so_to_pos'] as $pairing) {
            $data[] = $pairing['po_trading_detail']['po_trading']['purchase_order_ware_house'];
        }

        return $this->ResponseJsonData($data);
    }

    /**
     * sale_order_coa
     *
     * @param int|null $id
     * @return mixed
     */
    public function sale_order_coas(int|null $id)
    {
        $model = model::findOrFail($id);
        validate_branch($model->branch_id);

        return $this->ResponseJsonData(new SoTradingCoaResource($model));
    }

    /**
     * delivery_order_done
     *
     * @param $model
     * @return \Illuminate\Http\Response
     */
    public function delivery_order_done($id)
    {
        $model = model::findOrFail($id);
        validate_branch($model->branch_id);

        $delivery_orders = DeliveryOrder::where('status', 'done')->where('so_trading_id', $id)->get();
        return DataTables::of($delivery_orders)
            ->addIndexColumn()
            ->editColumn('code', fn($row) => view("admin.delivery-order.detail-link", [
                'field' => $row->code,
                'row' => $row,
                'main' => 'delivery-order',
            ]))
            ->editColumn('target_delivery', fn($row) => localDate($row->target_delivery) ?? "-")
            ->editColumn('load_date', fn($row) => localDate($row->load_date) ?? "-")
            ->editColumn('unload_date', fn($row) => localDate($row->unload_date) ?? "-")
            ->editColumn('load_quantity', function ($row) {
                $unit = $row->so_trading->so_trading_detail->item->unit->name ?? '';
                formatNumber($row->load_quantity) . ' ' . $unit;
            })
            ->editColumn('unload_quantity_realization', function ($row) {
                $unit = $row->so_trading->so_trading_detail->item->unit->name ?? '';
                formatNumber($row->unload_quantity_realization) . ' ' . $unit;
            })
            ->rawColumns(['code'])
            ->make(true);
    }

    /**
     * sale_order export
     *
     */
    public function export($id, Request $request)
    {
        if (!$request->preview && authorizePrint('sale_order_trading')) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                model::class,
                decryptId($id),
                'sale_order',
            );

            if (!$result) {
                return abort(403);
            }
        }

        $model = model::with('customer', 'so_trading_detail', 'so_trading_detail.item')->findOrFail(decryptId($id));
        $file = public_path('/pdf_reports/Report-Sales-Order-' . microtime(true) . '.pdf');
        $fileName = 'Report-Sales-Order-' . microtime(true) . '.pdf';

        $taxes_id = $model->sale_order_taxes;
        $taxes_id = $taxes_id->map(function ($tax) {
            return $tax->only(['tax_id', 'value']);
        });

        $sale_order_add_taxes = SaleOrderAdditionalTax::whereHas('sale_order_additional', function ($q) use ($model) {
            $q->whereHas('sale_order', function ($q) use ($model) {
                $q->where('id', $model->id);
            });
        })->get();

        $addtion_taxes_id = $sale_order_add_taxes->map(function ($tax) {
            return $tax->only(['tax_id', 'value']);
        });

        $taxes_id = $taxes_id->merge($addtion_taxes_id);
        $taxes_id = $taxes_id->unique(function ($tax) {
            return $tax['tax_id'] . $tax['value'];
        });

        $taxes = Tax::whereIn(
            'id',
            $taxes_id->pluck('tax_id')->toArray()
        )->get();

        $taxes_id = $taxes_id->map(function ($tax) use ($model, $sale_order_add_taxes, $taxes) {
            $tax['amount'] = $model->sale_order_taxes
                ->where('value', $tax['value'])
                ->where('tax_id', $tax['tax_id'])
                ->sum('total');
            $tax['amount'] += $sale_order_add_taxes
                ->where('value', $tax['value'])
                ->where('tax_id', $tax['tax_id'])
                ->sum('total');
            $tax['name'] = $taxes->where('id', $tax['tax_id'])->first()->tax_name_with_percent;

            return $tax;
        });

        $qr_url = route('sales-order.export.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $approval = Authorization::where('model', model::class)
            ->with(['details' => function ($q) {
                $q->where('status', 'approve')
                    ->where('note', 'not like', '%otomatis%');
            }])
            ->where('model_id', $model->id)
            ->first();

        $pdf = PDF::loadview("admin/.$this->view_folder./export", compact('model', 'qr', 'taxes_id', 'approval'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'potrait');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $h = $canvas->get_height();
        $w = $canvas->get_width();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM}/{PAGE_COUNT}", '', 8);

        if ($request->ajax() || $request->preview) {
            $canvas->page_text($w / 5, $h / 1.7, 'PREVIEW ONLY', null, 60, array(0, 0, 0, 0.3), 0, 0, -30);
        }

        if ($request->ajax()) {
            Storage::disk('public')->deleteDirectory('tmp_sale_order');
            $tmp_file_name = 'sale_order_' . time() . '.pdf';
            $path = 'tmp_sale_order/' . $tmp_file_name;
            Storage::disk('public')->put($path, $pdf->output());

            return response()->json($path);
        }

        return $pdf->stream($fileName);
    }

    /**
     * adjust pairing trading
     *
     * @param int $id
     * @return mixed
     */
    public function adjust_pairing($id)
    {
        $model = model::findOrFail($id);

        if (in_array($model->status, ['done', 'pending', 'reject', 'void', 'revert'])) {
            return abort(403);
        }

        return view("admin.$this->view_folder.adjust-pairing", compact('model'));
    }

    /**
     * store_adjust_pairing
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function store_adjust_pairing(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $model = model::findOrFail($id);
            $so_trading_detail = $model->so_trading_detail;
            $jumlah_dialokasikan = 0;

            if (in_array($model->status, ['done', 'pending', 'reject', 'void', 'revert'])) {
                return abort(403);
            }

            $this->validate($request, [
                'alokasi.*' => 'required|min:0',
            ]);

            if (is_array($request->re_alokasi)) {
                foreach ($request->re_alokasi as $key => $value) {
                    // find data so and po
                    $pairing = PairingSoToPo::findOrFail($request->pairing_id[$key]);;

                    // if (thousand_to_float($value) == 0 or thousand_to_float($value) == null) {
                    //     $pairing->delete();
                    // } else {
                    $value_awal = $pairing->alokasi;

                    $jumlah_dialokasikan += thousand_to_float($value);
                    if ($pairing->alokasi != $value) {
                        $pairing->alokasi = thousand_to_float($value);
                        $pairing->save();

                        if ($key == 0) {
                            // update po trading detail
                            $po_trading_detail = $pairing->po_trading_detail;
                            $po_trading_detail->sudah_dialokasikan -= $po_trading_detail->sudah_dialokasikan - thousand_to_float($value);
                            $po_trading_detail->save();
                        } else {
                            $value_data = $value_awal - thousand_to_float($value);

                            if ($value_data < 0) {
                                // update po trading detail
                                $po_trading_detail = $pairing->po_trading_detail;
                                $po_trading_detail->sudah_dialokasikan -= thousand_to_float($value);
                                $po_trading_detail->save();
                            } else {
                                // update po trading detail
                                $po_trading_detail = $pairing->po_trading_detail;
                                $po_trading_detail->sudah_dialokasikan += thousand_to_float($value);
                                $po_trading_detail->save();
                            }
                        }
                    }
                    // }
                }
            }

            $so_trading_detail->sudah_dialokasikan = $jumlah_dialokasikan;
            $so_trading_detail->save();

            DB::commit();

            return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, 'update', 'adjust pairing'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'adjust pairing', $th->getMessage()));
        }
    }

    /**
     * detail_edit
     *
     * @param $id
     * @return mixed
     */
    public function detail_edit($id)
    {
        $model = model::with([
            'branch',
            'customer',
            'currency',
            'sh_number.sh_number_details',
            'so_trading_detail.item.unit',
            'sale_order_taxes.tax'
        ])->findOrFail($id);

        $additional = $model->sale_order_additionals()->with([
            'item',
            'sale_order_additional_taxes.tax'
        ])->get();

        return $this->ResponseJsonData(compact('model', 'additional'));
    }

    /**
     * get_lpb
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function get_lpb($id)
    {
        $model = model::findOrFail($id);

        $data = [];
        foreach ($model->so_trading_detail->pairing_so_to_pos as $pairing_key => $pairing) {
            foreach ($pairing->po_trading_detail->po_trading->item_receiving_report_data as $item_receiving_report_key => $item_receiving_report) {
                array_push($data, $item_receiving_report);
            }
        }

        return $this->ResponseJsonData($data);
    }

    /**
     * get detail data for invoice
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function get_detail_data_for_invoice($id)
    {
        $model = model::with([
            'customer.customer_banks.bank_internal',
            'currency',
            'sh_number.sh_number_details',
            'so_trading_detail.item.unit',
            'sale_order_taxes.tax',
            'sale_order_additionals.sale_order_additional_taxes.tax',
            'sale_order_additionals.item',
        ])->findOrFail($id);

        $delivery_orders = DeliveryOrder::where('so_trading_id', $id)
            ->where('status', 'done')
            ->where('is_invoice_created', false)
            ->whereDoesntHave('invoice_trading_detail', function ($query) {
                $query->whereHas('invoice_trading', function ($query) {
                    $query->whereIn('status', ['approve', 'pending']);
                });
            })
            ->where('type', "delivery-order")
            ->get();

        $delivery_order_ships = DeliveryOrder::where('so_trading_id', $id)
            ->where('status', 'done')
            ->where('is_invoice_created', false)
            ->where('type', "delivery-order-2")
            ->get();

        return $this->ResponseJsonData(compact('model', 'delivery_orders', 'delivery_order_ships'));
    }

    public function history($id, Request $request)
    {
        try {
            $sale_orders = DB::table('sale_orders')
                ->whereNull('deleted_at')
                ->where('id', $id)
                ->select(
                    'id',
                    'sale_orders.nomor_so as code',
                    'sale_orders.tanggal as date',
                    'status',
                )->get();

            $sale_orders = $sale_orders->map(function ($item) {
                $item->link = route('admin.sales-order.show', $item->id);
                $item->menu = 'sales order trading';
                return $item;
            });

            $delivery_orders = DB::table('delivery_orders')
                ->where('so_trading_id', $id)
                ->whereNotIn('status', ['rejected', 'void'])
                ->whereNull('deleted_at')
                ->select(
                    'delivery_orders.id',
                    'delivery_orders.code',
                    'delivery_orders.target_delivery as date',
                    'delivery_orders.status',
                )->get();

            $delivery_orders = $delivery_orders->map(function ($item) use ($id) {
                $item->link = route('admin.delivery-order.list-delivery-order.show', ['sale_order_id' => $id, 'delivery_order_id' => $item->id]);
                $item->menu = 'delivery order trading';
                return $item;
            });

            $invoice_tradings = DB::table('invoice_tradings')
                ->join('invoice_parents', function ($query) {
                    $query->on('invoice_parents.reference_id', '=', 'invoice_tradings.id')
                        ->where('invoice_parents.model_reference', '=', 'App\Models\InvoiceTrading');
                })
                ->where('invoice_tradings.so_trading_id', $id)
                ->whereNotIn('invoice_tradings.status', ['rejected', 'void'])
                ->whereNull('invoice_tradings.deleted_at')
                ->select(
                    'invoice_tradings.id',
                    'invoice_tradings.kode as code',
                    'invoice_tradings.date',
                    'invoice_tradings.status',
                    'invoice_parents.id as invoice_parent_id',
                )->get();

            $invoice_tradings = $invoice_tradings->map(function ($item) {
                $item->link = route('admin.invoice-trading.show', $item->id);
                $item->menu = 'invoice trading';
                return $item;
            });

            $invoice_down_payments = DB::table('invoice_down_payments')
                ->where('invoice_down_payments.sale_order_model', SoTrading::class)
                ->where('invoice_down_payments.sale_order_model_id', $id)
                ->whereNull('invoice_down_payments.deleted_at')
                ->whereNotIn('invoice_down_payments.status', ['rejected', 'void'])
                ->select(
                    'invoice_down_payments.id',
                    'invoice_down_payments.code',
                    'invoice_down_payments.date',
                    'invoice_down_payments.status'
                )
                ->get();

            $invoice_down_payments = $invoice_down_payments->map(function ($item) {
                $item->link = route('admin.invoice-down-payment.show', $item->id);
                $item->menu = 'invoice down payment';
                return $item;
            });

            $receivables_payments = DB::table('receivables_payment_details')
                ->where('invoice_parent_id', $invoice_tradings->pluck('invoice_parent_id')->toArray())
                ->join('receivables_payments', 'receivables_payments.id', '=', 'receivables_payment_details.receivables_payment_id')
                ->leftJoin('bank_code_mutations', function ($query) {
                    $query->on('bank_code_mutations.ref_id', '=', 'receivables_payments.id')
                        ->where('bank_code_mutations.ref_model', '=', 'App\Models\ReceivablesPayment');
                })
                ->whereNull('receivables_payments.deleted_at')
                ->whereNotIn('receivables_payments.status', ['rejected', 'void'])
                ->select(
                    'receivables_payments.id',
                    'receivables_payments.code',
                    'bank_code_mutations.code as bank_code_mutation_code',
                    'receivables_payments.date',
                    'receivables_payments.status',
                )->get()
                ->map(function ($item) {
                    $item->code = $item->bank_code_mutation_code ?? $item->code;
                    return $item;
                });

            $receivables_payments = $receivables_payments->map(function ($item) {
                $item->link = route('admin.receivables-payment.show', $item->id);
                $item->menu = 'receivables payment';
                return $item;
            });

            $histories = $sale_orders->unique('id')
                ->merge($delivery_orders->unique('id'))
                ->merge($invoice_tradings->unique('id'))
                ->merge($invoice_down_payments->unique('id')) // 👈 INVDP
                ->merge($receivables_payments->unique('id'))
                ->sortBy('date')
                ->values()
                ->all();

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

    public function bypass_pairing($id)
    {
        DB::beginTransaction();
        try {
            $model = model::findOrFail($id);
            if ($model->status == 'approve' && $model->pairing_status == "pending") {
                $this->create_activity_status_log(model::class, $id, "bypass SO pairing $model->nomor_so", $model->status, "ready");

                $model->pairing_status = "done";
                $model->status = "ready";
                $model->save();

                $sale_order_detail = SoTradingDetail::where('so_trading_id', $model->id)->first();
                $sale_order_detail->sudah_dialokasikan = $sale_order_detail->jumlah;
                $sale_order_detail->save();
            } else {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'Gagal melakukan bypass pairing'));
            }

            DB::commit();
            return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update', 'Berhasil melakukan bypass pairing'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', $th->getMessage()));
        }
    }

    public function select_for_purchase_order(Request $request)
    {
        $model = model::leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->with(['customer'])
            ->whereNotIn('sale_orders.status', [
                'pending',
                'rejected',
                'void',
            ])
            ->where('pairing_status', '!=', 'done')
            ->when(!get_current_branch()->is_primary, function ($q) {
                $q->where('sale_orders.branch_id', auth()->user()->branch_id);
            })
            ->when($request->branch_id, function ($q) use ($request) {
                $q->where('sale_orders.branch_id', $request->branch_id);
            })
            ->select('sale_orders.*', 'customers.nama as customer_name')
            ->orderByDesc('sale_orders.created_at')
            ->limit(10)
            ->when($request->search, function ($q) use ($request) {
                $q->where('sale_orders.nomor_so', 'like', "%$request->search%")
                    ->orWhere('customers.nama', 'like', "%$request->search%");
            })
            ->distinct('sale_orders.id');

        return $this->ResponseJson($model->paginate(10));
    }

    public function select(Request $request)
    {
        $model = model::leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->with(['customer'])
            ->whereNotIn('sale_orders.status', [
                'rejected',
                'void',
            ])
            ->select('sale_orders.*')
            ->orderByDesc('sale_orders.created_at')
            ->limit(10)
            ->when($request->customer_id, function ($q) use ($request) {
                $q->where('sale_orders.customer_id', $request->customer_id);
            })
            ->when($request->currency_id, function ($q) use ($request) {
                $q->where('sale_orders.currency_id', $request->currency_id);
            })
            ->when($request->search, function ($q) use ($request) {
                $q->where('sale_orders.nomor_so', 'like', "%$request->search%")
                    ->orWhere('customers.nama', 'like', "%$request->search%");
            })
            ->distinct('sale_orders.id');

        return $this->ResponseJson($model->paginate(10));
    }

    public function generate_sale_order_status()
    {
        DB::beginTransaction();
        try {
            $sale_order_details = SoTradingDetail::whereColumn('sudah_dikirim', '>=', 'sudah_dialokasikan')
                ->where('sudah_dialokasikan', '!=', 0)
                ->get();

            foreach ($sale_order_details as $key => $sale_order_detail) {
                if ($sale_order_detail->so_trading->status == 'partial-sended') {
                    DB::table('sale_orders')
                        ->where('id', $sale_order_detail->so_trading_id)
                        ->update([
                            'status' => 'delivery_complete',
                        ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false
            ]);
        }
    }

    public function cancelPairing(Request $request, $id)
    {
        $findSoTrading = model::findOrFail($id);

        if (!$findSoTrading) return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'Data tidak ditemukan'));

        if ($findSoTrading->so_trading_detail->pairing_so_to_pos->count() > 0) return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'Hanya bisa membatalkan bypass pairing'));

        DB::beginTransaction();
        try {
            $findSoTrading->pairing_status = 'pending';
            $findSoTrading->status = 'approve';
            $findSoTrading->save();

            $findSoTrading->so_trading_detail->sudah_dialokasikan = 0;
            $findSoTrading->so_trading_detail->save();

            DB::commit();
            return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update', 'Berhasil membatalkan pairing'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', $th->getMessage()));
        }
    }
}
