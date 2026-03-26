<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOrderGeneral as model;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Http\Helpers\PrintHelper;
use App\Models\Authorization;
use App\Models\DeliveryOrderGeneral;
use App\Models\DeliveryOrderGeneralDetail;
use App\Models\SaleOrderGeneral;
use App\Models\WareHouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DeliveryOrderGeneralController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'delivery-order-general';

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
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                'delivery_order_generals.id',
                'delivery_order_generals.date',
                'delivery_order_generals.code',
                'customers.nama',
                'sale_order_generals.kode',
                'delivery_order_generals.status',
                'delivery_order_generals.created_at',
                'delivery_order_generals.id',
                'delivery_order_generals.id',
            ];


            // * get data with date
            $search = $request->input('search.value');
            $query = model::with(['customer', 'sale_order_general'])
                ->join('customers', 'customers.id', 'delivery_order_generals.customer_id')
                ->join('sale_order_generals', 'sale_order_generals.id', 'delivery_order_generals.sale_order_general_id')
                ->select('delivery_order_generals.*')
                ->when(get_current_branch()->is_primary && $request->branch_id, fn($q) => $q->where('delivery_order_generals.branch_id', $request->branch_id))
                ->when(!get_current_branch()->is_primary, fn($q) => $q->where('delivery_order_generals.branch_id', Auth::user()->branch_id))
                ->when($request->from_date, fn($q) => $q->whereDate('delivery_order_generals.date', '>=', Carbon::parse($request->from_date)))
                ->when($request->to_date, fn($q) => $q->whereDate('delivery_order_generals.date', '<=', Carbon::parse($request->to_date)))
                ->when($request->status, fn($q) => $q->where('delivery_order_generals.status', $request->status))
                ->when($request->customer_id, fn($q) => $q->where('delivery_order_generals.customer_id', $request->customer_id))
                ->when($search, function ($q) use ($search) {
                    $q->where('delivery_order_generals.code', 'like', "%{$search}%")
                        ->orWhere('delivery_order_generals.date', 'like', "%{$search}%")
                        ->orWhere('customers.nama', 'like', "%{$search}%")
                        ->orWhere('sale_order_generals.kode', 'like', "%{$search}%")
                        ->orWhere('delivery_order_generals.status', 'like', "%{$search}%")
                        ->orWhere('delivery_order_generals.date', 'like', "%{$search}%");
                });

            // * calculate some data
            $totalData = $query->count();
            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $totalFiltered = $query->count();

            $query->select('delivery_order_generals.*')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir);

            $checkAuthorizePrint = authorizePrint('delivery_order_general');

            $data = $query->get();

            // * make data table
            $results = array();
            if (!empty($data)) {
                foreach ($data as $key => $single_data) {
                    $badge = '<div class="badge badge-lg badge-' . delivery_order_general_status()[$single_data->status]['color'] . '">
                                            ' . delivery_order_general_status()[$single_data->status]['label'] . ' - ' . delivery_order_general_status()[$single_data->status]['text'] . '
                                        </div>';

                    $nestedData['DT_RowIndex'] = $key + 1;
                    $nestedData['id'] = $single_data->id;
                    $nestedData['date'] = localDate($single_data->date);
                    $nestedData['code'] = '<a href="' . route("admin.$this->view_folder.index") . '/' . $single_data->id . '" class="text-primary">' . $single_data->code . '</a>';
                    $nestedData['customer']['nama'] = $single_data->customer?->nama;

                    $link = route('admin.sales-order-general.show', ['sales_order_general' => $single_data->sale_order_general_id]);
                    $nestedData['sale_order_general']['kode'] = '<a href="' . $link . '" class="text-primary" target="_blank">' . $single_data->sale_order_general?->kode . '</a>';
                    $nestedData['status'] = $badge;
                    $nestedData['created_at'] = toDayDateTimeString($single_data->created_at);
                    $link_export = route("delivery-order-general.export.id", ['id' => encryptId($single_data->id)]);
                    $link_detail = route("admin.delivery-order-general.show", ['delivery_order_general' => encryptId($single_data->id)]);
                    $nestedData['export'] = '<a href="' . $link_export . '" class="btn btn-sm btn-flat btn-info" target="_blank" onclick="show_print_out_modal(event)" ' . ($checkAuthorizePrint ? 'data-model="' . \App\Models\DeliveryOrderGeneral::class . '" data-id="' . $single_data->id . '" data-print-type="delivery_order_general" data-link="' . $link_detail . '" data-code="' . $single_data->code . '"' : '') . '>Export</a>';
                    $nestedData['action'] = Blade::render('components.datatable.button-datatable', [
                        'row' => $single_data,
                        'main' => $this->view_folder,
                        'permission_name' => $this->view_folder,
                        'btn_config' => [
                            'detail' => [
                                'display' => false,
                            ],
                            'edit' => [
                                'display' => $single_data->check_available_date ? in_array($single_data->status, ['pending', 'revert']) : false,
                            ],
                            'delete' => [
                                'display' => false,
                            ],
                        ],
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
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(model::class)) {
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
        // * validate
        $this->validate($request, [

            // * parent
            'sale_order_general_id' => 'required|exists:sale_order_generals,id',
            'date' => 'required|date',
            'target_delivery' => 'required|date',
            'date_send' => 'nullable|date',
            'date_receive' => 'nullable|date',
            'supply' => 'nullable|string|max:100',
            'drop' => 'required|string|max:100',
            'description' => 'nullable|string',
            'ware_house_id' => 'required',

            'ware_house_id' => 'required|exists:ware_houses,id',

            // * detail
            'sale_order_general_detail_id.*' => 'required|exists:sale_order_general_details,id',
            'quantity.*' => 'required',
            'quantity_received.*' => 'required',
            'detail_description.*' => 'nullable|string',
        ]);

        // * get data
        $sale_order_general = SaleOrderGeneral::find($request->sale_order_general_id);

        DB::beginTransaction();

        // * create parent data
        $model = new model();

        // * create child data #####################################################################
        // if (is_array($request->sale_order_general_detail_id)) {
        //     foreach ($request->sale_order_general_detail_id as $key => $value) {
        //         if ($request->quantity[$key] <= 0) {
        //             return redirect()->back()->with($this->ResponseMessageCRUD(false, 'craete', null, 'Quantity dikirim harus diisi'));
        //         }
        //     }
        // }

        $model->loadModel([
            'branch_id' => $sale_order_general->branch_id,
            'sale_order_general_id' => $request->sale_order_general_id,
            'customer_id' => $sale_order_general->customer_id,
            'external_code' => $request->external_code,
            'ware_house_id' => $request->ware_house_id,
            'date' => Carbon::parse($request->date),
            'target_delivery' => Carbon::parse($request->target_delivery),
            'date_send' => Carbon::parse($request->date_send),
            'date_receive' => Carbon::parse($request->date_receive),
            'supply' => $request->supply ?? '-',
            'drop' => $request->drop,
            'description' => $request->description,
            'ware_house_id' => $request->ware_house_id,
            'created_by' => Auth::user()->id
        ]);

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }
        // * create child data #####################################################################
        if (is_array($request->sale_order_general_detail_id)) {
            foreach ($request->sale_order_general_detail_id as $key => $value) {
                // ? is quantity not null or greater than 0 create child data
                if ($request->quantity[$key] != null && $request->quantity[$key] > 0) {
                    // * find data sale order
                    $sale_order_general_detail = $sale_order_general->sale_order_general_details()->find($value);

                    $model_detail = new DeliveryOrderGeneralDetail();
                    $model_detail->loadModel([
                        'delivery_order_general_id' => $model->id,
                        'sale_order_general_detail_id' => $value,
                        'item_id' => $sale_order_general_detail->item_id,
                        'unit_id' => $sale_order_general_detail->unit_id,
                        'hpp' => $sale_order_general_detail->item->getCurrentValue() ?? 0,
                        'quantity' => thousand_to_float($request->quantity[$key]),
                        'quantity_received' => !empty($request->quantity_received[$key]) ? thousand_to_float($request->quantity_received[$key]) : 0,
                        'quantity_returned' => !empty($request->quantity_returned[$key]) ? thousand_to_float($request->quantity_returned[$key]) : 0,
                        'quantity_lost' => !empty($request->quantity_lost[$key]) ? thousand_to_float($request->quantity_lost[$key]) : 0,
                        'quantity_damage' => !empty($request->quantity_damage[$key]) ? thousand_to_float($request->quantity_damage[$key]) : 0,
                        'description' => $request->detail_description[$key] ?? null,
                    ]);

                    try {
                        $model_detail->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
                    }
                }
            }
        }

        // Trigger to update Sended in SaleOrderGeneral

        $model = DeliveryOrderGeneral::find($model->id);
        $model->delivery_order_general_details->each(function ($detail) use ($model) {
            try {
                Log::error('Message');
                $detail->sale_order_general_detail->update([
                    'sended' => $detail->sale_order_general_detail->sended + $detail->quantity,
                ]);
            } catch (\Throwable $th) {
                throw $th;
            }
        });

        // * end create child data #####################################################################

        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        $authorization->init(
            branch_id: $model->branch_id,
            user_id: auth()->user()->id,
            model: model::class,
            model_id: $model->id,
            amount: 0,
            title: "DO General",
            subtitle: Auth::user()->name . " mengajukan DO General " . $model->code,
            link: route('admin.delivery-order-general.show', $model),
            update_status_link: route('admin.delivery-order-general.update-status', ['id' => $model->id]),
            division_id: auth()->user()->division_id ?? null
        );

        DB::commit();

        return redirect()->route("admin.delivery.index")->with($this->ResponseMessageCRUD());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = model::findOrFail($id);
        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: model::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );

        $authorization_logs['can_revert'] = false;
        $authorization_logs['can_void'] = $model->check_available_date && $model->status == 'approve' && $model->is_invoice_created == 0;
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = false;
        $authorization_logs['can_void_request'] = $model->check_available_date && $model->status == 'approve' && $model->is_invoice_created == 0;
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'auth_revert_void_button'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = model::findOrFail($id);

        if (in_array($model->status, ['done', 'reject', 'void'])) {
            return  abort(403);
        }

        if (!$model->check_available_date) {
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
        $this->validate($request, [
            // * parent
            'date' => 'nullable|date',
            'date_send' => 'nullable|date',
            'date_receive' => 'nullable|date',
            'target_delivery' => 'nullable|date',
            'supply' => 'nullable|string|max:100',
            'drop' => 'required|string|max:100',
            'description' => 'nullable|string',

            // * detail
            'delivery_order_general_detail_id.*' => 'required|exists:delivery_order_general_details,id',
            'ware_house_id.*' => 'nullable|exists:ware_houses,id',
            'quantity_received.*' => 'nullable',
            'quantity_returned.*' => 'nullable',
            'quantity_lost.*' => 'nullable',
            'quantity_damage.*' => 'nullable',
            'description.*' => 'nullable|stirng',
        ]);

        $model = model::findOrFail($id);

        if (in_array($model->status, ['done', 'reject', 'void'])) {
            return  abort(403);
        }

        if (!$model->check_available_date) {
            return abort(403);
        }

        DB::beginTransaction();
        $model->loadModel([
            'ware_house_id' => $request->ware_house_id,
            'external_code' => $request->external_code,
            'date' => Carbon::parse($request->date),
            'date_send' => Carbon::parse($request->date_send),
            'date_receive' => Carbon::parse($request->date_receive),
            'target_delivery' => Carbon::parse($request->target_delivery),
            'supply' => $request->supply ?? '-',
            'drop' => $request->drop,
            'description' => $request->description,
        ]);

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        // * update child data #####################################################################
        if (is_array($request->delivery_order_general_detail_id)) {
            foreach ($request->delivery_order_general_detail_id as $key => $value) {
                // * find data delivery order detail order
                $delivery_order_general_detail = $model->delivery_order_general_details()->find($value);

                // * validate quantity
                $qty_receive = thousand_to_float($request->quantity_received[$key] ?? 0);
                $qty_lost_damage = thousand_to_float($request->quantity_lost[$key] ?? 0) + thousand_to_float($request->quantity_damage[$key] ?? 0);

                if ($qty_receive < $qty_lost_damage) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, 'Quantity Received cannot be less than Quantity Lost and damaged'));
                }

                // * update data
                $delivery_order_general_detail->loadModel([
                    'quantity_received' => thousand_to_float($request->quantity_received[$key] ?? 0),
                    'quantity_returned' => thousand_to_float($request->quantity_returned[$key] ?? 0),
                    'quantity_lost' => thousand_to_float($request->quantity_lost[$key] ?? 0),
                    'quantity_damage' => thousand_to_float($request->quantity_damage[$key] ?? 0),
                    'description' => $request->detail_description[$key] ?? null,
                ]);

                try {
                    $delivery_order_general_detail->save();
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
                }
            }
        }
        // * end update child data #####################################################################

        if (in_array($model->status, ['pending', 'revert'])) {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: 0,
                title: "DO General",
                subtitle: Auth::user()->name . " mengajukan DO General " . $model->code,
                link: route('admin.delivery-order-general.show', $model),
                update_status_link: route('admin.delivery-order-general.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * update_status
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, $id)
    {
        $model = model::findOrFail($id);

        if (!$model->check_available_date) {
            abort(403);
        }

        DB::beginTransaction();

        // * validate status when approve
        // if ($request->status == 'approve') {
        //     if (is_null($model->date_send)) {
        //         return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', "edit status", "Date Send cannot be empty"));
        //     }
        // }

        // * validate status when done
        if ($request->status == 'done') {
            if (is_null($model->date_receive)) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', "edit status", "Date Received cannot be empty"));
            }

            // * validate quantity received
            foreach ($model->delivery_order_general_details as $key => $value) {
                if (is_null($value->quantity_received) or $value->quantity_received <= 0) {
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', "edit status", "Quantity Received cannot be 0. "));
                }
            }
        }

        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(model::class, $model->id,  $request->note ?? $request->message ?? "Message not available", $model->status, $request->status);
                $model->status = $request->status;
                if ($model->status == 'approve') {
                    $model->approved_by = Auth::user()->id;
                }
                $model->save();
            } else {
                $this->create_activity_status_log(model::class, $model->id,  $request->note ?? $request->message ?? "Message not available", null, $request->status);
            }

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', "edit status", $th->getMessage()));
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'edit', 'edit status'));
    }

    /**
     * getStockInAWarehouse
     *
     * @param Request $request
     * @param $warehouse_id
     * @param $id
     * @return mixed
     */
    public function getStockInAWarehouse(Request $request, $warehouse_id, $id)
    {
        // if ($request->ajax()) {
        $model = model::with('delivery_order_general_details.item.unit')->findOrFail($id);
        $warehouse = \App\Models\WareHouse::find($warehouse_id);

        if (!$warehouse) {
            return $this->ResponseJsonNotFound();
        }

        $data = [];
        foreach ($model->delivery_order_general_details as $key => $value) {
            $in = \App\Models\StockMutation::where('ware_house_id', $warehouse_id)
                ->where('item_id', $value->item_id)
                ->whereNull('is_return')
                ->sum('in');

            $out = \App\Models\StockMutation::where('ware_house_id', $warehouse_id)
                ->where('item_id', $value->item_id)
                ->whereNull('is_return')
                ->sum('out');

            $data[] = [
                'id' => $value->id,
                'unit' => $value->item?->unit,
                'stock' => $in - $out,
            ];
        }

        return $this->ResponseJsonData($data);
    }

    public function getStockInAWarehouseWhileCreate(Request $request)
    {
        $saleOrder = \App\Models\SaleOrderGeneral::findOrFail($request->id);
        $saleOrderDetails = \App\Models\SaleOrderGeneralDetail::where('sale_order_general_id', $request->id)->get();
        $saleOrderItemIds = $saleOrderDetails->pluck('item_id')->toArray();
        $warehouse = \App\Models\WareHouse::findOrFail($request->ware_house_id);

        $stockMutations = \App\Models\StockMutation::where('ware_house_id', $request->ware_house_id)
            ->whereIn('item_id', $saleOrderItemIds)
            ->get();

        $data = [];

        foreach ($saleOrderItemIds as $saleOrderItemId) {
            $in = $stockMutations->where('item_id', $saleOrderItemId)->sum('in');
            $out = $stockMutations->where('item_id', $saleOrderItemId)->sum('out');

            $data[] = [
                'stock' => $in - $out,
                'item' => $saleOrderItemId,
            ];
        }

        return $this->ResponseJsonData($data);
    }

    public function get_by_customer_so(Request $request)
    {
        $model = model::with(['customer', 'sale_order_general'])
            ->whereIn('status', ['approve', 'done'])
            ->where('customer_id', $request->customer_id)
            ->whereHas('sale_order_general', function ($q) use ($request) {
                $q->where('currency_id', $request->currency_id);
            })
            ->where('branch_id', $request->branch_id)
            ->whereIn('sale_order_general_id', $request->sale_order_general_id)
            ->where('is_invoice_created', false)
            ->when($request->search, function ($q) use ($request) {
                $q->where('code', 'like', "%{$request->search}%");
            })
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return $this->ResponseJsonData($model);
    }

    /**
     * selectForInvoiceGeneral
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function selectForInvoiceGeneral(Request $request)
    {
        if ($request->search) {
            $model = model::with(['customer', 'sale_order_general'])
                ->whereIn('status', ['approve', 'done'])
                ->where('is_invoice_created', false)
                ->where(function ($query) use ($request) {
                    $query->where('code', 'like', "%{$request->search}%")
                        ->orWhereHas('customer', function ($query) use ($request) {
                            $query->where('nama', 'like', "%{$request->search}%");
                        });
                })
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        } else {
            $model = model::with(['customer', 'sale_order_general'])
                ->whereIn('status', ['approve', 'done'])
                ->where('is_invoice_created', false)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        return $this->ResponseJsonData($model);
    }

    /**
     * detailForInvoiceGeneralDetail
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function detailForInvoiceGeneralDetail($id = null)
    {
        // if ($request->ajax()) {
        $model = model::with([
            'sale_order_general.currency',
            'customer.customer_banks.bank_internal',
            'delivery_order_general_details.delivery_order_general',
            'delivery_order_general_details.item',
            'delivery_order_general_details.unit',
            'delivery_order_general_details.sale_order_general_detail',
            'delivery_order_general_details.sale_order_general_detail.sale_order_general_detail_taxes',
            'delivery_order_general_details.sale_order_general_detail.sale_order_general_detail_taxes.tax',
        ])
            ->findOrFail($id);

        return $this->ResponseJsonData($model);
        // }
    }

    public function export($id, Request $request)
    {
        if (!$request->preview && authorizePrint('delivery_order_general')) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                model::class,
                decryptId($id),
                'delivery_order_general',
            );

            if (!$result) {
                return abort(403);
            }
        }

        $model = model::with('delivery_order_general_details', 'sale_order_general', 'created_by_user', 'approved_by_user')->findOrFail(decryptId($id));
        $file = public_path('/pdf_reports/Report-Invoice-General-' . microtime(true) . '.pdf');
        $fileName = 'Report-Delivery-Order-General-' . microtime(true) . '.pdf';

        $qr_url = route('delivery-order-general.export.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $approval = Authorization::where('model', model::class)
            ->with(['details' => function ($q) {
                $q->where('status', 'approve')
                    ->where('note', 'not like', '%otomatis%');
            }])
            ->where('model_id', $model->id)
            ->first();

        $pdf = PDF::loadview("admin/.$this->view_folder./export", compact('model', 'qr', 'approval'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $w = $canvas->get_width();
        $h = $canvas->get_height();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM}/{PAGE_COUNT}", '', 8);

        if ($request->preview) {
            Storage::disk('public')->deleteDirectory('tmp_delivery_order_general');
            $tmp_file_name = 'delivery_order_general_' . time() . '.pdf';
            $path = 'tmp_delivery_order_general/' . $tmp_file_name;
            Storage::disk('public')->put($path, $pdf->output());

            return response()->json($path);
        }

        return $pdf->stream($fileName);
    }

    public function close(Request $request, $id)
    {
        $losses_coa = get_default_coa('finance', 'Losess DO General');
        $default_warehouse = WareHouse::where('nama', 'like', '%Gudang Reject%')->first();

        if (!$losses_coa || !$default_warehouse) {
            throw new \Exception("Losses COA or Default Warehouse not found");
        }

        $this->validate($request, [
            // * detail
            'delivery_order_general_detail_id.*' => 'required|exists:delivery_order_general_details,id',
            'quantity_received.*' => 'required',
            'quantity_returned.*' => 'required',
            'quantity_lost.*' => 'required',
            'quantity_damage.*' => 'required',
            'description.*' => 'nullable|stirng',
        ]);

        $model = model::findOrFail($id);

        if (in_array($model->status, ['done', 'reject', 'void'])) {
            return  abort(403);
        }

        if (!$model->check_available_date) {
            return abort(403);
        }

        DB::beginTransaction();
        try {
            if (is_array($request->delivery_order_general_detail_id)) {
                foreach ($request->delivery_order_general_detail_id as $key => $value) {
                    // * find data delivery order detail order
                    $delivery_order_general_detail = $model->delivery_order_general_details()->find($value);

                    // * validate quantity
                    $qty_receive = thousand_to_float($request->quantity_received[$key] ?? 0);
                    $qty_lost_damage = thousand_to_float($request->quantity_lost[$key] ?? 0) + thousand_to_float($request->quantity_damage[$key] ?? 0);

                    if ($qty_receive < $qty_lost_damage) {
                        DB::rollBack();

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, 'Quantity Received cannot be less than Quantity Lost and damaged'));
                    }

                    // * update data
                    $delivery_order_general_detail->loadModel([
                        'quantity_received' => thousand_to_float($request->quantity_received[$key] ?? 0),
                        'quantity_returned' => thousand_to_float($request->quantity_returned[$key] ?? 0),
                        'quantity_lost' => thousand_to_float($request->quantity_lost[$key] ?? 0),
                        'quantity_damage' => thousand_to_float($request->quantity_damage[$key] ?? 0),
                        'description' => $request->detail_description[$key] ?? null,
                    ]);

                    $delivery_order_general_detail->save();
                }
            }

            $journal = new \App\Http\Helpers\JournalHelpers('delivery-order-general-losses', $model->id);
            $journal->generate();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    public function history($id, Request $request)
    {
        try {
            $delivery_order_generals = DB::table('delivery_order_general_details')
                ->join('delivery_order_generals', 'delivery_order_generals.id', '=', 'delivery_order_general_details.delivery_order_general_id')
                ->whereNull('delivery_order_generals.deleted_at')
                ->where('delivery_order_generals.id', $id)
                ->select(
                    'delivery_order_generals.id',
                    'delivery_order_generals.code',
                    'delivery_order_generals.date',
                    'delivery_order_generals.status',
                    'delivery_order_generals.sale_order_general_id',
                    'delivery_order_general_details.id as delivery_order_general_detail_id',
                )->get();

            $sale_order_generals = DB::table('sale_order_general_details')
                ->join('sale_order_generals', 'sale_order_generals.id', '=', 'sale_order_general_details.sale_order_general_id')
                ->whereNull('sale_order_generals.deleted_at')
                ->whereIn('sale_order_general_id', $delivery_order_generals->pluck('sale_order_general_id')->toArray())
                ->select(
                    'sale_order_generals.id',
                    'sale_order_generals.kode as code',
                    'sale_order_generals.tanggal as date',
                    'sale_order_generals.status',
                )
                ->get();

            $invoice_generals = DB::table('invoice_general_details')
                ->join('invoice_generals', 'invoice_generals.id', '=', 'invoice_general_details.invoice_general_id')
                ->join('invoice_parents', function ($query) {
                    $query->on('invoice_generals.id', '=', 'invoice_parents.reference_id')
                        ->where('invoice_parents.model_reference', '=', 'App\Models\InvoiceGeneral');
                })
                ->whereIn('invoice_general_details.delivery_order_general_detail_id', $delivery_order_generals->pluck('delivery_order_general_detail_id')->toArray())
                ->select(
                    'invoice_generals.id',
                    'invoice_generals.code',
                    'invoice_generals.date',
                    'invoice_generals.status',
                    'invoice_parents.id as invoice_parent_id',
                )->get();

            $invoice_returns = DB::table('invoice_returns')
                ->whereIn('reference_id', $delivery_order_generals->pluck('id')->toArray())
                ->where('reference_model', 'App\Models\DeliveryOrderGeneral')
                ->whereNotIn('status', ['rejected', 'void'])
                ->whereNull('deleted_at')
                ->select(
                    'id',
                    'code',
                    'date',
                    'status',
                )->get();

            $receivables_payments = DB::table('receivables_payment_details')
                ->where('invoice_parent_id', $invoice_generals->pluck('invoice_parent_id')->toArray())
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

            $delivery_order_generals = $delivery_order_generals->map(function ($item) {
                $item->link = route('admin.delivery-order-general.show', $item->id);
                $item->menu = 'delivery order general';
                return $item;
            });

            $sale_order_generals = $sale_order_generals->map(function ($item) {
                $item->link = route('admin.sales-order-general.show', $item->id);
                $item->menu = 'sales order general';
                return $item;
            });

            $invoice_generals = $invoice_generals->map(function ($item) {
                $item->link = route('admin.invoice-general.show', $item->id);
                $item->menu = 'invoice general';
                return $item;
            });

            $invoice_returns = $invoice_returns->map(function ($item) {
                $item->link = route('admin.invoice-return.show', $item->id);
                $item->menu = 'invoice return';
                return $item;
            });

            $receivables_payments = $receivables_payments->map(function ($item) {
                $item->link = route('admin.receivables-payment.show', $item->id);
                $item->menu = 'receivables payment';
                return $item;
            });

            $histories = $sale_order_generals->unique('id')
                ->merge($delivery_order_generals->unique('id'))
                ->merge($invoice_generals->unique('id'))
                ->merge($invoice_returns->unique('id'))
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

    public function generate_delivery_journal()
    {
        DB::beginTransaction();
        try {
            $journals = DB::table('journal_details')
                ->join('journals', function ($q) {
                    $q->on('journals.id', '=', 'journal_details.journal_id')
                        ->where('journals.reference_model', 'App\Models\DeliveryOrderGeneral');
                })
                ->whereNull('journals.deleted_at')
                ->selectRaw('
                    count(journal_details.id) as count,
                    journals.id as journal_id,
                    journals.date as journal_date,
                    journals.code as journal_code,
                    journal_details.coa_id,
                    journal_details.debit as credit,
                    journal_details.credit as debit
                ')
                ->groupBy('journal_details.coa_id', 'journal_details.credit', 'journal_details.remark', 'journals.id')
                ->havingRaw('count > 1')
                ->get();


            return response()->json($journals);

            DB::commit();

            return response()->json($journals);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json($th->getMessage());
        }
    }
}
