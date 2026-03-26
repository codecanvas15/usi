<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Http\Helpers\PrintHelper;
use App\Models\AccountPayable;
use App\Models\Authorization;
use App\Models\Branch;
use App\Models\Project;
use App\Models\PurchaseOrderService;
use App\Models\PurchaseOrderServiceDetailItem;
use App\Models\PurchaseOrderServiceDetailItemTax;
use App\Models\PurchaseRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PurchaseOrderServiceController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view purchase-service", ['only' => ['index', 'show', 'data']]);
        $this->middleware("permission:create purchase-service", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit purchase-service", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete purchase-service", ['only' => ['destroy']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'purchase-order-service';

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $checkAuthorizePrint = authorizePrint('purchase_order_service');
            $data = \App\Models\PurchaseOrderService::select('purchase_order_services.*')
                ->join('purchases', function ($query) {
                    $query->on('purchases.model_id', 'purchase_order_services.id')
                        ->where('model_reference', \App\Models\PurchaseOrderService::class);
                })
                ->leftJoin('cash_advance_payments', function ($query) {
                    $query->on('cash_advance_payments.purchase_id', 'purchases.id')
                        ->whereNull('cash_advance_payments.deleted_at')
                        ->where('cash_advance_payments.status', 'approve');
                })
                ->with(['vendor'])
                ->when($request->branch_id && get_current_branch()->is_primary, function ($query) use ($request) {
                    $query->where('purchase_order_services.branch_id', $request->branch_id);
                })
                ->when(!get_current_branch()->is_primary, function ($query) {
                    $query->where('purchase_order_services.branch_id', get_current_branch()->id);
                })
                ->when($request->status, function ($query) use ($request) {
                    $query->where('purchase_order_services.status', $request->status);
                })
                ->when($request->from_date, function ($query) use ($request) {
                    $query->whereDate('purchase_order_services.date', '>=', Carbon::parse($request->from_date));
                })
                ->when($request->to_date, function ($query) use ($request) {
                    $query->whereDate('purchase_order_services.date', '<=', Carbon::parse($request->to_date));
                })
                ->when($request->vendor_id, function ($query) use ($request) {
                    $query->where('purchase_order_services.vendor_id', $request->vendor_id);
                })
                ->select('purchase_order_services.*', 'cash_advance_payments.id as cash_advance_payment_id')
                ->groupBy('purchase_order_services.id');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', fn($row) => \Carbon\Carbon::parse($row->date)->format('d-m-Y'))
                ->editColumn('code', function ($row) use ($checkAuthorizePrint) {
                    $link = route('purchase-order-service.export-pdf', ['id' => encryptId($row->id)]);
                    $button = view('components.datatable.detail-link', [
                        'field' => $row->is_spk ? $row->spk_number : $row->code,
                        'row' => $row,
                        'main' => $this->view_folder,
                        'permission_name' => 'purchase-service',
                    ]) . '<br>';
                    // view("components.datatable.export-button", [
                    //     'route' => route('purchase-order-service.export-pdf', ['id' => encryptId($row->id)]),
                    //     'onclick' => "show_print_out_modal(event)",
                    // ]


                    if ($row->is_spk) {
                        $link = route('purchase-order-service.export-pdf', ['id' => encryptId($row->id), 'type' => 'spk']);
                        $button .= view('components.button-auth-print', [
                            'type' => 'purchase_order_service_spk',
                            'href' => $link,
                            'model' => PurchaseOrderService::class,
                            'did' => $row->id,
                            'code' => $row->spk_number,
                            'condition' => false,
                            'symbol' => '&',
                            'label' => 'SPK',
                            'size' => 'sm',
                        ])->render();
                    } else {
                        $button .=  view('components.button-auth-print', [
                            'type' => 'purchase_order_service',
                            'href' => $link,
                            'model' => PurchaseOrderService::class,
                            'did' => $row->id,
                            'code' => $row->code,
                            'condition' => $checkAuthorizePrint,
                            'size' => 'sm',
                        ])->render();
                    }

                    if ($row->status == 'close') {
                        $link_closed = route('purchase-order-service.export-pdf', ['id' => encryptId($row->id)]) . '?type=closed';
                        $button .= '<br>' . view('components.button-auth-print', [
                            'type' => 'purchase_order_service_closed',
                            'model' => PurchaseOrderService::class,
                            'did' => $row->id,
                            'href' => $link_closed,
                            'label' => 'Print Close PO',
                            'icon' => 'edit',
                            'color' => 'primary',
                            'symbol' => '&',
                            'size' => 'sm',
                        ]);
                    }

                    return $button;
                })
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . purchase_service_status()[$row->status]['color'] . '">
                                            ' . purchase_service_status()[$row->status]['label'] . ' - ' . purchase_service_status()[$row->status]['text'] . '
                                        </div>';

                    if (!in_array($row->status, ['done']) && $row->cash_advance_payment_id) {
                        $badge .= '<br><div class="badge badge-pill badge-danger  mt-1 animate__animated animate__pulse animate__infinite infinite">Uang Muka Telah dibayar!</div>';
                    }
                    return $badge;
                })
                ->addColumn('action', function ($row) {
                    return view('admin.purchase-order-service.data-table.btn', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'permission_name' => 'purchase-service',
                        'btn_config' => [
                            'detail' => [
                                'display' => false,
                            ],
                            'edit' => [
                                'display' => $row->check_available_date ? in_array($row->status, ['pending', 'revert']) : false,
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
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(PurchaseOrderService::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

        return view("admin.$this->view_folder.create");
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = json_decode($request->values);

        DB::beginTransaction();

        try {
            // Validate main quantity is not nan and 0
            if (is_array($request->main_quantity)) {
                foreach ($request->main_quantity as $quantity) {
                    if ($quantity == 0) {
                        throw new \Exception('Jumlah tidak boleh sama dengan 0');
                    } elseif ($quantity == "NaN") {
                        throw new \Exception('Jumlah tidak boleh string');
                    }
                }
            }

            // ? CALCULATION VARIABLES
            $total = 0;
            $total_main = 0;
            $total_additional = 0;
            $total_tax_main = 0;
            $total_tax_additional = 0;

            // ? CREATE PURCHASES
            $purchase = new \App\Models\Purchase();
            $purchase->fill([
                'kode',
                'tanggal' => Carbon::parse($data->date),
                'tipe' => 'jasa',
                'model_reference' => \App\Models\PurchaseOrderService::class,
                'status' => 'pending',
                'branch_id' => $data->branch_id ?? get_current_branch()->id,
                'vendor_id' => $data->vendor_id,
            ]);

            // Check available date closing
            if (!$purchase->check_available_date) {
                throw new \Exception('Tanggal yang anda masukkan sudah closing');
            }

            $purchase->save();

            // ? CREATE PARENT DATA
            $branch = Branch::find($data->branch_id ?? get_current_branch()->id);
            $model = new \App\Models\PurchaseOrderService();
            $model->fill([
                'purchase_id' => $purchase->id,
                'branch_id' => $data->branch_id ?? get_current_branch()->id,
                'vendor_id' => $data->vendor_id,
                'currency_id' => $data->currency_id,
                'created_by',
                'approved_by',
                'code',
                'date' => Carbon::parse($data->date),
                'quotation' => $request->hasFile('quotation') ? $this->upload_file($request->file('quotation'), 'purchase-order-service') : null,
                'term_of_payment' => $data->term_of_payment,
                'term_of_payment_days' => $data->term_of_payment_days,
                'payment_description' => $request->payment_description,
                'exchange_rate' => thousand_to_float($data->exchange_rate),
                'is_spk' => $request->is_spk ?? 0,
                'is_include_tax' => $request->is_include_tax ?? 0,
                'spk_number' => $request->is_spk ? generate_code(\App\Models\PurchaseOrderService::class, 'spk_number', 'date', "SPK", branch_sort: $branch->sort ?? null, date: Carbon::parse($data->date)) : null,
                'pic' => $request->pic ?? null,
            ]);

            $model->save();

            $purchase->update([
                'model_id' => $model->id,
                'currency_id' => $model->currency_id,
            ]);

            // ? CREATE FROM PURCHASE REQUEST
            foreach ($data->main as $key => $value) {
                if (!is_null($value)) {
                    $single_purchase_request_sub_total = 0;
                    $single_purchase_request_sub_total_after_tax = 0;
                    $single_purchase_request_amount_discount = 0;
                    $single_purchase_request_tax_total = 0;
                    $single_purchase_request_total = 0;

                    // $model_purchase_request = \App\Models\PurchaseRequest::findOrFail($value->purchase_request_id);
                    // $purchase_request_done_count = $model_purchase_request->purchase_request_details->whereNotIn('status', ['reject', 'pending'])->where('status', 'done')->count();
                    // $model_purchase_request_count_done = $model_purchase_request->purchase_request_details->whereNotIn('status', ['reject', 'pending'])->count();

                    $purchase_request = \App\Models\PurchaseRequest::find($value->purchase_request_id);
                    $datePurchaseRequest = \Carbon\Carbon::parse($purchase_request->tanggal);
                    $datePurchaseOrder = \Carbon\Carbon::parse($data->date);

                    if ($datePurchaseRequest->gt($datePurchaseOrder)) {
                        throw new \Exception("Tanggal PO tidak boleh lebih kecil dari tanggal PR");
                    }

                    $model_child = new \App\Models\PurchaseOrderServiceDetail();
                    $model_child->fill([
                        'purchase_order_service_id' => $model->id,
                        'purchase_request_id' => $value->purchase_request_id,
                        'type' => 'main',
                    ]);
                    $model_child->save();

                    foreach ($value->purchase_request_detail_id as $key2 => $value2) {
                        if (!is_null($value2)) {
                            $single_purchase_request_detail_sub_total = $value2->quantity * $value2->price;
                            $single_purchase_request_detail_sub_total_after_tax = $single_purchase_request_detail_sub_total;
                            $single_purchase_request_detail_amount_discount = 0;
                            $single_purchase_request_detail_tax_total = 0;
                            $single_purchase_request_detail_total = $single_purchase_request_detail_sub_total;

                            $item = \App\Models\Item::find($value2->item_id);

                            $model_purchase_request_detail_item = \App\Models\PurchaseRequestDetail::findOrFail($value2->purchase_request_detail_id);
                            $purchase_detail_model = \App\Models\PurchaseOrderServiceDetailItem::whereNotIn('status', ['reject', 'done', 'void'])
                                ->whereHas('purchase_order_service_detail', function ($q) {
                                    $q->whereHas('purchase_order_service', function ($q) {
                                        $q->whereNull('deleted_at');
                                    });
                                })
                                ->where('purchase_request_detail_id', $model_purchase_request_detail_item->id)
                                ->get()
                                ->sum('quantity');

                            if ($purchase_detail_model + $value2->quantity > $model_purchase_request_detail_item->jumlah_diapprove) {
                                throw new \Exception("Jumlah PO melebihi jumlah PR");
                            }

                            $model_child_item = new \App\Models\PurchaseOrderServiceDetailItem();
                            $model_child_item->fill([
                                'purchase_order_service_detail_id' => $model_child->id,
                                'purchase_request_detail_id' => $value2->purchase_request_detail_id,
                                'item_id' => $item->id,
                                'unit_id' => $item->unit_id,
                                'quantity' => $value2->quantity,
                                'price_before_discount' => (float) $value2->price_before_discount,
                                'discount' => (float) $value2->discount,
                                'price' => (float) $value2->price,
                                'sub_total' => (float) $single_purchase_request_detail_sub_total,
                                'sub_total_after_tax' => (float) $single_purchase_request_detail_sub_total_after_tax,
                                'amount_discount' => (float) $single_purchase_request_detail_amount_discount,
                                'tax_total' => (float) $single_purchase_request_detail_tax_total,
                                'total' => (float) $single_purchase_request_detail_total,
                            ]);
                            $model_child_item->save();

                            if (($model_child_item->price_before_discount != $model_child_item->price) && $model_child_item->discount == 0 && !$request->is_include_tax) {
                                Log::info('Error Request: ' . json_encode($request->all()));
                                throw new \Exception("Invalid Data Price Before Discount on Create");
                            }

                            foreach ($value2->tax_id as $key3 => $value3) {
                                $tax = \App\Models\Tax::find($value3);
                                $single_purchase_request_detail_item_tax_total = $single_purchase_request_detail_sub_total * $tax->value;

                                $single_purchase_request_detail_sub_total_after_tax += $single_purchase_request_detail_item_tax_total;
                                $single_purchase_request_detail_tax_total += $single_purchase_request_detail_item_tax_total;
                                $single_purchase_request_detail_total += $single_purchase_request_detail_item_tax_total;

                                $model_child_item_tax = new \App\Models\PurchaseOrderServiceDetailItemTax();
                                $model_child_item_tax->fill([
                                    'purchase_order_service_detail_item_id' => $model_child_item->id,
                                    'tax_id' => $tax->id,
                                    'value' => $tax->value,
                                    'total' => $single_purchase_request_detail_item_tax_total,
                                ]);
                                $model_child_item_tax->save();
                            }

                            $model_child_item->update([
                                'sub_total' => $single_purchase_request_detail_sub_total,
                                'sub_total_after_tax' => $single_purchase_request_detail_sub_total_after_tax,
                                'amount_discount' => $single_purchase_request_detail_amount_discount,
                                'tax_total' => $single_purchase_request_detail_tax_total,
                                'total' => $single_purchase_request_detail_total,
                            ]);

                            $single_purchase_request_sub_total += $single_purchase_request_detail_sub_total;
                            $single_purchase_request_sub_total_after_tax += $single_purchase_request_detail_sub_total_after_tax;
                            $single_purchase_request_amount_discount += $single_purchase_request_detail_amount_discount;
                            $single_purchase_request_tax_total += $single_purchase_request_detail_tax_total;
                            $single_purchase_request_total += $single_purchase_request_detail_total;
                        }
                    }

                    $model_child->update([
                        'sub_total' => $single_purchase_request_sub_total,
                        'sub_total_after_tax' => $single_purchase_request_sub_total_after_tax,
                        'amount_discount' => $single_purchase_request_amount_discount,
                        'tax_total' => $single_purchase_request_tax_total,
                        'total' => $single_purchase_request_total,
                    ]);

                    // if ($model_purchase_request_count_done == $purchase_request_done_count) {
                    //     $model_purchase_request->update(['status' => 'done']);
                    // } elseif ($model_purchase_request_count_done > $purchase_request_done_count && $model_purchase_request->status != 'partial') {
                    //     $model_purchase_request->update(['status' => 'partial']);
                    // }

                    $total += $single_purchase_request_total;
                    $total_main += $single_purchase_request_total;
                    $total_tax_main += $single_purchase_request_tax_total;
                }
            }

            // ? CREATE ADDITIONAL ITEM
            if (is_array($data->additional) && count($data->additional) > 0) {
                if ($data->additional[0] != null) {
                    $additional_sub_total = 0;
                    $additional_sub_total_after_tax = 0;
                    $additional_amount_discount = 0;
                    $additional_tax_total = 0;
                    $additional_total = 0;

                    $model_additional = new \App\Models\PurchaseOrderServiceDetail();
                    $model_additional->fill([
                        'purchase_order_service_id' => $model->id,
                        'purchase_request_id',
                        'type' => 'additional',
                        'sub_total' => $additional_sub_total,
                        'sub_total_after_tax' => $additional_sub_total_after_tax,
                        'amount_discount' => $additional_amount_discount,
                        'tax_total' => $additional_tax_total,
                        'total' => $additional_total,
                    ]);
                    $model_additional->save();

                    foreach ($data->additional as $key => $value) {
                        if (!is_null($value)) {
                            $item = \App\Models\Item::find($value->item_id);

                            $additional_item_sub_total = $value->quantity * $value->price;
                            $additional_item_sub_total_after_tax = $additional_item_sub_total;
                            $additional_item_amount_discount = 0;
                            $additional_item_tax_total = 0;
                            $additional_item_total = $additional_item_sub_total;

                            $model_additional_item = new \App\Models\PurchaseOrderServiceDetailItem();
                            $model_additional_item->fill([
                                'purchase_order_service_detail_id' => $model_additional->id,
                                'item_id' => $item->id,
                                'unit_id' => $item->unit_id,
                                'quantity' => $value->quantity,
                                'price_before_discount' => (float) $value->price,
                                'price' => (float) $value->price,
                                'sub_total' => $additional_item_sub_total,
                                'sub_total_after_tax' => $additional_item_sub_total_after_tax,
                                'amount_discount' => $additional_item_amount_discount,
                                'tax_total' => $additional_item_tax_total,
                                'total' => $additional_item_total,
                            ]);
                            $model_additional_item->save();

                            foreach ($value->tax_id as $key2 => $value2) {
                                $tax = \App\Models\Tax::find($value2);
                                $single_item_tax_total = $additional_item_sub_total_after_tax * $tax->value;

                                $additional_item_tax_total += $single_item_tax_total;
                                $additional_item_total += $single_item_tax_total;

                                $model_additional_item_tax = new \App\Models\PurchaseOrderServiceDetailItemTax();
                                $model_additional_item_tax->fill([
                                    'purchase_order_service_detail_item_id' => $model_additional_item->id,
                                    'tax_id' => $tax->id,
                                    'value' => $tax->value,
                                    'total' => $single_item_tax_total,
                                ]);
                                $model_additional_item_tax->save();
                            }

                            $model_additional_item->update([
                                'sub_total' => $additional_item_sub_total,
                                'sub_total_after_tax' => $additional_item_sub_total_after_tax,
                                'amount_discount' => $additional_item_amount_discount,
                                'tax_total' => $additional_item_tax_total,
                                'total' => $additional_item_total,
                            ]);

                            $additional_sub_total += $additional_item_sub_total;
                            $additional_sub_total_after_tax += $additional_item_sub_total_after_tax;
                            $additional_amount_discount += $additional_item_amount_discount;
                            $additional_tax_total += $additional_item_tax_total;
                            $additional_total += $additional_item_total;
                        }
                    }

                    $model_additional->update([
                        'sub_total' => $additional_sub_total,
                        'sub_total_after_tax' => $additional_sub_total_after_tax,
                        'amount_discount' => $additional_amount_discount,
                        'tax_total' => $additional_tax_total,
                        'total' => $additional_total,
                    ]);

                    $total += $additional_total;
                    $total_additional += $additional_total;
                    $total_tax_additional += $additional_tax_total;
                }
            }

            $model->update([
                'total' => $total,
                'total_main' => $total_main,
                'total_additional' => $total_additional,
                'total_tax_main' => $total_tax_main,
                'total_tax_additional' => $total_tax_additional,
            ]);

            $purchase->fill([
                'kode' => $model->code,
                'tanggal' => Carbon::parse($model->date),
                'tipe' => 'jasa',
                'model_reference' => \App\Models\PurchaseOrderService::class,
                'status' => $model->status,
                'branch_id' => $model->branch_id,
                'vendor_id' => $model->vendor_id,
            ]);
            $purchase->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: PurchaseOrderService::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "PO Service",
                subtitle: Auth::user()->name . " mengajukan PO Service " . $model->code,
                link: route('admin.purchase-order-service.show', $model),
                update_status_link: route('admin.purchase-order-service.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );

            DB::commit();

            $model->purchaseOrderServiceDetails()
                ->each(function ($query) {
                    app('App\Http\Controllers\Admin\PurchaseRequestController')->check_purchase_request_status($query->purchase_request_id);
                });

            if ($request->ajax()) {
                return $this->ResponseJsonMessage('Success create purchase order service', 200);
            }


            return redirect()->route("admin.purchase.index")->with($this->ResponseMessageCRUD(true, "create"));
        } catch (\Throwable $th) {
            DB::rollback();

            if ($request->ajax()) {
                return $this->ResponseJsonMessage($th->getMessage(), 500);
            }
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", null, $th->getMessage()))->withInput();
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
        $model = \App\Models\PurchaseOrderService::with([
            'branch',
            'vendor',
            'currency',
            'create',
            'approve',
            'purchaseOrderServiceDetails.purchase_request.division',
            'purchaseOrderServiceDetails.purchase_order_service_detail_items.item',
            'purchaseOrderServiceDetails.purchase_order_service_detail_items.unit',
            'purchaseOrderServiceDetails.purchase_order_service_detail_items.purchase_order_service_detail_item_taxes.tax',
        ])
            ->findOrFail($id);

        $project_id = $model->purchaseOrderServiceDetails->map(function ($purchase_order_service_detail) {
            return $purchase_order_service_detail->purchase_request->project_id ?? null;
        })->unique()->toArray();

        $projects = Project::whereIn('id', $project_id)->get();


        validate_branch($model->branch_id);

        $parent_status_logs = $model->logs_data['status_logs'] ?? [];
        $parent_activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: PurchaseOrderService::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );
        $authorization_logs['can_revert'] = in_array($model->status, ['approve', 'partial-approve', 'partial-rejected']) && $model->check_available_date;
        $authorization_logs['can_void'] = in_array($model->status, ['approve', 'partial-approve', 'partial-rejected']) && $model->check_available_date;

        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $is_has_down_payment = $model->purchase->purchase_down_payments()->whereNotIn('status', ['void', 'reject'])->count() > 0;

        $authorization_logs['can_revert_request'] = in_array($model->status, ['approve', 'partial-approve', 'partial-rejected']) && $model->check_available_date && !$is_has_down_payment;
        $authorization_logs['can_void_request'] = in_array($model->status, ['approve', 'partial-approve', 'partial-rejected']) && $model->check_available_date && !$is_has_down_payment;
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.$this->view_folder.show", compact('model', 'parent_status_logs', 'parent_activity_logs', 'authorization_log_view', 'auth_revert_void_button', 'projects'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = \App\Models\PurchaseOrderService::findOrFail($id);
        $is_has_down_payment = $model->purchase->purchase_down_payments()->whereNotIn('status', ['void', 'reject'])->count() > 0;
        if ($is_has_down_payment) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'PO telah memiliki uang muka'));
        }

        validate_branch($model->branch_id);

        // Check available date closing
        if (!$model->check_available_date) {
            return abort(403);
        }

        if (!in_array($model->status, ['pending', 'revert'])) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "edit", null, "Data tidak dapat diubah karena status sudah bukan pending"));
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
        $data = json_decode($request->values);

        // Validate main quantity is not nan and 0
        if (is_array($request->main_quantity)) {
            foreach ($request->main_quantity as $quantity) {
                if ($quantity == 0) {
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", null, 'Jumlah tidak boleh sama dengan 0'));
                } elseif ($quantity == "NaN") {
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", null, 'Jumlah tidak boleh string'));
                }
            }
        }

        DB::beginTransaction();


        $purchase_request_ids = [];
        try {
            // ? CALCULATION VARIABLES
            $total = 0;
            $total_main = 0;
            $total_additional = 0;
            $total_tax_main = 0;
            $total_tax_additional = 0;

            // * PARENT DATA
            $model = \App\Models\PurchaseOrderService::findOrFail($id);

            $purchase_request_ids = $model->purchaseOrderServiceDetails->map(function ($purchase_order_service_detail) {
                return $purchase_order_service_detail->purchase_request_id;
            })->unique()->toArray();

            if ($request->is_spk) {
                if (!$model->spk_number) {
                    $spk_number = generate_code(\App\Models\PurchaseOrderService::class, 'spk_number', 'date', "SPK", branch_sort: $model->branch->sort ?? null, date: Carbon::parse($data->date));
                } else {
                    $spk_number = $model->spk_number;
                }
            } else {
                $spk_number = null;
            }

            $model->fill([
                'vendor_id' => $data->vendor_id,
                'currency_id' => $data->currency_id,
                'quotation' => $request->hasFile('quotation') ? $this->upload_file($request->file('quotation'), 'purchase-order-service') : $model->quotation,
                'term_of_payment' => $data->term_of_payment,
                'term_of_payment_days' => $data->term_of_payment_days,
                'payment_description' => $request->payment_description,
                'exchange_rate' => thousand_to_float($data->exchange_rate),
                'is_spk' => $request->is_spk ?? 0,
                'is_include_tax' => $request->is_include_tax ?? 0,
                'spk_number' => $spk_number,
                'pic' => $request->pic ?? null,
            ]);

            if ($model->status == 'revert') {
                $model->code  = generate_code_update($model->code);
            }

            $model->purchase->update([
                'tanggal' => Carbon::parse($data->date),
                'vendor_id' => $data->vendor_id,
                'currency_id' => $data->currency_id,
                'kode' => $model->code,
            ]);

            // Check available date closing
            if (!$model->check_available_date) {
                return abort(403);
            }

            $model->save();

            // * FROM PURCHASE REQUEST
            foreach ($data->main as $key => $value) {
                $single_purchase_request_sub_total = 0;
                $single_purchase_request_sub_total_after_tax = 0;
                $single_purchase_request_amount_discount = 0;
                $single_purchase_request_tax_total = 0;
                $single_purchase_request_total = 0;

                // $model_purchase_request = \App\Models\PurchaseRequest::findOrFail($value->purchase_request_id);
                // $purchase_request_done_count = $model_purchase_request->purchase_request_details->whereNotIn('status', ['reject', 'pending'])->where('status', 'done')->count();
                // $model_purchase_request_count_done = $model_purchase_request->purchase_request_details->whereNotIn('status', ['reject', 'pending'])->count();

                $model_child = \App\Models\PurchaseOrderServiceDetail::findOrFail($value->purchase_order_service_detail_id);

                $exist_purchase_order_service_detail_items = collect($value->purchase_order_service_detail_items)->map(function ($item) {
                    return $item->purchase_order_service_detail_item_id;
                })->toArray();

                if (count($exist_purchase_order_service_detail_items) > 0) {
                    $delete_items = \App\Models\PurchaseOrderServiceDetailItem::whereNotIn('id', $exist_purchase_order_service_detail_items)
                        ->where('purchase_order_service_detail_id', $value->purchase_order_service_detail_id)
                        ->get()
                        ->each(function ($item) {
                            $item->purchase_order_service_detail_item_taxes()->delete();
                            $item->delete();
                        });
                }

                foreach ($value->purchase_order_service_detail_items as $key2 => $value2) {
                    $single_purchase_request_detail_sub_total = $value2->quantity * $value2->price;
                    $single_purchase_request_detail_sub_total_after_tax = $single_purchase_request_detail_sub_total;
                    $single_purchase_request_detail_amount_discount = 0;
                    $single_purchase_request_detail_tax_total = 0;
                    $single_purchase_request_detail_total = $single_purchase_request_detail_sub_total;

                    $item = \App\Models\Item::find($value2->item_id);
                    $model_child_item = \App\Models\PurchaseOrderServiceDetailItem::findOrFail($value2->purchase_order_service_detail_item_id);

                    $model_purchase_request_detail_item = \App\Models\PurchaseRequestDetail::findOrFail($value2->purchase_request_detail_id);
                    $purchase_detail_model = \App\Models\PurchaseOrderServiceDetailItem::whereNotIn('status', ['reject', 'done', 'void'])
                        ->whereHas('purchase_order_service_detail', function ($q) {
                            $q->whereHas('purchase_order_service', function ($q) {
                                $q->whereNull('deleted_at');
                            });
                        })
                        ->where('purchase_request_detail_id', $model_purchase_request_detail_item->id)
                        ->whereHas('purchase_order_service_detail', function ($query) {
                            $query->whereHas('purchase_order_service', function ($query) {
                                $query->whereNull('deleted_at');
                            });
                        })
                        ->get()
                        ->sum('quantity');

                    if ($purchase_detail_model - $model_child_item->quantity + $value2->quantity > $model_purchase_request_detail_item->jumlah_diapprove) {
                        throw new \Exception("Jumlah PO melebihi jumlah PR");
                    }

                    $model_child_item->fill([
                        'item_id' => $item->id,
                        'unit_id' => $item->unit_id,
                        'quantity' => $value2->quantity,
                        'price_before_discount' => (float) $value2->price_before_discount,
                        'discount' => (float) $value2->discount,
                        'price' => (float) $value2->price,
                        'sub_total' => $single_purchase_request_detail_sub_total,
                        'sub_total_after_tax' => $single_purchase_request_detail_sub_total_after_tax,
                        'amount_discount' => $single_purchase_request_detail_amount_discount,
                        'tax_total' => $single_purchase_request_detail_tax_total,
                        'total' => $single_purchase_request_detail_total,
                    ]);

                    $model_child_item->save();

                    \App\Models\PurchaseOrderServiceDetailItemTax::where('purchase_order_service_detail_item_id', $model_child_item->id)->delete();
                    foreach ($value2->tax_id as $key3 => $value3) {
                        $tax = \App\Models\Tax::find($value3);
                        $single_purchase_request_detail_item_tax_total = $single_purchase_request_detail_sub_total * $tax->value;

                        $single_purchase_request_detail_sub_total_after_tax += $single_purchase_request_detail_item_tax_total;
                        $single_purchase_request_detail_tax_total += $single_purchase_request_detail_item_tax_total;
                        $single_purchase_request_detail_total += $single_purchase_request_detail_item_tax_total;

                        $model_child_item_tax = new \App\Models\PurchaseOrderServiceDetailItemTax();
                        $model_child_item_tax->fill([
                            'purchase_order_service_detail_item_id' => $model_child_item->id,
                            'tax_id' => $tax->id,
                            'value' => $tax->value,
                            'total' => $single_purchase_request_detail_item_tax_total,
                        ]);
                        $model_child_item_tax->save();
                    }

                    $model_child_item->update([
                        'sub_total' => $single_purchase_request_detail_sub_total,
                        'sub_total_after_tax' => $single_purchase_request_detail_sub_total_after_tax,
                        'amount_discount' => $single_purchase_request_detail_amount_discount,
                        'tax_total' => $single_purchase_request_detail_tax_total,
                        'total' => $single_purchase_request_detail_total,
                    ]);

                    $single_purchase_request_sub_total += $single_purchase_request_detail_sub_total;
                    $single_purchase_request_sub_total_after_tax += $single_purchase_request_detail_sub_total_after_tax;
                    $single_purchase_request_amount_discount += $single_purchase_request_detail_amount_discount;
                    $single_purchase_request_tax_total += $single_purchase_request_detail_tax_total;
                    $single_purchase_request_total += $single_purchase_request_detail_total;
                }

                $model_child->update([
                    'sub_total' => $single_purchase_request_sub_total,
                    'sub_total_after_tax' => $single_purchase_request_sub_total_after_tax,
                    'amount_discount' => $single_purchase_request_amount_discount,
                    'tax_total' => $single_purchase_request_tax_total,
                    'total' => $single_purchase_request_total,
                ]);

                // if ($model_purchase_request_count_done == $purchase_request_done_count) {
                //     $model_purchase_request->update(['status' => 'done']);
                // } elseif ($model_purchase_request_count_done > $purchase_request_done_count && $model_purchase_request->status != 'partial') {
                //     $model_purchase_request->update(['status' => 'partial']);
                // }

                $total += $single_purchase_request_total;
                $total_main += $single_purchase_request_total;
                $total_tax_main += $single_purchase_request_tax_total;
            }

            // * ADDITIONAL ITEM
            if ($data->additional) {
                if ($data->additional->purchase_order_service_detail_items[0] && $data->additional->purchase_order_service_detail_items[0]->quantity != 0 && $data->additional->purchase_order_service_detail_items[0]->price != 0) {
                    $model_additional = \App\Models\PurchaseOrderServiceDetail::where('purchase_order_service_id', $model->id)
                        ->where('type', 'additional')
                        ->where('id', $data->additional?->purchase_order_service_detail_id ?? null)
                        ->first();

                    if (!$model_additional) {
                        $model_additional = new \App\Models\PurchaseOrderServiceDetail();
                    } else {
                        $items = \App\Models\PurchaseOrderServiceDetailItem::where('purchase_order_service_detail_id', $model_additional->id)->get();
                        foreach ($items as $key => $value) {
                            \App\Models\PurchaseOrderServiceDetailItemTax::where('purchase_order_service_detail_item_id', $value->id)->delete();
                            $value->delete();
                        }
                    }

                    if (count($data->additional?->purchase_order_service_detail_items) > 0 and $data->additional?->purchase_order_service_detail_items[0] != null) {
                        $additional_sub_total = 0;
                        $additional_sub_total_after_tax = 0;
                        $additional_amount_discount = 0;
                        $additional_tax_total = 0;
                        $additional_total = 0;

                        $model_additional->fill([
                            'purchase_order_service_id' => $model->id,
                            'type' => 'additional',
                            'sub_total' => $additional_sub_total,
                            'sub_total_after_tax' => $additional_sub_total_after_tax,
                            'amount_discount' => $additional_amount_discount,
                            'tax_total' => $additional_tax_total,
                            'total' => $additional_total,
                        ]);
                        $model_additional->save();

                        foreach ($data->additional->purchase_order_service_detail_items as $key => $value) {
                            if (!is_null($value)) {
                                if ($value->quantity != 0 && $value->price != 0) {
                                    $item = \App\Models\Item::find($value->item_id);

                                    $additional_item_sub_total = $value->quantity * $value->price;
                                    $additional_item_sub_total_after_tax = $additional_item_sub_total;
                                    $additional_item_amount_discount = 0;
                                    $additional_item_tax_total = 0;
                                    $additional_item_total = $additional_item_sub_total;

                                    $model_additional_item = new \App\Models\PurchaseOrderServiceDetailItem();
                                    $model_additional_item->fill([
                                        'purchase_order_service_detail_id' => $model_additional->id,
                                        'item_id' => $item->id,
                                        'unit_id' => $item->unit_id,
                                        'quantity' => $value->quantity,
                                        'price_before_discount' => (float) $value->price,
                                        'price' => (float) $value->price,
                                        'sub_total' => $additional_item_sub_total,
                                        'sub_total_after_tax' => $additional_item_sub_total_after_tax,
                                        'amount_discount' => $additional_item_amount_discount,
                                        'tax_total' => $additional_item_tax_total,
                                        'total' => $additional_item_total,
                                    ]);
                                    $model_additional_item->save();

                                    foreach ($value->tax_id as $key2 => $value2) {
                                        $tax = \App\Models\Tax::find($value2);
                                        $single_item_tax_total = $additional_item_sub_total_after_tax * $tax->value;

                                        $additional_item_tax_total += $single_item_tax_total;
                                        $additional_item_total += $single_item_tax_total;

                                        $model_additional_item_tax = new \App\Models\PurchaseOrderServiceDetailItemTax();
                                        $model_additional_item_tax->fill([
                                            'purchase_order_service_detail_item_id' => $model_additional_item->id,
                                            'tax_id' => $tax->id,
                                            'value' => $tax->value,
                                            'total' => $single_item_tax_total,
                                        ]);
                                        $model_additional_item_tax->save();
                                    }

                                    $model_additional_item->update([
                                        'sub_total' => $additional_item_sub_total,
                                        'sub_total_after_tax' => $additional_item_sub_total_after_tax,
                                        'amount_discount' => $additional_item_amount_discount,
                                        'tax_total' => $additional_item_tax_total,
                                        'total' => $additional_item_total,
                                    ]);

                                    $additional_sub_total += $additional_item_sub_total;
                                    $additional_sub_total_after_tax += $additional_item_sub_total_after_tax;
                                    $additional_amount_discount += $additional_item_amount_discount;
                                    $additional_tax_total += $additional_item_tax_total;
                                    $additional_total += $additional_item_total;
                                }
                            }
                        }

                        $model_additional->update([
                            'sub_total' => $additional_sub_total,
                            'sub_total_after_tax' => $additional_sub_total_after_tax,
                            'amount_discount' => $additional_amount_discount,
                            'tax_total' => $additional_tax_total,
                            'total' => $additional_total,
                        ]);

                        $total += $additional_total;
                        $total_additional += $additional_total;
                        $total_tax_additional += $additional_tax_total;
                    }
                } else {
                    $model_additional = \App\Models\PurchaseOrderServiceDetail::where('purchase_order_service_id', $model->id)
                        ->where('type', 'additional')
                        ->where('id', $data->additional?->purchase_order_service_detail_id ?? null)
                        ->first();

                    if ($model_additional) {
                        $items = \App\Models\PurchaseOrderServiceDetailItem::where('purchase_order_service_detail_id', $model_additional->id)->get();
                        foreach ($items as $key => $value) {
                            \App\Models\PurchaseOrderServiceDetailItemTax::where('purchase_order_service_detail_item_id', $value->id)->delete();
                            $value->delete();
                        }
                        $model_additional->delete();
                    }
                }
            } else {
                $model_additional = \App\Models\PurchaseOrderServiceDetail::where('purchase_order_service_id', $model->id)
                    ->where('type', 'additional')
                    ->where('id', $data->additional?->purchase_order_service_detail_id ?? null)
                    ->first();

                if ($model_additional) {
                    $items = \App\Models\PurchaseOrderServiceDetailItem::where('purchase_order_service_detail_id', $model_additional->id)->get();
                    foreach ($items as $key => $value) {
                        \App\Models\PurchaseOrderServiceDetailItemTax::where('purchase_order_service_detail_item_id', $value->id)->delete();
                        $value->delete();
                    }
                    $model_additional->delete();
                }
            }

            // ? update parent total
            $model->update([
                'kode' => $model->code,
                'vendor_id' => $data->vendor_id,
                'total' => $total,
                'total_main' => $total_main,
                'total_additional' => $total_additional,
                'total_tax_main' => $total_tax_main,
                'total_tax_additional' => $total_tax_additional,
            ]);

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: PurchaseOrderService::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "PO Service",
                subtitle: Auth::user()->name . " mengajukan PO Service " . $model->code,
                link: route('admin.purchase-order-service.show', $model),
                update_status_link: route('admin.purchase-order-service.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );

            DB::commit();

            $purchase_request_ids = array_merge($purchase_request_ids, $model->purchaseOrderServiceDetails->map(function ($purchase_order_service_detail) {
                return $purchase_order_service_detail->purchase_request_id;
            })->toArray());

            foreach (array_unique($purchase_request_ids) as $purchase_request_id) {
                app('App\Http\Controllers\Admin\PurchaseRequestController')->check_purchase_request_status($purchase_request_id);
            }

            if ($request->ajax()) {
                return $this->ResponseJsonMessage('Success create purchase order service', 200);
            }

            return redirect()->route("admin.purchase-order-service.show", $model)->with($this->ResponseMessageCRUD(true, "edit"));
        } catch (\Throwable $th) {
            DB::rollback();

            if ($request->ajax()) {
                return $this->ResponseJsonMessage($th->getMessage(), 500);
            }
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "edit", null, $th->getMessage()))->withInput();
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
        $model = \App\Models\PurchaseOrderService::findOrFail($id);
        $is_has_down_payment = $model->purchase->purchase_down_payments()->whereNotIn('status', ['void', 'reject'])->count() > 0;
        if ($is_has_down_payment) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'PO telah memiliki uang muka'));
        }

        validate_branch($model->branch_id);

        // Check available date closing
        if (!$model->check_available_date) {
            return abort(403);
        }

        if (!in_array($model->status, ['pending', 'revert'])) {
            return redirect()->route("admin.purchase.index")->with($this->ResponseMessageCRUD(false, "delete", null, "Status tidak dapat dihapus"));
        }

        DB::beginTransaction();
        try {
            $purchase_request_ids = $model->purchaseOrderServiceDetails->map(function ($purchase_order_service_detail) {
                return $purchase_order_service_detail->purchase_request_id;
            })->unique()->toArray();

            $model->delete();

            foreach ($purchase_request_ids as $purchase_request_id) {
                app('App\Http\Controllers\Admin\PurchaseRequestController')->check_purchase_request_status($purchase_request_id);
            }

            Authorization::where('model', PurchaseOrderService::class)->where('model_id', $id)->delete();
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
        $model = \App\Models\PurchaseOrderService::findOrFail($id);

        $is_has_down_payment = $model->purchase->purchase_down_payments()->whereNotIn('status', ['void', 'reject'])->count() > 0;
        if (in_array($request->status, ['revert', 'void']) && $is_has_down_payment) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'PO telah memiliki uang muka'));
        }

        // Check available date closing
        if (!$model->check_available_date) {
            return abort(403);
        }

        // * validate
        validate_branch($model->branch_id);

        // * update status
        DB::beginTransaction();

        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(\App\Models\PurchaseOrderService::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);
                $model->update([
                    'status' => $request->status,
                    'close_note' => $request->close_note ?? null,
                    'approved_by' => $request->status == 'approve' ? Auth::user()->id : null
                ]);
            } else {
                $this->create_activity_status_log(\App\Models\PurchaseOrderService::class, $id, $request->note ?? $request->message ?? 'message not available', null, $request->status);
            }

            $model->purchaseOrderServiceDetails()
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
     * Approve status the specified resource detail item from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @param int $purchase_order_service_detail_id
     * @param int $purchase_order_service_detail_item_id
     * @return \Illuminate\Http\Response
     */
    public function approve_detail_item_status(Request $request, $id, $purchase_order_service_detail_id, $purchase_order_service_detail_item_id)
    {
        // * get model
        $model = \App\Models\PurchaseOrderService::findOrFail($id);
        $detail_model = $model->purchaseOrderServiceDetails()->find($purchase_order_service_detail_id);
        $item_model = $detail_model->purchase_order_service_detail_items()->find($purchase_order_service_detail_item_id);

        // * validate
        validate_branch($model->branch_id);

        if (!$item_model) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "update", null, "Data tidak ditemukan"));
        }

        if ($model->id != $detail_model->purchase_order_service_id && $detail_model->id != $item_model->purchase_order_service_detail_id) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "update", null, "Data tidak ditemukan"));
        }

        // * update status
        DB::beginTransaction();

        try {
            $this->create_activity_status_log(\App\Models\PurchaseOrderServiceDetailItem::class, $purchase_order_service_detail_item_id, $request->message ?? 'message not available', $item_model->status, 'approve');

            $item_model->update([
                'status' => 'approve'
            ]);
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "update", null, $th->getMessage()));
        }

        // * update and check detail status
        if ($detail_model != 'partial') {
            // * if all purchase order service items status is approve set the detail to approve
            if ($detail_model->purchase_order_service_detail_items()->where('status', 'approve')->count() == $detail_model->purchase_order_service_detail_items()->count()) {
                $detail_model->update([
                    'status' => 'approve'
                ]);

                $this->create_activity_status_log(\App\Models\PurchaseOrderServiceDetail::class, $detail_model->id, "otomatis sistem", $detail_model->status, 'approve');
            } else {
                // * else  && if model detail status not in partial approve or partial reject
                if ($detail_model->status != 'partial-approve' && $detail_model->status != 'partial-rejected') {
                    $detail_model->update([
                        'status' => 'partial-approve'
                    ]);

                    $this->create_activity_status_log(\App\Models\PurchaseOrderServiceDetail::class, $detail_model->id, 'otomatis sistem', $detail_model->status, 'partial-appoved');
                }
            }
        }

        // * update and check parent status
        if ($model->status != 'partial') {
            // * if all purchase order service detail status is approve set the parent to approve
            if ($model->purchaseOrderServiceDetails()->where('status', 'approve')->count() == $model->purchaseOrderServiceDetails()->count()) {
                $model->update([
                    'status' => 'approve'
                ]);

                $this->create_activity_status_log(\App\Models\PurchaseOrderService::class, $model->id, 'otomatis sistem', $detail_model->status, 'approve');
            } else {
                // * else  && if model status not in partial approve or partial reject
                if ($model->status != 'partial-approve' && $model->status != 'partial-rejected') {
                    $model->update([
                        'status' => 'partial-approve'
                    ]);

                    $this->create_activity_status_log(\App\Models\PurchaseOrderService::class, $model->id, 'otomatis sistem', $detail_model->status, 'partial-approve');
                }
            }
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, "update", "Status berhasil diubah"));
    }

    /**
     * Reject status the specified resource detail item from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @param int $purchase_order_service_detail_id
     * @param int $purchase_order_service_detail_item_id
     * @return \Illuminate\Http\Response
     */
    public function reject_detail_item_status(Request $request, $id, $purchase_order_service_detail_id, $purchase_order_service_detail_item_id)
    {
        // * get model
        $model = \App\Models\PurchaseOrderService::findOrFail($id);
        $detail_model = $model->purchaseOrderServiceDetails()->find($purchase_order_service_detail_id);
        $item_model = $detail_model->purchase_order_service_detail_items()->find($purchase_order_service_detail_item_id);

        // * validate
        validate_branch($model->branch_id);

        if (!$item_model) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "update", null, "Data tidak ditemukan"));
        }

        if ($model->id != $detail_model->purchase_order_service_id && $detail_model->id != $item_model->purchase_order_service_detail_id) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "update", null, "Data tidak ditemukan"));
        }

        // * update status
        DB::beginTransaction();

        try {
            $this->create_activity_status_log(\App\Models\PurchaseOrderServiceDetailItem::class, $purchase_order_service_detail_item_id, $request->message ?? 'message not available', $item_model->status, 'reject');

            $item_model->update([
                'status' => 'reject'
            ]);
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "update", null, $th->getMessage()));
        }

        // * update and check detail status
        if ($detail_model != 'partial') {
            // * if all purchase order service items status is approve set the detail to approve
            if ($detail_model->purchase_order_service_detail_items()->where('status', 'reject')->count() == $detail_model->purchase_order_service_detail_items()->count()) {
                $detail_model->update([
                    'status' => 'reject'
                ]);

                $this->create_activity_status_log(\App\Models\PurchaseOrderServiceDetail::class, $detail_model->id, "otomatis sistem", $detail_model->status, 'approve');
            } else {
                // * else  && if model detail status not in partial approve or partial reject
                if ($detail_model->status != 'partial-approve' && $detail_model->status != 'partial-rejected') {
                    $detail_model->update([
                        'status' => 'partial-reject'
                    ]);

                    $this->create_activity_status_log(\App\Models\PurchaseOrderServiceDetail::class, $detail_model->id, 'otomatis sistem', $detail_model->status, 'partial-appoved');
                }
            }
        }

        // * update and check parent status
        if ($model->status != 'partial') {
            // * if all purchase order service detail status is approve set the parent to approve
            if ($model->purchaseOrderServiceDetails()->where('status', 'reject')->count() == $model->purchaseOrderServiceDetails()->count()) {
                $model->update([
                    'status' => 'reject'
                ]);

                $this->create_activity_status_log(\App\Models\PurchaseOrderService::class, $model->id, 'otomatis sistem', $detail_model->status, 'approve');
            } else {
                // * else  && if model status not in partial approve or partial reject
                if ($model->status != 'partial-approve' && $model->status != 'partial-rejected') {
                    $model->update([
                        'status' => 'partial-rejected'
                    ]);

                    $this->create_activity_status_log(\App\Models\PurchaseOrderService::class, $model->id, 'otomatis sistem', $detail_model->status, 'partial-approve');
                }
            }
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, "update"));
    }

    /**
     * Revert status the specified resource detail item from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @param int $purchase_order_service_detail_id
     * @param int $purchase_order_service_detail_item_id
     * @return \Illuminate\Http\Response
     */
    public function revert_detail_item_status(Request $request, $id, $purchase_order_service_detail_id, $purchase_order_service_detail_item_id)
    {
        // * get model
        $model = \App\Models\PurchaseOrderService::findOrFail($id);
        $detail_model = $model->purchaseOrderServiceDetails()->find($purchase_order_service_detail_id);
        $item_model = $detail_model->purchase_order_service_detail_items()->find($purchase_order_service_detail_item_id);

        // * validate
        validate_branch($model->branch_id);

        if (!$item_model) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "update", null, "Data tidak ditemukan"));
        }

        if ($model->id != $detail_model->purchase_order_service_id && $detail_model->id != $item_model->purchase_order_service_detail_id) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "update", null, "Data tidak ditemukan"));
        }

        // * update status
        DB::beginTransaction();

        try {
            $this->create_activity_status_log(\App\Models\PurchaseOrderServiceDetailItem::class, $purchase_order_service_detail_item_id, $request->message ?? 'message not available', $item_model->status, 'pending');

            $item_model->update([
                'status' => 'pending'
            ]);
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "update", null, $th->getMessage()));
        }

        // * update and check detail status
        if ($detail_model != 'partial') {
            // * if all purchase order service items status is approve set the detail to approve
            if ($detail_model->purchase_order_service_detail_items()->where('status', 'pending')->count() == $detail_model->purchase_order_service_detail_items()->count()) {
                $detail_model->update([
                    'status' => 'pending'
                ]);

                $this->create_activity_status_log(\App\Models\PurchaseOrderServiceDetail::class, $detail_model->id, "otomatis sistem", $detail_model->status, 'approve');
            } else {
                // * else  && if model detail status not in partial approve or partial reject
                if ($detail_model->status != 'partial-approve' && $detail_model->status != 'partial-rejected') {
                    $detail_model->update([
                        'status' => 'partial-reject'
                    ]);

                    $this->create_activity_status_log(\App\Models\PurchaseOrderServiceDetail::class, $detail_model->id, 'otomatis sistem', $detail_model->status, 'partial-appoved');
                }
            }
        }

        // * update and check parent status
        if ($model->status != 'partial') {
            // * if all purchase order service detail status is approve set the parent to approve
            if ($model->purchaseOrderServiceDetails()->where('status', 'pending')->count() == $model->purchaseOrderServiceDetails()->count()) {
                $model->update([
                    'status' => 'pending'
                ]);

                $this->create_activity_status_log(\App\Models\PurchaseOrderService::class, $model->id, 'otomatis sistem', $detail_model->status, 'approve');
            } else {
                // * else  && if model status not in partial approve or partial reject
                if ($model->status != 'partial-approve' && $model->status != 'partial-rejected') {
                    $model->update([
                        'status' => 'partial-approve'
                    ]);

                    $this->create_activity_status_log(\App\Models\PurchaseOrderService::class, $model->id, 'otomatis sistem', $detail_model->status, 'partial-approve');
                }
            }
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
        $model = \App\Models\PurchaseOrderService::with([
            'branch',
            'vendor',
            'currency',
        ])
            ->findOrFail($id);

        $main = $model->purchaseOrderServiceDetails()->where('type', 'main')->with([
            'purchase_request.division',
            'purchase_order_service_detail_items.purchase_request_detail.unit',
            'purchase_order_service_detail_items.item.unit',
            'purchase_order_service_detail_items.purchase_order_service_detail_item_taxes.tax',
        ])->get();

        $additional = $model->purchaseOrderServiceDetails()->where('type', 'additional')->with([
            'purchase_order_service_detail_items.item.unit',
            'purchase_order_service_detail_items.purchase_order_service_detail_item_taxes.tax',
        ])->get();

        if (!in_array($model->status, ['pending', 'revert'])) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "edit", null, "Data tidak dapat diubah karena status sudah bukan pending"));
        }

        validate_branch($model->branch_id);

        return $this->ResponseJsonData(compact('model', 'main', 'additional'));
    }

    /**
     * Select api for item receiving report
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function select_api_for_item_receiving(Request $request)
    {
        $model = \App\Models\PurchaseOrderService::whereIn('status', ['approve', 'partial-approve', 'partial-rejected', 'partial'])
            ->leftJoin('vendors', 'vendors.id', '=', 'purchase_order_services.vendor_id')
            ->when($request->branch_id && get_current_branch()->is_primary, function ($query) use ($request) {
                $query->where('purchase_order_services.branch_id', $request->branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                $query->where('purchase_order_services.branch_id', get_current_branch()->id);
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('purchase_order_services.code', 'like', '%' . $request->search . '%')
                        ->orWhere('purchase_order_services.date', 'like', '%' . $request->search . '%')
                        ->orWhere('purchase_order_services.spk_number', 'like', '%' . $request->search . '%')
                        ->orWhere('vendors.nama', 'like', '%' . $request->search . '%');
                });
            })
            ->orderByDesc('purchase_order_services.created_at')
            ->select('purchase_order_services.id', 'purchase_order_services.code', 'vendors.nama', 'purchase_order_services.is_spk', 'purchase_order_services.spk_number')
            ->paginate(10);

        $model->getCollection()->transform(function ($m) {
            $code = $m->code;
            if ($m->is_spk) {
                $code .= ' / ' . $m->spk_number;
            }
            $m->final_code = $code;

            return $m;
        });

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
        $model = \App\Models\PurchaseOrderService::with('vendor')->findOrFail($id);

        // * validate
        validate_branch($model->branch_id);

        $details = \App\Models\PurchaseOrderServiceDetail::where('purchase_order_service_id', $model->id)
            ->leftJoin('purchase_order_service_detail_items', 'purchase_order_service_detail_items.purchase_order_service_detail_id', 'purchase_order_service_details.id')
            ->whereIn('purchase_order_service_details.status', ['approve', 'partial-approve', 'partial-rejected', 'partial'])
            ->with([
                'purchase_order_service_detail_items.item',
                'purchase_order_service_detail_items.unit',
                'purchase_order_service_detail_items' => function ($q) {
                    $q->where('quantity', '>', 'quantity_received');
                    $q->whereIn('status', ['approve', 'partial-approve', 'partial-rejected', 'partial']);
                },
            ])
            ->select('purchase_order_service_details.*')
            ->distinct('purchase_order_service_details.id')
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
        if (!$request->preview && authorizePrint('purchase_order_service')) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                PurchaseOrderService::class,
                decryptId($id),
                'purchase_order_service',
            );

            if (!$result) {
                return abort(403);
            }
        }

        $model = \App\Models\PurchaseOrderService::findOrFail(decryptId($id));
        $project_id = $model->purchaseOrderServiceDetails->map(function ($purchase_order_service_detail) {
            return $purchase_order_service_detail->purchase_request->project_id ?? null;
        })->unique()->toArray();

        $projects = Project::whereIn('id', $project_id)->get();

        $purchase_request_code = PurchaseRequest::whereIn('id', $model->purchaseOrderServiceDetails->pluck('purchase_request_id')->toArray())->pluck('kode')->toArray();
        $purchase_order_service_item = PurchaseOrderServiceDetailItem::whereIn('purchase_order_service_detail_id', $model->purchaseOrderServiceDetails->pluck('id')->toArray())->get();
        $subtotal = $purchase_order_service_item->sum('sub_total');

        $before_discount = $purchase_order_service_item->map(function ($purchase_order_service_detail_item) {
            return $purchase_order_service_detail_item->quantity * $purchase_order_service_detail_item->price_display;
        })->sum();

        $discount_total = $purchase_order_service_item->map(function ($purchase_order_service_detail_item) {
            return $purchase_order_service_detail_item->quantity * $purchase_order_service_detail_item->discount;
        })->sum();

        $subtotal = $purchase_order_service_item->sum('sub_total');

        $taxes = PurchaseOrderserviceDetailItemTax::whereIn('purchase_order_service_detail_item_id', $purchase_order_service_item->pluck('id')->toArray())->get();
        $tax_data = $taxes->unique('tax_id')->map(function ($tax) use ($taxes) {
            $tax->total =  $taxes->where('tax_id', $tax->tax_id)->sum('total');
            return $tax;
        });

        if ($request->type == 'spk') {
            $qr_url = route('purchase-order-service.export-pdf', ['id' => $id, 'type' => 'spk']);
        } else {
            $qr_url = route('purchase-order-service.export-pdf', ['id' => $id]);
            if ($request->type == 'closed') {
                $qr_url .= '?type=closed';
            }
        }
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));

        $approval = Authorization::where('model', \App\Models\PurchaseOrderService::class)
            ->with(['details' => function ($q) {
                $q->where('status', 'approve')
                    ->where('note', 'not like', '%otomatis%');
            }])
            ->where('model_id', $model->id)
            ->first();

        if ($request->type == 'spk') {
            $pdf = PDF::loadView("admin.$this->view_folder.pdf.export_spk", compact('model', 'qr', 'purchase_request_code', 'before_discount', 'discount_total', 'subtotal', 'tax_data', 'approval', 'projects'));
        } elseif ($request->type == 'closed') {
            $taxes = $taxes->unique('tax_id');
            $pdf = PDF::loadView("admin.$this->view_folder.pdf.export-closed", compact('model', 'qr', 'purchase_request_code', 'before_discount', 'discount_total', 'subtotal', 'tax_data', 'approval', 'projects'));
        } else {
            $pdf = PDF::loadView("admin.$this->view_folder.pdf.export", compact('model', 'qr', 'purchase_request_code', 'before_discount', 'discount_total', 'subtotal', 'tax_data', 'approval', 'projects'));
        }
        $pdf->setPaper($request->paper ?? 'a4', $request->orientation ?? 'potrait');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $w = $canvas->get_width();
        $h = $canvas->get_height();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM} / {PAGE_COUNT}", '', 8);

        if ($request->preview) {
            Storage::disk('public')->deleteDirectory('tmp_purchase_order_service');
            $tmp_file_name = 'purchase_order_service_' . time() . '.pdf';
            $path = 'tmp_purchase_order_service/' . $tmp_file_name;
            Storage::disk('public')->put($path, $pdf->output());

            return response()->json($path);
        }

        if ($request->type == 'spk') {
            return $pdf->stream("SPK $model->date - $model->spk_number.pdf");
        } else {
            return $pdf->stream("PURCHASE ORDER SERVICE $model->date - $model->code.pdf");
        }
    }

    public function history($id, Request $request)
    {
        try {
            $purchase_order_services = DB::table('purchase_order_service_details')
                ->join('purchase_order_services', 'purchase_order_services.id', '=', 'purchase_order_service_details.purchase_order_service_id')
                ->whereNull('purchase_order_services.deleted_at')
                ->where('purchase_order_service_id', $id)
                ->select(
                    'purchase_order_services.id',
                    'purchase_order_services.code',
                    'purchase_order_services.date',
                    'purchase_order_services.status',
                    'purchase_order_service_details.purchase_request_id',
                )
                ->get();

            $purchase_order_services = $purchase_order_services->map(function ($item) {
                $item->link = route('admin.purchase-order-service.show', $item->id);
                $item->menu = 'purchase order service';
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
                $item->link = route('admin.purchase-request.show', $item->id);
                $item->menu = 'purchase request';
                return $item;
            });

            $item_receiving_reports = DB::table('item_receiving_reports')
                ->where('reference_model', PurchaseOrderService::class)
                ->whereIn('reference_id', $purchase_order_services->pluck('id')->toArray())
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
                ->merge($purchase_order_services->unique('id'))
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
