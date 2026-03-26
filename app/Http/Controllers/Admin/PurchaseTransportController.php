<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Http\Helpers\PrintHelper;
use App\Http\Resources\Admin\PurchaseTransportCoaResource;
use App\Models\AccountPayable;
use App\Models\Authorization;
use App\Models\Branch;
use App\Models\DeliveryOrder;
use App\Models\Purchase;
use App\Models\PurchaseRequest;
use App\Models\PurchaseTransport as model;
use App\Models\PurchaseTransport;
use App\Models\PurchaseTransportDetail;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;

class PurchaseTransportController extends Controller
{
    use ActivityStatusLogHelper;
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view purchase-transport", ['only' => ['index', 'show']]);
        $this->middleware("permission:create purchase-transport", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit purchase-transport", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete purchase-transport", ['only' => ['destroy']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'purchase-order-transport';

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
        abort(404);
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
                '',
                'kode',
                'so_trading_id',
                'vendor_id',
                'type',
                'status',
                'created_at',
            ];


            // * get data with date
            $query = model::join('purchases', function ($query) {
                $query->on('purchases.model_id', 'purchase_transports.id')
                    ->where('model_reference', model::class);
            })
                ->leftJoin('cash_advance_payments', function ($query) {
                    $query->on('cash_advance_payments.purchase_id', 'purchases.id')
                        ->whereNull('cash_advance_payments.deleted_at')
                        ->where('cash_advance_payments.status', 'approve');
                })
                ->orderByDesc('purchase_transports.target_delivery')
                ->with(['vendor', 'so_trading'])
                ->select('purchase_transports.*', 'cash_advance_payments.id as cash_advance_payment_id')
                ->join('vendors', 'vendors.id', '=', 'purchase_transports.vendor_id');


            if ($request->branch_id) {
                $query->where('purchase_transports.branch_id', $request->branch_id);
            }

            if ($request->from_date) {
                $query->whereDate('purchase_transports.target_delivery', '>=', Carbon::parse($request->from_date));
            }

            if ($request->to_date) {
                $query->whereDate('purchase_transports.target_delivery', '<=', Carbon::parse($request->to_date));
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
                    $q->where('purchase_transports.kode', 'like', "%{$search}%");
                    $q->orWhere('vendors.nama', 'like', "%{$search}%");
                });
            }

            if ($request->status) {
                $query->where('purchase_transports.status', $request->status);
            }

            if ($request->vendor_id) {
                $query->where('purchase_transports.vendor_id', $request->vendor_id);
            }

            $totalFiltered = $query->count();

            $query
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir);

            $checkAuthorizePrint = authorizePrint('purchase_order_transport');

            $data = $query->get();

            // * make data table
            $results = array();
            if (!empty($data)) {
                foreach ($data as $key => $purchase_order) {
                    $badge = '';
                    if (!in_array($purchase_order->status, ['done']) && $purchase_order->cash_advance_payment_id) {
                        $badge = '<br><div class="badge badge-pill badge-danger  mt-1 animate__animated animate__pulse animate__infinite infinite">Uang Muka Telah dibayar!</div>';
                    }

                    $nestedData['DT_RowIndex'] = $key + 1;
                    $nestedData['id'] = $purchase_order->id;
                    $link = route("purchase-order-transport.export.id", ['id' => encryptId($purchase_order->id)]);
                    $nestedData['kode'] = '<a target="_blank" href="' . route("admin.$this->view_folder.index") . '/' . $purchase_order->id . '" class="text-primary">' . $purchase_order->kode . '</a>' . '<br>' .
                        view('components.button-auth-print', [
                            'type' => 'purchase_order_transport',
                            'href' => $link,
                            'model' => model::class,
                            'did' => $purchase_order->id,
                            'code' => $purchase_order->code,
                            'condition' => $checkAuthorizePrint,
                        ]);
                    if ($purchase_order->so_trading) {
                        $nestedData['so_trading']['nomor_so'] = '<a target="_blank" href="' . route("admin.sales-order.index") . '/' . $purchase_order->so_trading->id . '" class="text-primary">' . $purchase_order->so_trading->nomor_so . '</a>';
                    } else {
                        $nestedData['so_trading']['nomor_so'] = '';
                    }
                    $nestedData['vendor']['nama'] = $purchase_order?->vendor?->nama;
                    $nestedData['type'] = Str::headline($purchase_order->type);
                    $nestedData['status'] = '<div class="badge badge-lg badge-' . purchase_transport_status()[$purchase_order->status]['color'] . '">
                                            ' . purchase_transport_status()[$purchase_order->status]['label'] . ' - ' . purchase_transport_status()[$purchase_order->status]['text'] . '
                                        </div>' . $badge;
                    $nestedData['created_at'] = toDayDateTimeString($purchase_order->created_at);

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $model = [];

        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(PurchaseTransport::class)) {
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

        $so_trading = $request->one_so_trading_id ?? $request->so_trading_id;

        if ($request->type == 'not_double_handling') {
            $sub_total = 0;
            $total = 0;

            // * creating purchase parent
            $model = new Purchase();
            $model->loadModel([
                'tipe' => 'transportir',
                'tanggal' => Carbon::parse($request->target_delivery),
                'vendor_id' => $request->vendor_id,
                'branch_id' => $request->branch_id,
            ]);

            // Check Available Date Closing
            if (!$model->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal yang dipilih sudah closing'));
            }

            try {
                $model->save();
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }

            // * creating purchase transport
            $purchase_transport = new PurchaseTransport();
            $purchase_transport->fill([
                'branch_id' => $request->branch_id,
                'delivery_destination' => $request->delivery_destination,
                'send_from' => $request->send_from,
                'po_trading_id' => $request->po_trading_id,
                'target_delivery' => Carbon::parse($request->target_delivery),
                'purchase_id' => $model->id,
                'purchase_request_id' => null,
                'so_trading_id' => $so_trading,
                'vendor_id' => $request->vendor_id,
                'ware_house_id' => $request->ware_house_id,
                'item_id' => $request->item_id,
                'currency_id' => $request->currency_id,
                'exchange_rate' => thousand_to_float($request->exchange_rate),
                'type' => $request->type,
                'harga' => thousand_to_float($request->price),
                'sub_total' => 0,
                'total' => 0,
                'created_by' => auth()->user()->id,
            ]);

            // Check Available Date Closing
            if (!$purchase_transport->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal yang dipilih sudah closing'));
            }

            try {
                $purchase_transport->save();

                $model->update([
                    'model_id' => $purchase_transport->id,
                    'currency_id' => $purchase_transport->currency_id,
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }

            // * creating purchase transport detail
            $data_details = [];
            foreach ($request->amount_do as $key => $qty) {
                $data_details[] = [
                    'purchase_transport_id' => $purchase_transport->id,
                    'jumlah_do' => thousand_to_float($qty),
                    'jumlah' => thousand_to_float($request->quantity[$key]),
                    'vehicle_type' => $request->vehicle_type[$key] ?? null,
                    'vehicle_info' => $request->vehicle_info[$key] ?? null,
                ];

                $sub_total += thousand_to_float($qty) *  thousand_to_float($request->quantity[$key]) * thousand_to_float($request->price);
                $total += thousand_to_float($qty) *  thousand_to_float($request->quantity[$key]) * thousand_to_float($request->price);
            }

            try {
                $purchase_transport->purchase_transport_details()->createMany($data_details);
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }

            // * create purchase transport tax
            if (is_array($request->tax_id) && count($request->tax_id) > 0) {
                $data_tax = [];

                $taxes = Tax::whereIn('id', $request->tax_id)->get();

                foreach ($taxes as $key => $tax) {
                    $data_tax[] = [
                        'tax_id' => $tax->id,
                        'purchase_transport_id' => $purchase_transport->id,
                        'value' => $tax->value,

                    ];

                    $total += $sub_total * $tax->value;
                }

                try {
                    $purchase_transport->purchase_transport_taxes()->createMany($data_tax);
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
                }
            }

            // * update purchase transport total
            $purchase_transport->sub_total = $sub_total;
            $purchase_transport->total = $total;
            $purchase_transport->total_qty = $purchase_transport->purchase_transport_details->map(function ($item) {
                return $item->jumlah * $item->jumlah_do;
            })->sum();
            try {
                $purchase_transport->save();

                $authorization = new \App\Http\Helpers\AuthorizationHelper();
                $authorization->init(
                    branch_id: $purchase_transport->branch_id,
                    user_id: auth()->user()->id,
                    model: PurchaseTransport::class,
                    model_id: $purchase_transport->id,
                    amount: $purchase_transport->total ?? 0,
                    title: "PO Transport",
                    subtitle: Auth::user()->name . " mengajukan PO Transport " . $purchase_transport->kode,
                    link: route('admin.purchase-order-transport.show', $purchase_transport),
                    update_status_link: route('admin.purchase-order-transport.update-status', ['id' => $purchase_transport->id]),
                    division_id: auth()->user()->division_id ?? null
                );
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }

            // * update purchase
            $model->kode = $purchase_transport->kode;
            $model->model_reference = PurchaseTransport::class;
            try {
                $model->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'create', 'updating purchase code and reference.', $th->getMessage(), 422);
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'updating purchase code and reference.', $th->getMessage()));
            }
        } elseif ($request->type == 'double_handling') {
            // ! VENDOR ONE =====================================================
            $sub_total = 0;
            $total = 0;

            // * creating purchase parent
            $model_one = new Purchase();
            $model_one->loadModel([
                'tipe' => 'transportir',
                'tanggal' => Carbon::parse($request->one_target_delivery),
                'vendor_id' => $request->one_vendor_id,
                'branch_id' => $request->branch_id,
            ]);
            try {
                $model_one->save();
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }

            // * creating purchase transport
            $purchase_transport_one = new PurchaseTransport();
            $purchase_transport_one->fill([
                'branch_id' => $request->branch_id,
                'delivery_destination' => $request->delivery_destination,
                'send_from' => $request->send_from,
                'po_trading_id' => $request->po_trading_id,
                'target_delivery' => Carbon::parse($request->one_target_delivery),
                'purchase_id' => $model_one->id,
                'purchase_request_id' => null,
                'so_trading_id' => $so_trading,
                'vendor_id' => $request->one_vendor_id,
                'ware_house_id' => $request->one_ware_house_id,
                'item_id' => $request->one_item_id,
                'currency_id' => $request->one_currency_id,
                'exchange_rate' => thousand_to_float($request->one_exchange_rate),
                'type' => $request->type,
                'harga' => thousand_to_float($request->one_price),
                'sub_total' => 0,
                'total' => 0,
            ]);

            try {
                $purchase_transport_one->save();

                $model_one->update([
                    'model_id' => $purchase_transport_one->id,
                    'currency_id' => $purchase_transport_one->currency_id,
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }

            try {
                $purchase_transport_one->purchase_transport_details()->createMany([
                    [
                        'purchase_transport_id' => $purchase_transport_one->id,
                        'jumlah_do' => 1,
                        'jumlah' => thousand_to_float($request->one_quantity),
                    ]
                ]);

                $sub_total += thousand_to_float($request->one_quantity) * thousand_to_float($request->one_price);
                $total += thousand_to_float($request->one_quantity) * thousand_to_float($request->one_price);
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }

            // * create purchase transport tax
            if (
                is_array($request->one_tax_id) && count($request->one_tax_id) > 0
            ) {
                $data_tax = [];

                $taxes = Tax::whereIn('id', $request->one_tax_id)->get();

                foreach ($taxes as $key => $tax) {
                    $data_tax[] = [
                        'tax_id' => $tax->id,
                        'purchase_transport_id' => $purchase_transport_one->id,
                        'value' => $tax->value,

                    ];

                    $total += $sub_total * $tax->value;
                }

                try {
                    $purchase_transport_one->purchase_transport_taxes()->createMany($data_tax);
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(
                        false,
                        'create',
                        null,
                        $th->getMessage()
                    ));
                }
            }

            // * update purchase transport total
            $purchase_transport_one->sub_total = $sub_total;
            $purchase_transport_one->total = $total;
            $purchase_transport_one->total_qty = $purchase_transport_one->purchase_transport_details->map(function ($item) {
                return $item->jumlah * $item->jumlah_do;
            })->sum();
            try {
                $purchase_transport_one->save();

                $authorization = new \App\Http\Helpers\AuthorizationHelper();
                $authorization->init(
                    branch_id: $purchase_transport_one->branch_id,
                    user_id: auth()->user()->id,
                    model: PurchaseTransport::class,
                    model_id: $purchase_transport_one->id,
                    amount: $purchase_transport_one->total ?? 0,
                    title: "PO Transport",
                    subtitle: Auth::user()->name . " mengajukan PO Transport " . $purchase_transport_one->kode,
                    link: route('admin.purchase-order-transport.show', $purchase_transport_one),
                    update_status_link: route('admin.purchase-order-transport.update-status', ['id' => $purchase_transport_one->id]),
                    division_id: auth()->user()->division_id ?? null
                );
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }

            // * update purchase
            $model_one->kode = $purchase_transport_one->kode;
            $model_one->model_reference = PurchaseTransport::class;
            try {
                $model_one->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'create', 'updating purchase code and reference.', $th->getMessage(), 422);
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'updating purchase code and reference.', $th->getMessage()));
            }
            // ! VENDOR ONE =====================================================

            // ! VENDOR TWO =====================================================
            $sub_total = 0;
            $total = 0;

            // * creating purchase parent
            $model_two = new Purchase();
            $model_two->loadModel([
                'tipe' => 'transportir',
                'branch_id' => $request->branch_id,
                'tanggal' => Carbon::parse($request->two_target_delivery),
                'vendor_id' => $request->two_vendor_id,
            ]);
            try {
                $model_two->save();
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }

            // * creating purchase transport
            $purchase_transport_two = new PurchaseTransport();
            $purchase_transport_two->fill([
                'delivery_destination' => $request->delivery_destination,
                'send_from' => $request->send_from,
                'po_trading_id' => $request->po_trading_id,
                'branch_id' => $request->branch_id,
                'target_delivery' => Carbon::parse($request->two_target_delivery),
                'purchase_id' => $model_two->id,
                'purchase_request_id' => null,
                'so_trading_id' => $so_trading,
                'vendor_id' => $request->two_vendor_id,
                'ware_house_id' => $request->two_ware_house_id,
                'item_id' => $request->two_item_id,
                'currency_id' => $request->two_currency_id,
                'purchase_transport_id' => $purchase_transport_one->id,
                'exchange_rate' => thousand_to_float($request->two_exchange_rate),
                'type' => $request->type,
                'harga' => thousand_to_float($request->two_price),
                'sub_total' => 0,
                'total' => 0,
            ]);

            try {
                $purchase_transport_two->save();

                $model_two->update([
                    'model_id' => $purchase_transport_two->id,
                    'currency_id' => $purchase_transport_two->currency_id,
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }

            $data_details = [];
            foreach ($request->two_amount_do as $key => $qty) {
                $data_details[] = [
                    'purchase_transport_id' => $purchase_transport_two->id,
                    'jumlah_do' => thousand_to_float($qty),
                    'jumlah' => thousand_to_float($request->two_quantity[$key]),
                    'vehicle_type' => $request->vehicle_type[$key] ?? null,
                    'vehicle_info' => $request->vehicle_info[$key] ?? null,
                ];

                $sub_total = thousand_to_float($qty) *  thousand_to_float($request->two_quantity[$key]) * thousand_to_float($request->two_price);
                $total += thousand_to_float($qty) *  thousand_to_float($request->two_quantity[$key]) * thousand_to_float($request->two_price);
            }

            try {
                $purchase_transport_two->purchase_transport_details()->createMany($data_details);
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }

            // * create purchase transport tax
            if (
                is_array($request->two_tax_id) && count($request->two_tax_id) > 0
            ) {
                $data_tax = [];

                $taxes = Tax::whereIn(
                    'id',
                    $request->two_tax_id
                )->get();

                foreach ($taxes as $key => $tax) {
                    $data_tax[] = [
                        'tax_id' => $tax->id,
                        'purchase_transport_id' => $purchase_transport_two->id,
                        'value' => $tax->value,

                    ];

                    $total += $sub_total * $tax->value;
                }

                try {
                    $purchase_transport_two->purchase_transport_taxes()->createMany($data_tax);
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(
                        false,
                        'create',
                        null,
                        $th->getMessage()
                    ));
                }
            }

            // * update purchase transport total
            $purchase_transport_two->sub_total = $sub_total;
            $purchase_transport_two->total = $total;
            $purchase_transport_two->total_qty = $purchase_transport_two->purchase_transport_details->map(function ($item) {
                return $item->jumlah * $item->jumlah_do;
            })->sum();
            try {
                $purchase_transport_two->save();
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }

            // * update purchase
            $model_two->kode = $purchase_transport_two->kode;
            $model_two->model_reference = PurchaseTransport::class;
            try {
                $model_two->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'create', 'updating purchase code and reference.', $th->getMessage(), 422);
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'updating purchase code and reference.', $th->getMessage()));
            }
            // ! VENDOR TWO =====================================================
        } else {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Type not found'));
        }

        DB::commit();

        return redirect()->route('admin.purchase.index')->with($this->ResponseMessageCRUD(true, 'create', 'purchase transport'));
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
        validate_branch($model->branch_id);

        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }
        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: PurchaseTransport::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );
        $authorization_logs['can_revert'] = false;
        $authorization_logs['can_void'] = $model->check_available_date && in_array($model->status, ['approve']);

        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $is_has_down_payment = $model->purchase->purchase_down_payments()->whereNotIn('status', ['void', 'reject'])->count() > 0;
        $is_has_delivery_order = $model->delivery_orders()->whereNotIn('status', ['void', 'reject'])->count() > 0;

        $authorization_logs['can_revert_request'] = $model->check_available_date && $model->status == 'approve' && !$is_has_down_payment && !$is_has_delivery_order;
        $authorization_logs['can_void_request'] = $model->check_available_date && $model->status == 'approve' && !$is_has_down_payment && !$is_has_delivery_order;
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'auth_revert_void_button'));
    }

    /**K
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $model = model::findOrFail($id);

        validate_branch($model->branch_id);

        // Check Available Date Closing
        if (!$model->check_available_date) {
            abort(403);
        }

        if (!in_array($model->status, ['pending', 'revert'])) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', 'purchase transport', 'status is not valid'));
        }

        if ($model->type == 'double_handling') {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', 'purchase transport', 'Tipe purchase transport tidka valid'));
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

        // Check Available Date Closing
        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal yang dipilih sudah closing'));
        }

        if (!in_array($model->status, ['pending', 'revert'])) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', 'purchase transport', 'status is not valid'));
        }

        if ($model->type == 'double_handling') {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', 'purchase transport', 'Tipe purchase transport tidka valid'));
        }

        if ($model->type == 'not_double_handling') {
            $sub_total = 0;
            $total = 0;

            // * creating purchase parent
            $model = Purchase::where('model_id', $model->id)->where('model_reference', PurchaseTransport::class)->firstOrFail();
            $model->loadModel([
                'tipe' => 'transportir',
                'tanggal' => Carbon::parse($request->target_delivery),
                'vendor_id' => $request->vendor_id,
                'branch_id' => $request->branch_id,
            ]);

            // Check Available Date Closing
            if (!$model->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal yang dipilih sudah closing'));
            }

            try {
                $model->save();
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }

            // * creating purchase transport
            $purchase_transport = PurchaseTransport::findOrFail($id);
            $purchase_transport->fill([
                'branch_id' => $request->branch_id,
                'target_delivery' => Carbon::parse($request->target_delivery),
                'purchase_id' => $model->id,
                'purchase_request_id' => null,
                'vendor_id' => $request->vendor_id,
                'ware_house_id' => $request->ware_house_id,
                'item_id' => $request->item_id,
                'currency_id' => $request->currency_id,
                'exchange_rate' => thousand_to_float($request->exchange_rate),
                'harga' => thousand_to_float($request->price),
                'sub_total' => 0,
                'total' => 0,
            ]);

            // Check Available Date Closing
            if (!$purchase_transport->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal yang dipilih sudah closing'));
            }

            try {
                $purchase_transport->save();

                $model->update([
                    'model_id' => $purchase_transport->id,
                    'currency_id' => $purchase_transport->currency_id,
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }

            // * delete old purchase transport detail
            try {
                DeliveryOrder::where('purchase_transport_id', $purchase_transport->id)->delete();
                $purchase_transport->purchase_transport_details()->delete();
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(
                    false,
                    'create',
                    null,
                    $th->getMessage()
                ));
            }

            // * creating purchase transport detail
            $data_details = [];
            foreach ($request->amount_do as $key => $qty) {
                $data_details[] = [
                    'purchase_transport_id' => $purchase_transport->id,
                    'jumlah_do' => thousand_to_float($qty),
                    'jumlah' => thousand_to_float($request->quantity[$key]),
                    'vehicle_type' => $request->vehicle_type[$key] ?? null,
                    'vehicle_info' => $request->vehicle_info[$key] ?? null,
                ];

                $sub_total += thousand_to_float($qty) *  thousand_to_float($request->quantity[$key]) * thousand_to_float($request->price);
                $total += thousand_to_float($qty) *  thousand_to_float($request->quantity[$key]) * thousand_to_float($request->price);
            }

            try {
                $purchase_transport->purchase_transport_details()->createMany($data_details);
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }

            // * delete old purchase transport tax
            try {
                $purchase_transport->purchase_transport_taxes()->delete();
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(
                    false,
                    'create',
                    null,
                    $th->getMessage()
                ));
            }

            // * create purchase transport tax
            if (is_array($request->tax_id) && count($request->tax_id) > 0) {
                $data_tax = [];

                $taxes = Tax::whereIn('id', $request->tax_id)->get();

                foreach ($taxes as $key => $tax) {
                    $data_tax[] = [
                        'tax_id' => $tax->id,
                        'purchase_transport_id' => $purchase_transport->id,
                        'value' => $tax->value,

                    ];

                    $total += $sub_total * $tax->value;
                }

                try {
                    $purchase_transport->purchase_transport_taxes()->createMany($data_tax);
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
                }
            }

            // * update purchase transport total
            $purchase_transport->sub_total = $sub_total;
            $purchase_transport->total = $total;
            $purchase_transport->total_qty = $purchase_transport->purchase_transport_details->map(function ($item) {
                return $item->jumlah * $item->jumlah_do;
            })->sum();
            try {
                $purchase_transport->save();

                $authorization = new \App\Http\Helpers\AuthorizationHelper();
                $authorization->init(
                    branch_id: $purchase_transport->branch_id,
                    user_id: auth()->user()->id,
                    model: PurchaseTransport::class,
                    model_id: $purchase_transport->id,
                    amount: $purchase_transport->total ?? 0,
                    title: "PO Transport",
                    subtitle: Auth::user()->name . " mengajukan PO Transport " . $purchase_transport->kode,
                    link: route('admin.purchase-order-transport.show', $purchase_transport),
                    update_status_link: route('admin.purchase-order-transport.update-status', ['id' => $purchase_transport->id]),
                    division_id: auth()->user()->division_id ?? null
                );
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }

            // * update purchase
            $model->kode = $purchase_transport->kode;
            $model->model_reference = PurchaseTransport::class;
            try {
                $model->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'create', 'updating purchase code and reference.', $th->getMessage(), 422);
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'updating purchase code and reference.', $th->getMessage()));
            }
        }

        DB::commit();

        return redirect()->route('admin.purchase.index')->with($this->ResponseMessageCRUD(true, 'create', 'purchase transport'));
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
        $is_has_down_payment = $model->purchase->purchase_down_payments()->whereNotIn('status', ['void', 'reject'])->count() > 0;
        if ($is_has_down_payment) {
            return $this->ResponseJsonMessageCRUD(false, 'delete', null, 'PO telah memiliki uang muka');
        }
        validate_branch($model->branch_id);

        DB::beginTransaction();
        try {
            $model->delete();

            Authorization::where('model', PurchaseTransport::class)
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
     * update status purchase order transport
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required',
            'message' => 'nullable'
        ]);


        $model = model::findOrFail($id);
        validate_branch($model->branch_id);

        $is_has_down_payment = $model->purchase->purchase_down_payments()->whereNotIn('status', ['void', 'reject'])->count() > 0;
        if (in_array($request->status, ['revert', 'void']) && $is_has_down_payment) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'PO telah memiliki uang muka'));
        }

        Db::beginTransaction();

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
                if ($request->status == 'approve') {
                    $model->approved_by = auth()->user()->id;
                }
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
            return redirect()->route("admin.purchase-order-transport.show", $model)->with($this->ResponseMessageCRUD(false, 'update', 'update status', $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.purchase-order-transport.show", $model)->with($this->ResponseMessageCRUD(true, 'update', 'update status'));
    }

    /**
     * POT export
     *
     *
     *
     */
    public function export($id, Request $request)
    {
        if (!$request->preview && authorizePrint('purchase_order_transport')) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                model::class,
                decryptId($id),
                'purchase_order_transport',
            );

            if (!$result) {
                return abort(403);
            }
        }

        $model = model::findOrFail(decryptId($id));
        $file = public_path('/pdf_reports/Report-Purchase-Transport-' . microtime(true) . '.pdf');
        $fileName = 'Report-Purchase-Transport-' . microtime(true) . '.pdf';
        // purchase_transport_details
        $purchase_request_code = PurchaseRequest::whereIn('id', $model->purchase_transport_details->pluck('purchase_request_id')->toArray())->pluck('kode')->toArray();
        $purchase_order_general_item = PurchaseTransportDetail::whereIn('purchase_transport_id', $model->purchase_transport_details->pluck('id')->toArray())->get();

        $qr_url = route('purchase-order-transport.export.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));

        $approval = Authorization::where('model', model::class)
            ->with(['details' => function ($q) {
                $q->where('status', 'approve')
                    ->where('note', 'not like', '%otomatis%');
            }])
            ->where('model_id', $model->id)
            ->first();

        $pdf = PDF::loadview("admin/.$this->view_folder./export", compact('model', 'qr', 'purchase_request_code', 'purchase_order_general_item', 'approval'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $w = $canvas->get_width();
        $h = $canvas->get_height();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM} / {PAGE_COUNT}", '', 8);

        if ($request->preview) {
            Storage::disk('public')->deleteDirectory('tmp_purchase_order_transport');
            $tmp_file_name = 'purchase_order_transport_' . time() . '.pdf';
            $path = 'tmp_purchase_order_transport/' . $tmp_file_name;
            Storage::disk('public')->put($path, $pdf->output());

            return response()->json($path);
        }

        return $pdf->stream($fileName);
    }

    /**
     * select api for lpb
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function select_for_lpb(Request $request)
    {
        $model = model::with('vendor')
            ->leftJoin('item_receiving_reports', function ($join) {
                $join->on('item_receiving_reports.reference_id', 'purchase_transports.id')
                    ->where('item_receiving_reports.reference_model', model::class)
                    ->whereIn('item_receiving_reports.status', ['pending', 'approve', 'done'])
                    ->whereNull('item_receiving_reports.deleted_at');
            })
            ->where(function ($q) use ($request) {
                $q->whereHas('delivery_orders', function ($q) {
                    $q->where('is_item_receiving_report_created', 0)
                        ->whereIn('status', ['done']);
                })
                    ->orWhere(function ($q) {
                        $q->where('purchase_transports.delivery_destination', 'to_warehouse')
                            ->whereNull('item_receiving_reports.id');
                    });
            })
            ->join('vendors', 'vendors.id', 'purchase_transports.vendor_id')
            ->leftJoin('sale_orders', 'sale_orders.id', 'purchase_transports.so_trading_id')
            ->leftJoin('customers', 'customers.id', 'sale_orders.customer_id')
            ->when($request->branch_id, function ($query) use ($request) {
                $query->where('purchase_transports.branch_id', $request->branch_id);
            })
            ->where(function ($query)  use ($request) {
                $query->where('purchase_transports.status', 'approve')
                    ->orWhere('purchase_transports.status', 'partial-sent')
                    ->when($request->selected_id, function ($query) use ($request) {
                        $query->orWhere('purchase_transports.id', $request->selected_id);
                    });
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('purchase_transports.kode', "like", "%$request->search%")
                        ->orWhere('vendors.nama', "like", "%$request->search%")
                        ->orWhere('customers.nama', "like", "%$request->search%");
                });
            })
            ->groupBy('purchase_transports.id')
            ->selectRaw(
                'purchase_transports.*,
                customers.nama as customer_name',
            )
            ->orderByDesc('purchase_transports.created_at')
            ->paginate(10);

        return $this->ResponseJson($model);
    }

    /**
     * detail api for lpb
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function detail_lpb($id)
    {
        $model = model::with(['vendor', 'so_trading.customer'])->findOrFail($id);

        $delivery_orders = $model->delivery_orders()
            ->where('status', 'done')
            ->where('is_item_receiving_report_created', false)
            ->get()
            ->map(function ($item) {
                $item->unit = $item->so_trading->so_trading_detail->item->unit->name ?? '';
                return $item;
            });

        return $this->ResponseJsonData(compact('model', 'delivery_orders'));
    }

    /**
     * coas
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function purchase_coa($id = null)
    {
        $model = model::findOrFail($id);

        return $this->ResponseJsonData(new PurchaseTransportCoaResource($model));
    }

    /**
     * check_stock
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function check_stock(Request $request)
    {
        // * get stock in
        $in = \App\Models\StockMutation::where('ware_house_id', $request->ware_house_id)
            ->where('item_id', $request->item_id)
            ->whereNull('is_return')
            ->sum('in');

        // * get stock out
        $out = \App\Models\StockMutation::where('ware_house_id', $request->ware_house_id)
            ->where('item_id', $request->item_id)
            ->whereNull('is_return')
            ->sum('out');

        return $this->ResponseJsonData([
            'stock' => $in - $out,
        ]);
    }

    /**
     * select api for delivery order
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function deliveryOrderSelect(Request $request)
    {
        $data = \App\Models\DeliveryOrder::with([
            'so_trading',
            'purchase_transport',
        ])
            ->whereIn('delivery_orders.status', ['approve', 'partial-used'])
            ->where('delivery_orders.unload_quantity_realization', '>', 'quantity_used')
            ->when($request->search, function ($query) use ($request) {
                return $query->where('delivery_orders.code', 'like', "%$request->search%")
                    ->orWhere('delivery_orders.target_delivery', 'like', "%$request->search%")
                    ->orWhere('so_trading.nomor_so', 'like', "%$request->search%")
                    ->orWhere('purchase_transports.kode', 'like', "%$request->search%");
            })
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return $this->ResponseJsonData($data);
    }

    /**
     * detail api for delivery order
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deliveryOrderCheckStock($id)
    {
        $model = \App\Models\DeliveryOrder::findOrFail($id);
        $stock = $model->unload_quantity_realization - $model->quantity_used;

        return $this->ResponseJsonData(compact('stock'));
    }

    /**
     * Get data for edit
     */
    public function getDataForEdit($id)
    {
        $model = model::with([
            'item',
            'so_trading.sh_number.sh_number_details',
            'so_trading.so_trading_detail.item',
            'so_trading.customer',
            'po_trading.po_trading_detail',
            'vendor',
            'currency',
            'ware_house',
            'branch',
            'purchase_transport_details',
            'purchase_transport_taxes.tax',
        ])->findOrFail($id);

        return $this->ResponseJsonData($model);
    }

    public function history($id, Request $request)
    {
        try {
            $purchase_orders = DB::table('purchase_transports')
                ->where('id', $id)
                ->select(
                    'purchase_transports.id',
                    'purchase_transports.kode as code',
                    'purchase_transports.target_delivery as date',
                    'purchase_transports.status',
                )
                ->get();

            $purchase_orders = $purchase_orders->map(function ($item) {
                $item->link = route('admin.purchase-order-transport.show', $item->id);
                $item->menu = 'purchase order transport';
                return $item;
            });

            $item_receiving_reports = DB::table('item_receiving_reports')
                ->where('reference_model', PurchaseTransport::class)
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

            $histories = $purchase_orders->unique('id')
                ->merge($item_receiving_reports->unique('id'))
                ->merge($supplier_invoices->unique('id'))
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

    /**
     * Get the database for list of delivery orders.
     *
     * @author @zulfikar-ditya
     * @param Request $request
     * @param string|int $id purchase transport id.
     * @return ResponseJson
     */
    public function dataTableDeliveryOrders(Request $request, $id)
    {
        if ($request->ajax()) {
            $model = model::findOrFail($id);
            validate_branch($model->branch_id);
            $data = DeliveryOrder::where('purchase_transport_id', $id)
                ->when($request->type, function ($q) use ($request) {
                    $q->where('type', $request->type);
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('code', function ($row) use ($model) {
                    $code = '<a href="' . route("admin.delivery-order.list-delivery-order.show", ['sale_order_id' => $row->so_trading_id, 'delivery_order_id' => $row->id]) . '" target="_blank" class="text-primary text-decoration-underline hover_text-dark">' . $row->code . '</a>';
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
                ->addColumn('moda_transport', fn($row) => $row->purchase_transport_detail_id ? 'Transportir' : 'Own Use')
                ->rawColumns(['code', 'status'])
                ->make(true);
        }

        return abort(503);
    }

    public function repair_calculation()
    {
        DB::beginTransaction();

        try {
            $purchase_transports = PurchaseTransport::where('type', 'not_double_handling')->get();
            foreach ($purchase_transports as $key => $purchase_transport) {
                $subtotal = 0;
                foreach ($purchase_transport->purchase_transport_details as $key => $purchase_transport_detail) {
                    $subtotal += $purchase_transport->harga * $purchase_transport_detail->jumlah * $purchase_transport_detail->jumlah_do;
                }

                $total = $subtotal;
                foreach ($purchase_transport->purchase_transport_taxes as $key => $purchase_transport_tax) {
                    $total += $subtotal * $purchase_transport_tax->value;
                }

                DB::table('purchase_transports')
                    ->where('id', $purchase_transport->id)
                    ->update([
                        'sub_total' => $subtotal,
                        'total' => $total
                    ]);
            }

            DB::commit();

            return 'success';
        } catch (\Throwable $th) {
            DB::rollBack();
            return 'failed';
        }
    }
}
