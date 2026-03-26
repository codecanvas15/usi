<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Http\Helpers\PrintHelper;
use App\Http\Resources\Admin\PoTradingCoaResource;
use App\Models\AccountPayable;
use App\Models\Authorization;
use App\Models\ItemReceivingPoTrading;
use App\Models\ItemReceivingPoTradingAdditional;
use App\Models\PairingSoToPo;
use App\Models\PoTrading as model;
use App\Models\PoTrading;
use App\Models\PoTradingDetail;
use App\Models\Purchase;
use App\Models\PurchaseOrderTax;
use App\Models\PurchaseOrderAdditionalTaxs;
use App\Models\PurchaseOrderAdditionalItems;
use App\Models\PurchaseRequestTrading;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\DataTables;

class PoTradingController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'purchase-order';

    /**
     * search  value
     *
     * @var string|null
     */
    protected string|null $search = null;

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
            $checkAuthorizePrint = authorizePrint('purchase_order_trading');
            // * get data with date
            $query = model::with(['sh_number', 'customer'])
                ->join('sh_numbers', 'sh_numbers.id', 'purchase_orders.sh_number_id')
                ->join('customers', 'customers.id', 'purchase_orders.customer_id')
                ->join('vendors', 'vendors.id', 'purchase_orders.vendor_id')
                ->join('purchase_order_details', 'purchase_order_details.po_trading_id', 'purchase_orders.id')
                ->join('purchases', function ($query) {
                    $query->on('purchases.model_id', 'purchase_orders.id')
                        ->where('model_reference', model::class);
                })
                ->leftJoin('cash_advance_payments', function ($query) {
                    $query->on('cash_advance_payments.purchase_id', 'purchases.id')
                        ->whereNull('cash_advance_payments.deleted_at')
                        ->where('cash_advance_payments.status', 'approve');
                })
                ->when($request->status, fn($q) => $q->where('purchase_orders.status', $request->status))
                ->when($request->customer_id, fn($q) => $q->where('purchase_orders.customer_id', $request->customer_id))
                ->when($request->branch_id, fn($q) => $q->where('purchase_orders.branch_id', $request->branch_id))
                ->when($request->from_date, function ($query) use ($request) {
                    $query->whereDate('purchase_orders.tanggal', '>=', Carbon::parse($request->from_date));
                })
                ->when($request->to_date, function ($query) use ($request) {
                    $query->whereDate('purchase_orders.tanggal', '<=', Carbon::parse($request->to_date));
                })
                ->groupBy('purchase_orders.id')
                ->select(
                    'purchase_orders.*',
                    'cash_advance_payments.id as cash_advance_payment_id',
                    'sh_numbers.kode as sh_number_code',
                    'vendors.nama as vendor_name',
                    'customers.nama as customer_name',
                    'purchase_order_details.jumlah',
                    'purchase_order_details.type as purchase_order_detail_type',
                );

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . status_purchase_orders()[$row->status]['color'] . '">
                                ' . status_purchase_orders()[$row->status]['label'] . ' - ' . status_purchase_orders()[$row->status]['text'] . '
                            </div>';

                    if (!in_array($row->status, ['done', 'ready']) && $row->cash_advance_payment_id) {
                        $badge .= '<br><div class="badge badge-pill badge-danger  mt-1 animate__animated animate__pulse animate__infinite infinite">Uang Muka Telah dibayar!</div>';
                    }

                    return $badge;
                })
                ->editColumn('pairing_status', function ($row) {
                    $pairing_badge = '<div class="badge badge-lg badge-' . pairing_status()[$row->pairing_status]['color'] . '">
                        ' . pairing_status()[$row->pairing_status]['label'] . ' - ' . pairing_status()[$row->pairing_status]['text'] . '
                        </div>';

                    return $pairing_badge;
                })->editColumn('nomor_po', function ($row) use ($checkAuthorizePrint) {
                    $link_export = route('purchase-order.export.id', ['id' => encryptId($row->id)]);

                    return '<a href="' . route("admin.$this->view_folder.index") . '/' . $row->id . '" class="text-primary">' . $row->nomor_po . '</a>' . '<br><a href="' . $link_export . '" class="btn btn-sm btn-info" target="_blank" onclick="show_print_out_modal(event)" ' . ($checkAuthorizePrint ? 'data-model="' . model::class . '" data-id="' . $row->id . '" data-print-type="purchase_order_trading" data-link="' . route("admin.$this->view_folder.index") . '/' . $row->id . '" data-code="' . $row->nomor_po . '"' : '') . ' >Export</a>';
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
                })
                ->editColumn('tanggal', function ($row) {
                    return Carbon::parse($row->tanggal)->format('d-m-Y');
                })
                ->editColumn('jumlah', function ($row) {
                    return formatNumber((float) $row->jumlah) . " " . $row->purchase_order_detail_type;
                })
                ->addColumn('action', function ($row) {
                    return Blade::render('admin.purchase-order.btn-data-table', [
                        'row' => $row,
                    ]);
                })
                ->rawColumns(['status', 'pairing_status', 'nomor_po', 'action'])
                ->make(true);
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
        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(PoTrading::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

        $model = [];
        return view('admin.' . $this->view_folder . '.create', compact('model'));
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

        // Validate main quantity is not NaN and 0
        if ($request->jumlah == 0) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", null, 'Jumlah tidak boleh sama dengan 0'));
        } elseif ($request->jumlah == "NaN") {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", null, 'Jumlah tidak boleh string'));
        }

        // * validate
        if ($request->ajax()) {
            $this->validate_api($request->all(), [
                'purchase_type' => 'required|in:trading'
            ]);
        } else {
            $this->validate($request, [
                'purchase_type' => 'required|in:trading'
            ]);
        }

        // * creating purchase
        $model = new Purchase();
        $model->loadModel([
            'branch_id' => $request->branch_id ?? get_current_branch_id(),
            'tipe' => $request->purchase_type,
            'tanggal' => Carbon::parse($request->tanggal),
            'vendor_id' => $request->vendor_id,
        ]);

        // Check Available Date Closing
        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal yang dipilih sudah closing'));
        }

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        // ! ################################ purchase trading create ###################################
        // * validate
        if ($request->ajax()) {
            $this->validate_api($request->all(), model::rules());
        } else {
            $this->validate($request, model::rules());
        }

        $code2 = \App\Models\Vendor::find($request->vendor_id);
        // * create data
        $po_trading = new model();
        $po_trading->nomor_po = generate_code_with_cus_name(
            model: model::class,
            code: 'PO',
            code2: $code2,
            date_column: 'tanggal',
            date: Carbon::parse($request->tanggal) ?? \Carbon\Carbon::now()->format('Y-m-d'),
            filter: [],
        );

        $purchase_request_trading = \App\Models\PurchaseRequestTrading::find($request->purchase_request_trading_id);
        $purchase_request_trading_detail = $purchase_request_trading->purchase_request_trading_details[0] ?? null;

        $sale_order = \App\Models\SoTrading::find($request->sale_order_id);

        $po_trading->loadModel([
            'branch_id' => $request->branch_id ?? get_current_branch_id(),
            'purchase_request_trading_id' => $request->sale_order_id ? null : $request->purchase_request_trading_id,
            'sale_order_id' => $request->purchase_request_trading_id ? null :  $request->sale_order_id,
            'tanggal' => Carbon::parse($request->tanggal),
            'customer_id' => $purchase_request_trading->customer_id  ?? $sale_order->customer_id  ?? $request->customer_id,
            'vendor_id' => $request->vendor_id,
            'sh_number_id' => $purchase_request_trading ? $purchase_request_trading->sh_number_id :  $request->sh_number_id,
            'sale_confirmation' => $request->sale_confirmation,
            'purchase_id' => $model->id,
            'other_cost' => thousand_to_float($request->other_cost ?? 0),
            'currency_id' => $request->currency_id,
            'exchange_rate' => thousand_to_float($request->exchange_rate ?? 0),
            'quotation' => $request->hasFile('quotation') ? $this->upload_file($request->file('quotation'), 'purchase-order-quotation/quotation') : null,
            'note' => $request->note ?? '',
            'top' => $request->top,
            'top_day' => $request->top_day,
        ]);

        // * saving and make reponse
        try {
            $po_trading->save();

            $model->currency_id = $po_trading->currency_id;
            $model->model_id = $po_trading->id;
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        // * save po trading detail
        $dpp = thousand_to_float($request->dpp_trading); // * harga - discount
        $discount = thousand_to_float($request->discount);

        $main_sub_total = $dpp * thousand_to_float($request->jumlah);
        $main_tax_total = 0;
        $main_total = 0;

        $model_detail = new PoTradingDetail();
        $model_detail->loadModel([
            'po_trading_id' => $po_trading->id,
            'item_id' => $purchase_request_trading_detail->item_id ?? $sale_order->so_trading_detail->item_id ?? $request->item_id,
            'harga' => thousand_to_float($request->harga),
            'jumlah' => thousand_to_float($request->jumlah),
            'type' => $request->type,
            'keterangan' => $request->keterangan,
            'discount_per_liter' => $discount,
        ]);

        try {
            $model_detail->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        // save po trading tax
        $main_sub_total_after_tax = $main_sub_total;
        if ($request->ppn_id) {
            $ppn = Tax::find($request->ppn_id);

            if ($ppn) {
                $sub_total = ($dpp * $ppn->value) * thousand_to_float($request->jumlah);
                $main_tax_total += $sub_total;

                $model_tax = new PurchaseOrderTax();
                $model_tax->loadModel([
                    'tax_id' => null,
                    'po_trading_id' => $po_trading->id,
                    'value' => $ppn->value,
                    'total' => $sub_total,
                    'tax_trading_id' => $ppn->id,
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

        $tax_list = $request->tax_id_trading;
        if (is_array($tax_list)) {
            foreach ($tax_list as $key => $value) {
                $tax = Tax::find($value);

                $sub_total = (thousand_to_float($request->harga) * $tax->value) * thousand_to_float($request->jumlah);
                $main_tax_total += $sub_total;

                $model_tax = new PurchaseOrderTax();
                $model_tax->loadModel([
                    'tax_id' => $tax->id,
                    'po_trading_id' => $po_trading->id,
                    'value' => $tax->value,
                    'total' => $sub_total,
                    'tax_trading_id' => null,
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

        $main_sub_total_after_tax += $main_sub_total + $main_tax_total;
        $main_total += $main_sub_total + $main_tax_total;

        $additional_total = 0;
        if (is_array($request->additional_item_id)) {
            foreach ($request->additional_item_id as $key => $value) {
                $tax_key = $request->additional_item_row_index[$key];

                $single_additional = thousand_to_float($request->additional_price[$key]) * thousand_to_float($request->additional_qty[$key]);

                $model_detail = new PurchaseOrderAdditionalItems();
                $model_detail->loadModel([
                    'po_trading_id' => $po_trading->id,
                    'item_id' => $value,
                    'harga' => thousand_to_float($request->additional_price[$key]),
                    'jumlah' => thousand_to_float($request->additional_qty[$key]),
                    'sub_total' => $single_additional,
                    'total' => 0,
                ]);

                try {
                    $model_detail->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
                    }

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
                }

                $single_tax_total = 0;
                if (isset($_REQUEST['additional_tax_id_' . $tax_key])) {
                    foreach ($_REQUEST['additional_tax_id_' . $tax_key] as $key2 => $value2) {
                        $tax = Tax::find($value2);
                        if ($tax) {
                            $tax_total = $single_additional * $tax->value;
                            $model_tax = new PurchaseOrderAdditionalTaxs();
                            $model_tax->loadModel([
                                'tax_id' => $tax->id,
                                'po_additional_id' => $model_detail->id,
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

                $model_detail->total = $single_additional;
                try {
                    $model_detail->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
                    }

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
                }
            }
        }

        // * update total and sub total in po trading
        $po_trading->sub_total = $main_sub_total;
        $po_trading->sub_total_after_tax = $main_sub_total_after_tax;
        $po_trading->other_cost = $additional_total;
        $po_trading->total = $main_total + $additional_total;

        try {
            $po_trading->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        // * updating purchase code and reference
        $model->kode = $po_trading->nomor_po;
        $model->model_reference = PoTrading::class;
        try {
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $po_trading->branch_id,
                user_id: auth()->user()->id,
                model: PoTrading::class,
                model_id: $po_trading->id,
                amount: $po_trading->total ?? 0,
                title: "PO Trading",
                subtitle: Auth::user()->name . " mengajukan PO trading " . $po_trading->nomor_po,
                link: route('admin.purchase-order.show', $po_trading),
                update_status_link: route('admin.purchase-order.update_status', ['id' => $po_trading->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'updating purchase code and reference.', $th->getMessage()));
        }
        // ! ################################ purchase trading create ###################################

        DB::commit();

        return redirect()->route('admin.purchase.index')->with($this->ResponseMessageCRUD(true, 'create', 'create purchase trading'));
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

        $model->po_trading_details_additional = PurchaseOrderAdditionalItems::where('po_trading_id', $id)->get();

        validate_branch($model->branch_id);

        if ($request->ajax()) {
            $model->customer;
            $model->po_trading_detail->item;
            return $this->ResponseJsonData(compact('model'));
        }

        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: PoTrading::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );
        $authorization_logs['can_revert'] = $model->check_available_date && $model->status == 'approve';
        $authorization_logs['can_void'] = $model->check_available_date && $model->status == 'approve';

        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $is_has_down_payment = $model->purchase->purchase_down_payments()->whereNotIn('status', ['void', 'reject'])->count() > 0;
        if ($model->check_available_date && ($model->status == 'approve' || ($model->pairing_status == 'pairing' && $model->status == 'approve')) && !$is_has_down_payment) {
            $authorization_logs['can_revert_request'] = true;
            $authorization_logs['can_void_request'] = true;
        } else {
            $authorization_logs['can_revert_request'] = false;
            $authorization_logs['can_void_request'] = false;
        }

        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'authorization_logs', 'auth_revert_void_button'));
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
        $is_has_down_payment = $model->purchase->purchase_down_payments()->whereNotIn('status', ['void', 'reject'])->count() > 0;
        if ($is_has_down_payment) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'PO telah memiliki uang muka'));
        }

        if (!in_array($model->status, ['pending', 'revert'])) {
            abort(403);
        }

        // Check available date closing
        if (!$model->check_available_date) {
            return abort(403);
        }

        validate_branch($model->branch_id);
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
        $po_trading = model::findOrFail($id);
        if (!in_array($po_trading->status, ['pending', 'revert'])) {
            abort(403);
        }

        // Check Available Date Closing
        if (!$po_trading->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal yang dipilih sudah closing'));
        }
        validate_branch($po_trading->branch_id);

        DB::beginTransaction();
        // * validate
        if ($request->ajax()) {
            $this->validate($request, model::rules());
        } else {
            $this->validate_api($request->all(), model::rules());
        }

        // Validate main quantity is not NaN and 0
        if ($request->jumlah == 0) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", null, 'Jumlah tidak boleh sama dengan 0'));
        } elseif ($request->jumlah == "NaN") {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", null, 'Jumlah tidak boleh string'));
        }

        $purchase_request_trading = PurchaseRequestTrading::find($request->purchase_request_trading_id);
        $purchase_request_trading_detail = $purchase_request_trading->purchase_request_trading_details[0] ?? null;

        $sale_order = \App\Models\SoTrading::find($request->sale_order_id);

        // * update data
        $po_trading->loadModel([
            'branch_id' => $request->branch_id ?? get_current_branch_id(),
            'tanggal' => Carbon::parse($request->tanggal),
            'purchase_request_trading_id' => $request->sale_order_id ? null : $request->purchase_request_trading_id,
            'sale_order_id' => $request->purchase_request_trading_id ? null :  $request->sale_order_id,
            'customer_id' => $purchase_request_trading ? $purchase_request_trading->customer_id : $request->customer_id,
            'sh_number_id' => $purchase_request_trading ? $purchase_request_trading->sh_number_id : $request->sh_number_id,
            'customer_id' => $purchase_request_trading->customer_id  ?? $sale_order->customer_id  ?? $request->customer_id,
            'kode' => generate_trading_code_update($po_trading->nomor_po),
            'sale_confirmation' => $request->sale_confirmation,
            'currency_id' => $request->currency_id,
            'exchange_rate' => thousand_to_float($request->exchange_rate ?? 0),
            'quotation' => $request->hasFile('quotation') ? $this->upload_file($request->file('quotation'), 'purchase-order-quotation/quotation') : null,
            'note' => $request->note ?? '',
            'top' => $request->top,
            'top_day' => $request->top_day,
        ]);

        // * saving and make reponse
        try {
            $po_trading->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        $po_additionals = PurchaseOrderAdditionalItems::where('po_trading_id', $po_trading->id)->get();
        $ids = $po_additionals->pluck('id')->toArray();

        PurchaseOrderTax::where('po_trading_id', $po_trading->id)->delete();
        PurchaseOrderAdditionalTaxs::whereIn('po_additional_id', $ids)->delete();
        PurchaseOrderAdditionalItems::where('po_trading_id', $po_trading->id)->delete();
        PairingSoToPo::where('po_trading_detail_id', $po_trading->po_trading_detail->id)->delete();
        PoTradingDetail::where('po_trading_id', $po_trading->id)->delete();

        // * save po trading detail
        $dpp = thousand_to_float($request->dpp_trading);
        $discount = thousand_to_float($request->discount);

        $main_sub_total = $dpp * thousand_to_float($request->jumlah);
        $main_tax_total = 0;
        $main_total = 0;

        $model_detail = new PoTradingDetail();
        $model_detail->loadModel([
            'po_trading_id' => $po_trading->id,
            'item_id' => $purchase_request_trading_detail->item_id ?? $sale_order->so_trading_detail->item_id ?? $request->item_id,
            'harga' => thousand_to_float($request->harga),
            'jumlah' => thousand_to_float($request->jumlah),
            'type' => $request->type,
            'keterangan' => $request->keterangan,
            'discount_per_liter' => $discount,
        ]);

        try {
            $model_detail->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        // save po trading tax
        $main_sub_total_after_tax = 0;
        if ($request->ppn_id) {
            $ppn = Tax::find($request->ppn_id);

            if ($ppn) {
                $sub_total = ($dpp * $ppn->value) * thousand_to_float($request->jumlah);
                $main_tax_total += $sub_total;

                $model_tax = new PurchaseOrderTax();
                $model_tax->loadModel([
                    'tax_id' => null,
                    'po_trading_id' => $po_trading->id,
                    'value' => $ppn->value,
                    'total' => $sub_total,
                    'tax_trading_id' => $ppn->id,
                ]);

                try {
                    $model_tax->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return $this->ResponseJsonMessageCRUD(false, 'edit', 'edit tax', $th->getMessage(), 422);
                    }

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', 'edit tax', $th->getMessage()));
                }
            }
        }

        $tax_list = $request->tax_id_trading;
        if (is_array($tax_list)) {
            foreach ($tax_list as $key => $value) {
                $tax = Tax::find($value);

                $sub_total = (thousand_to_float($request->harga) * $tax->value) * thousand_to_float($request->jumlah);
                $main_tax_total += $sub_total;

                $model_tax = new PurchaseOrderTax();
                $model_tax->loadModel([
                    'tax_id' => $tax->id,
                    'po_trading_id' => $po_trading->id,
                    'value' => $tax->value,
                    'total' => $sub_total,
                    'tax_trading_id' => null,
                ]);

                try {
                    $model_tax->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return $this->ResponseJsonMessageCRUD(false, 'edit', 'edit tax', $th->getMessage(), 422);
                    }

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', 'edit tax', $th->getMessage()));
                }
            }
        }

        $main_sub_total_after_tax += $main_sub_total + $main_tax_total;
        $main_total += $main_sub_total + $main_tax_total;

        $additional_total = 0;
        if (is_array($request->additional_item_id)) {
            foreach ($request->additional_item_id as $key => $value) {
                $tax_key = $request->additional_item_row_index[$key];

                $single_additional = thousand_to_float($request->additional_price[$key]) * thousand_to_float($request->additional_qty[$key]);

                $model_detail = new PurchaseOrderAdditionalItems();
                $model_detail->loadModel([
                    'po_trading_id' => $po_trading->id,
                    'item_id' => $value,
                    'harga' => thousand_to_float($request->additional_price[$key]),
                    'jumlah' => thousand_to_float($request->additional_qty[$key]),
                    'sub_total' => $single_additional,
                    'total' => 0,
                ]);

                try {
                    $model_detail->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
                    }

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
                }

                $single_tax_total = 0;
                if (isset($_REQUEST['additional_tax_id_' . $tax_key])) {
                    foreach ($_REQUEST['additional_tax_id_' . $tax_key] as $key2 => $value2) {
                        $tax = Tax::find($value2);
                        if ($tax) {
                            $tax_total = $single_additional * $tax->value;
                            $model_tax = new PurchaseOrderAdditionalTaxs();
                            $model_tax->loadModel([
                                'tax_id' => $tax->id,
                                'po_additional_id' => $model_detail->id,
                                'value' => $tax->value,
                                'total' => $tax_total,
                            ]);

                            try {
                                $model_tax->save();
                            } catch (\Throwable $th) {
                                DB::rollBack();

                                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', 'edit tax', $th->getMessage()));
                            }

                            $single_tax_total += $tax_total;
                        }
                    }
                }

                // * set total sale order additional
                $single_additional += $single_tax_total;
                $additional_total += $single_additional;

                $model_detail->total = $single_additional;
                try {
                    $model_detail->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
                    }

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
                }
            }
        }

        // * update total and sub total in po trading
        $po_trading->sub_total = $main_sub_total;
        $po_trading->sub_total_after_tax = $main_sub_total_after_tax;
        $po_trading->other_cost = $additional_total;
        $po_trading->total = $main_total + $additional_total;

        try {
            $po_trading->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $po_trading->branch_id,
                user_id: auth()->user()->id,
                model: PoTrading::class,
                model_id: $po_trading->id,
                amount: $po_trading->total ?? 0,
                title: "PO Trading",
                subtitle: Auth::user()->name . " mengajukan PO trading " . $po_trading->nomor_po,
                link: route('admin.purchase-order.show', $po_trading),
                update_status_link: route('admin.purchase-order.update_status', ['id' => $po_trading->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD(true, 'edit');
        }

        return redirect()->route("admin.purchase.index")->with($this->ResponseMessageCRUD(true, 'edit'));
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
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'PO telah memiliki uang muka'));
        }

        if (!in_array($model->status, ['pending', 'revert'])) {
            abort(403);
        }
        // Check Available Date Closing
        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal yang dipilih sudah closing'));
        }
        validate_branch($model->branch_id);

        DB::beginTransaction();
        try {
            $model->delete();

            Authorization::where('model', PoTrading::class)->where('model_id', $id)->delete();
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

        return redirect()->route("admin.purchase.index")->with($this->ResponseMessageCRUD(true, 'delete'));
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
        $status = status_purchase_orders();
        $new = '';
        foreach ($status as $key => $value) {
            $new .= $key . ',';
        }
        if ($request->ajax()) {
            $this->validate_api($request->all(), [
                'status' => 'required|in:' . $new,
            ]);
        } else {
            $this->validate($request, [
                'status' => 'required|in:' . $new,
            ]);
        }

        $model = model::findOrFail($id);

        $is_has_down_payment = $model->purchase->purchase_down_payments()->whereNotIn('status', ['void', 'reject'])->count() > 0;
        if (in_array($request->status, ['revert', 'void']) && $is_has_down_payment) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'PO telah memiliki uang muka'));
        }

        // Check available date closing
        if (!$model->check_available_date) {
            return abort(403);
        }
        validate_branch($model->branch_id);

        // * model approve check sale confirmation and nomor po external
        if ($request->status == 'approve') {
            if (!$model->sale_confirmation) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'sale confirmation not available'));
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
     * update sale confirmation
     *
     * @param  Request  $request
     * @param int id
     * @return Response
     */
    public function update_sale_confirmation(Request $request, $id)
    {
        DB::beginTransaction();
        $model = model::findOrFail($id);
        validate_branch($model->branch_id);

        // Check available date closing
        if (!$model->check_available_date) {
            return abort(403);
        }

        $model->sale_confirmation = $request->sale_confirmation;
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'update', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD(true, 'update', 'update sale confirmation');
        }

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, 'update', 'update sale confirmation'));
    }

    /**
     * select api for lpb
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function select_for_lpb(Request $request)
    {
        $model = model::leftJoin('purchase_order_details', 'purchase_order_details.po_trading_id', 'purchase_orders.id')
            ->with(['customer'])
            ->when($request->branch_id, function ($q) use ($request) {
                return $q->where('purchase_orders.branch_id', $request->branch_id);
            })
            ->where('purchase_orders.nomor_po', 'like', "%$request->search%")
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->whereNotIn('purchase_orders.status', ['reject', 'pending', 'done', 'close', 'void'])
                        ->whereRaw('purchase_order_details.jumlah > purchase_order_details.jumlah_lpbs');
                })
                    ->orWhere(function ($q) {
                        $q->whereNotIn('purchase_orders.status', ['reject', 'pending', 'void', 'close'])
                            ->whereHas('purchase_order_additionals', function ($q) {
                                $q->where('purchase_order_additional_items.jumlah', '>', function ($query) {
                                    $query->select(DB::raw('SUM(receive_qty)'))
                                        ->from('item_receiving_po_trading_additionals')
                                        ->join('item_receiving_reports', 'item_receiving_reports.id', '=', 'item_receiving_po_trading_additionals.item_receiving_report_id')
                                        ->whereColumn('item_receiving_po_trading_additionals.purchase_order_additional_items_id', 'purchase_order_additional_items.id')
                                        ->whereIn('item_receiving_reports.status', ['pending', 'approve', 'revert', 'done'])
                                        ->groupBy('item_receiving_po_trading_additionals.purchase_order_additional_items_id');
                                });
                            });
                    });
            })
            ->orderByDesc('purchase_orders.created_at')
            ->select('purchase_orders.*')
            ->distinct('purchase_orders.id')
            ->paginate(10);

        return $this->ResponseJson($model);
    }

    /**
     * api for po trading sh
     *
     * @param int|null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function po_sh($id)
    {
        $model = model::findOrFail($id);
        validate_branch($model->branch_id);
        $data = $model->sh_number->sh_number_details;
        return $this->ResponseJsonData($data);
    }

    /**
     * detail
     *
     * @param int|null $id
     * @return arguments
     */
    public function detail(Request $request, $id)
    {
        $model = model::with(['sale_order.sh_number.sh_number_details', 'sh_number.sh_number_details'])
            ->findOrFail($id);

        $jumlah_lpbs = ItemReceivingPoTrading::whereHas('item_receiving_report', function ($query) use ($model) {
            $query->where('reference_id', $model->id)
                ->where('reference_model', PoTrading::class)
                ->whereIn('status', ['approve', 'done', 'pending', 'revert']);
        })
            ->when($request->item_receiving_report_id, function ($query) use ($request) {
                $query->where('item_receiving_report_id', '!=', $request->item_receiving_report_id);
            })
            ->sum('liter_obs');

        validate_branch($model->branch_id);
        $additionals = PurchaseOrderAdditionalItems::where('po_trading_id', $id)->with(['item.unit'])->get();
        $item_receiving_additionals = ItemReceivingPoTradingAdditional::whereHas('item_receiving_report', function ($query) use ($model, $request) {
            $query->whereIn('status', ['approve', 'done', 'pending', 'revert'])
                ->when($request->item_receiving_report_id, function ($query) use ($request) {
                    $query->where('item_receiving_report_id', '!=', $request->item_receiving_report_id);
                });
        })
            ->whereIn('purchase_order_additional_items_id', $additionals->pluck('id'))
            ->get();

        $additionals = $additionals->each(function ($item) use ($item_receiving_additionals) {
            $item->outstanding = $item->jumlah - $item_receiving_additionals->where('purchase_order_additional_items_id', $item->id)->sum('receive_qty');

            return $item;
        });

        $model->customer;
        $model->po_trading_detail->item;
        $model->jumlah_lpbs = $jumlah_lpbs;
        $model->po_trading_details_additional = $additionals;
        $model->unit = $model->po_trading_detail->item->unit->name ?? '';

        return $this->ResponseJsonData($model);
    }

    /**
     * po_coas
     *
     * @param int|null $id
     * @return mixed
     */
    public function po_coas(int|null $id)
    {
        $model = model::findOrFail($id);
        validate_branch($model->branch_id);

        return $this->ResponseJsonData(new PoTradingCoaResource($model));
    }
    /**
     * POT Export
     *
     *
     *
     */
    public function export($id, Request $request)
    {
        if (!$request->preview && authorizePrint('purchase_order_trading')) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                model::class,
                decryptId($id),
                'purchase_order_trading',
            );

            if (!$result) {
                return abort(403);
            }
        }

        $model = model::with('vendor', 'po_trading_detail', 'po_trading_detail.item', 'approve_by')->findOrFail(decryptId($id));
        $file = public_path('/pdf_reports/Report-Purchase-Order-Trading-' . microtime(true) . '.pdf');
        $fileName = 'Report-Purchase-Order-Trading-' . microtime(true) . '.pdf';

        $qr_url = route('purchase-order.export.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $approval = Authorization::where('model', model::class)
            ->with(['details' => function ($q) {
                $q->where('status', 'approve')
                    ->where('note', 'not like', '%otomatis%');
            }])
            ->where('model_id', $model->id)
            ->first();

        $view = 'admin.' . $this->view_folder . '.export';
        if ($request->close) {
            $view = 'admin.' . $this->view_folder . '.export-close';
        }
        $pdf = PDF::loadview($view, compact('model', 'qr', 'approval'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $w = $canvas->get_width();
        $h = $canvas->get_height();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM}/{PAGE_COUNT}", '', 8);

        if ($request->preview) {
            Storage::disk('public')->deleteDirectory('tmp_purchase_order_trading');
            $tmp_file_name = 'purchase_order_trading_' . time() . '.pdf';
            $path = 'tmp_purchase_order_trading/' . $tmp_file_name;
            Storage::disk('public')->put($path, $pdf->output());

            return response()->json($path);
        }

        return $pdf->stream($fileName);
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
            'customer',
            'vendor',
            'sh_number.sh_number_details',
            'currency',
            'po_trading_detail.item.unit',
            'purchase_order_taxes.tax',
            'purchase_order_taxes.tax_trading',
            'purchase_request_trading.purchase_request_trading_details.item',
        ])->findOrFail($id);

        $additional = $model->purchase_order_additionals()->with([
            'item',
            'purchase_order_additional_taxes.tax'
        ])->get();

        return $this->ResponseJsonData(compact('model', 'additional'));
    }

    public function history($id, Request $request)
    {
        try {
            $purchase_orders = DB::table('purchase_orders')
                ->where('id', $id)
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

            $item_receiving_reports = DB::table('item_receiving_reports')
                ->where('reference_model', PoTrading::class)
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

                $item->date = localDate($item->date);
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

    public function select_for_transport(Request $request)
    {
        $model = model::leftJoin('purchase_order_details', 'purchase_order_details.po_trading_id', 'purchase_orders.id')
            ->with(['customer'])
            ->where('purchase_orders.nomor_po', 'like', "%$request->search%")
            ->whereNotIn('purchase_orders.status', ['reject', 'pending', 'void'])
            ->whereColumn('purchase_order_details.jumlah', '>', 'purchase_order_details.jumlah_lpbs')
            ->orderByDesc('purchase_orders.created_at')
            ->select('purchase_orders.*')
            ->distinct('purchase_orders.id')
            ->when($request->filter, function ($q) use ($request) {
                if ($request->filter == "no_sales_order") {
                    return $q->whereNull('purchase_orders.sale_order_id');
                } elseif ($request->filter == "has_sales_order") {
                    return $q->whereNotNull('purchase_orders.sale_order_id');
                }
            })
            ->when($request->sales_order_id, function ($q) use ($request) {
                return $q->where('purchase_orders.sale_order_id', $request->sales_order_id);
            })
            ->paginate(10);

        return $this->ResponseJson($model);
    }
}
