<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClosingDeliveryOrderShip;
use App\Models\DeliveryOrder;
use App\Models\Journal;
use App\Models\SoTrading;
use App\Models\StockMutation;
use App\Models\WareHouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ClosingDeliveryOrderShipController extends Controller
{
    /**
     * The title associated with the controller.
     */
    protected $title;

    /**
     * The route name prefix associated with the controller.
     */
    protected $routeNamePrefix;

    /**
     * The view name prefix associated with the controller.
     */
    protected $viewNamePrefix;

    /**
     * The permission name associated with the controller.
     */
    protected $permissionName;

    /**
     * Instantiate a new Controllers instance.
     */
    public function __construct()
    {
        $this->title = 'Closing Delivery Order Ship';
        $this->routeNamePrefix = 'admin.closing-delivery-order-ship';
        $this->viewNamePrefix = 'admin.closing-delivery-order-ship';
        $this->permissionName = 'closing-delivery-order-ship';

        $this->middleware("permission:view $this->permissionName", ['only' => ['index', 'show']]);
        $this->middleware("permission:create $this->permissionName", ['only' => ['create', 'store']]);
        $this->middleware("permission:void $this->permissionName", ['only' => ['void']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = \App\Models\ClosingDeliveryOrderShip::select('closing_delivery_order_ships.*')
                ->with(['deliveryOrder', 'item'])
                ->when($request->branch_id && get_current_branch()->is_primary, function ($query) use ($request) {
                    $query->where('branch_id', $request->branch_id);
                })
                ->when(!get_current_branch()->is_primary, function ($query) {
                    $query->where('branch_id', get_current_branch()->id);
                })
                ->when($request->status, function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->when($request->from_date, function ($query) use ($request) {
                    $query->whereDate('date', '>=', Carbon::parse($request->from_date));
                })
                ->when($request->to_date, function ($query) use ($request) {
                    $query->whereDate('date', '<=', Carbon::parse($request->to_date));
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', fn($row) => \Carbon\Carbon::parse($row->date)->format('d-m-Y'))
                ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => "closing-delivery-order-ship",
                    'permission_name' => $this->permissionName,

                ]))
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . closing_delivery_order_ship()[$row->status]['color'] . '">
                                            ' . closing_delivery_order_ship()[$row->status]['label'] . ' - ' . closing_delivery_order_ship()[$row->status]['text'] . '
                                        </div>';
                    return $badge;
                })
                ->rawColumns(['status', 'code',])
                ->make(true);
        }

        return view("$this->viewNamePrefix.index", [
            'title' => $this->title,
            'routeNamePrefix' => $this->routeNamePrefix,
            'permissionName' => $this->permissionName,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("$this->routeNamePrefix.create", [
            'title' => $this->title,
            'routeNamePrefix' => $this->routeNamePrefix,
            'permissionName' => $this->permissionName,
        ]);
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
            'delivery_order_id' => 'required|exists:delivery_orders,id',
            'losses_id' => 'required|exists:coas,id',
        ]);

        // * find delivery order
        $deliveryOrder = DeliveryOrder::find($request->delivery_order_id);
        if (!is_null($deliveryOrder->delivery_order)) {
            return redirect(route("$this->routeNamePrefix.create"))->with($this->ResponseMessageCRUD(false, 'create', 'Delivery Order not valid.'));
        }

        if ($deliveryOrder->type != 'delivery-order-2') {
            return redirect(route("$this->routeNamePrefix.create"))->with($this->ResponseMessageCRUD(false, 'create', 'Delivery Order not valid.'));
        }

        // * find sale order and item
        $saleOrder = SoTrading::with(['so_trading_detail.item'])->find($deliveryOrder->so_trading_id);
        $item = $saleOrder->so_trading_detail->item;
        if (!$saleOrder) {
            return redirect(route("$this->routeNamePrefix.create"))->with($this->ResponseMessageCRUD(false, 'create', 'Sales Order not found.'));
        }

        // * find stock mutation
        $price_unit = $deliveryOrder->hpp;
        $price_unit = $price_unit;

        // * find warehouse
        $warehouse = WareHouse::find($deliveryOrder->ware_house_id);
        if (!$warehouse) {
            return redirect(route("$this->routeNamePrefix.create"))->with($this->ResponseMessageCRUD(false, 'create', 'Warehouse not found.'));
        }

        $losesQuantity = $deliveryOrder->load_quantity_realization - $deliveryOrder->unload_quantity_realization;
        $debit = $losesQuantity * $price_unit;
        $credit = $losesQuantity * $price_unit;

        // * create closing delivery order ship
        $closingDeliveryOrderShip = new \App\Models\ClosingDeliveryOrderShip();
        $closingDeliveryOrderShip->fill([
            'branch_id' => $deliveryOrder->branch_id,
            'delivery_order_id' => $deliveryOrder->id,
            'losses_coa_id' => $request->losses_id,
            'item_id' => $item->id,
            'date' => Carbon::parse($request->date),
            'note' => $request->note,
            'losses_quantity' => $losesQuantity,
            'amount_sent' => $deliveryOrder->load_quantity_realization * $price_unit,
            'amount_losses' => ($deliveryOrder->load_quantity_realization - $deliveryOrder->unload_quantity_realization) * $price_unit
        ]);

        // * save closing delivery order ship
        try {
            $closingDeliveryOrderShip->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th);

            return redirect(route("$this->routeNamePrefix.create"))->with($this->ResponseMessageCRUD(false, 'create', 'Failed to create closing delivery order ship.'));
        }

        DB::beginTransaction();

        // * create journal
        $journal = new Journal();
        $journal->fill([
            'branch_id' => $deliveryOrder->branch_id,
            'reference_id' => $closingDeliveryOrderShip->id,
            'reference_model' => \App\Models\ClosingDeliveryOrderShip::class,
            'customer_id' => $saleOrder->customer_id,
            'reference_number' => $closingDeliveryOrderShip->code,
            'document_reference' => [
                'id' => $closingDeliveryOrderShip->id,
                'model' => ClosingDeliveryOrderShip::class,
                'code' => $closingDeliveryOrderShip->code,
                'link' => route('admin.closing-delivery-order-ship.show', $closingDeliveryOrderShip->id),
            ],
            'reference' => [
                'id' => $closingDeliveryOrderShip->id,
                'model' => ClosingDeliveryOrderShip::class,
                'code' => $closingDeliveryOrderShip->code,
                'link' => route('admin.closing-delivery-order-ship.show', $closingDeliveryOrderShip->id),
            ],
            'date' => $closingDeliveryOrderShip->date,
            'exchange_rate' => 1,
            'currency_id' => get_local_currency()->id,
            'journal_type' => "Closing Delivery Order",
            'remark' => "Closing Delivery order {$deliveryOrder->code}",
            'status' => 'approve',
            'is_generated' => true,
        ]);

        // * save journal
        try {
            $journal->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th);

            return redirect(route("$this->routeNamePrefix.create"))->with($this->ResponseMessageCRUD(false, 'create', 'Failed to create journal.'));
        }

        // * find item category inventory
        $inventory = DB::table('item_category_coas')
            ->where('item_category_id', $item->item_category_id)
            ->whereRaw('LOWER(type) = ?', ['inventory'])
            ->first();

        if (!$inventory) {
            DB::rollBack();

            return redirect(route("$this->routeNamePrefix.create"))->with($this->ResponseMessageCRUD(false, 'create', 'Coa Inventory not found.'));
        }

        // * create list of journal details
        $journalDetails = [];

        // * credit inventory
        $journalDetails[] = [
            'reference_id' => $saleOrder->so_trading_detail->id,
            'reference_model' => \App\Models\SoTradingDetail::class,
            'coa_id' => $inventory->coa_id,
            'credit' => $credit,
            'debit' => 0,
            'remark' => "$item->kode - $item->nama"
        ];

        // * debit losses
        $journalDetails[] = [
            'reference_id' => $saleOrder->so_trading_detail->id,
            'reference_model' => \App\Models\SoTradingDetail::class,
            'coa_id' => $request->losses_id,
            'credit' => 0,
            'debit' => $debit,
            'remark' => "$item->kode - $item->nama"
        ];

        // * saving journal details
        try {
            $journal->journal_details()->createMany($journalDetails);
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th);

            return redirect(route("$this->routeNamePrefix.create"))->with($this->ResponseMessageCRUD(false, 'create', 'Failed to create journal details.'));
        }

        // * update journal debit and credit
        $journal->fresh();
        $journal->fill([
            'debit_total' => $journal->journal_details()->sum('debit'),
            'credit_total' => $journal->journal_details()->sum('credit'),
        ]);

        // * save journal
        try {
            $journal->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th);

            return redirect(route("$this->routeNamePrefix.create"))->with($this->ResponseMessageCRUD(false, 'create', 'Failed to update journal.'));
        }

        // * creating stock mutation
        $stockMutation = new StockMutation();
        $stockMutation->fill([
            'ware_house_id' => $deliveryOrder->ware_house_id,
            'branch_id' => $warehouse->branch_id ?? get_current_branch_id(),
            'item_id' => $item->id,
            'price_id' => $stockMutation->price_id,
            'document_model' => \App\Models\ClosingDeliveryOrderShip::class,
            'document_id' => $closingDeliveryOrderShip->id,
            'document_code' => "{$closingDeliveryOrderShip->code} - {$deliveryOrder->code}",
            'date' => $closingDeliveryOrderShip->date,
            'vendor_model' => Vendor::class,
            'type' => 'delivery order trading losses',
            'out' => $losesQuantity,
            'note' => "Delivery order trading losses",
        ]);

        try {
            $stockMutation->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th);

            return redirect(route("$this->routeNamePrefix.create"))->with($this->ResponseMessageCRUD(false, 'create', 'Failed to create stock mutation.'));
        }

        DB::commit();

        return redirect(route("$this->routeNamePrefix.index"))->with($this->ResponseMessageCRUD(true, 'create'));
    }

    /**
     * Show the detail the specified resource.
     */
    public function show($id)
    {
        $model = ClosingDeliveryOrderShip::with(['deliveryOrder', 'lossesCoa', 'item'])->findOrFail($id);

        return view("$this->viewNamePrefix.show", [
            'title' => $this->title,
            'routeNamePrefix' => $this->routeNamePrefix,
            'permissionName' => $this->permissionName,
            'model' => $model,
        ]);
    }

    /**
     * Void the specified resource.
     */
    public function void($id)
    {
        $model = ClosingDeliveryOrderShip::findOrFail($id);

        DB::beginTransaction();

        // * find journal
        $journal = Journal::where('reference_id', $model->id)
            ->where('reference_model', \App\Models\ClosingDeliveryOrderShip::class)
            ->first();

        // * if journal not found
        if (!$journal) {
            DB::rollBack();

            return redirect(route("$this->routeNamePrefix.show", $model))->with($this->ResponseMessageCRUD(false, 'void', 'Journal not found.'));
        }

        // * void journal
        try {
            $journal->delete();
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th);

            return redirect(route("$this->routeNamePrefix.show", $model))->with($this->ResponseMessageCRUD(false, 'void', 'Failed to void journal.'));
        }

        // * void closing delivery order ship
        $model->fill([
            'status' => 'void',
        ]);

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th);

            return redirect(route("$this->routeNamePrefix.show", $model))->with($this->ResponseMessageCRUD(false, 'void', 'Failed to void closing delivery order ship.'));
        }

        DB::commit();

        return redirect(route("$this->routeNamePrefix.index"))->with($this->ResponseMessageCRUD(true, 'void'));
    }

    /**
     * Select delivery order
     */
    public function selectDeliveryOrder(Request $request)
    {
        $closingDeliveryOrders = DB::table('closing_delivery_order_ships')
            ->where('status', '!=', 'void')
            ->get();

        $valueIds = [];
        $closingDeliveryOrders
            ->pluck('delivery_order_id')
            ->map(function ($item) use (&$valueIds) {
                $valueIds[] = $item;
            });

        return $this->ResponseJsonData(\App\Models\DeliveryOrder::whereNull('delivery_order_id')
            ->where('type', 'delivery-order-2')
            ->where('status', 'done')
            ->whereNotIn('id', $valueIds)
            ->when($request->search, function ($query, $search) {
                $query->where('code', 'like', "%$search%")
                    ->orWhereDate('target_delivery', 'like', "%$search%");
            })
            ->orderByDesc('created_at')
            ->limit(10)
            ->select(['id', 'code', 'target_delivery'])
            ->get());
    }

    /**
     * detail delivery order
     */
    public function detailDeliveryOrder($id)
    {
        // * find delivery order
        $deliveryOrder = DeliveryOrder::with('branch')->find($id);
        if (!is_null($deliveryOrder->delivery_order)) {
            return $this->ResponseJsonMessageCRUD(false, 'create', 'Delivery Order not valid.');
        }

        if ($deliveryOrder->type != 'delivery-order-2') {
            return $this->ResponseJsonMessageCRUD(false, 'create', 'Delivery Order not valid.');
        }

        // * find sale order and item
        $saleOrder = SoTrading::with(['so_trading_detail.item'])->find($deliveryOrder->so_trading_id);
        $item = $saleOrder->so_trading_detail->item;
        if (!$saleOrder) {
            return redirect(route("$this->routeNamePrefix.create"))->with($this->ResponseMessageCRUD(false, 'create', 'Sales Order not found.'));
        }

        // * find stock mutation
        $stockMutation = StockMutation::where('item_id', $item->id)->first();
        if (!$stockMutation) {
            return redirect(route("$this->routeNamePrefix.create"))->with($this->ResponseMessageCRUD(false, 'create', 'Stock Mutation not found.'));
        }

        return $this->ResponseJsonData([
            'branch' => $deliveryOrder->branch->name,
            'code' => $deliveryOrder->code,
            'target_delivery' => localDate($deliveryOrder->target_delivery),
            'unload_date' => $deliveryOrder->unload_date ? localDate($deliveryOrder->unload_date) : '',
            'item_code' => $item->kode,
            'item_name' => $item->nama,
            'load_quantity_realization' => $deliveryOrder->load_quantity_realization,
            'unload_quantity_realization' => $deliveryOrder->unload_quantity_realization,
            'losses_quantity' => $deliveryOrder->load_quantity_realization - $deliveryOrder->unload_quantity_realization,
            'amount_sent' => $deliveryOrder->load_quantity_realization * $stockMutation->price_unit,
            'amount_losses' => ($deliveryOrder->load_quantity_realization - $deliveryOrder->unload_quantity_realization) * $stockMutation->price_unit,
        ]);
    }
}
