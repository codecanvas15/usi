<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\WareHouse;
use Illuminate\Http\Request;
use App\Models\StockMutation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class StockCardController extends Controller
{
    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'stock-card';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $branch_id = Auth::user()->branch_id ?? Auth::user()->temp_branch_id ?? $request->branch_id;
            $data = Item::with('unit')
                ->orderByDesc('items.created_at')
                ->join('units', 'units.id', 'items.unit_id')
                ->leftJoin('item_minimums', function ($i) use ($branch_id) {
                    $i->on('item_minimums.item_id', 'items.id')
                        ->where('item_minimums.branch_id', $branch_id);
                })
                ->when($request->item_id && $request->item_id != 'null', function ($q) use ($request) {
                    $q->where('items.id', $request->item_id);
                })
                // ->when(!get_current_branch()->is_primary, fn($q) => $q->where('items.branch_id', get_current_branch_id()))
                ->when($request->type, function ($q) use ($request) {
                    $q->where('items.type', $request->type);
                })
                ->when($request->from_date, function ($q) use ($request) {
                    $q->where('items.created_at', '>=', Carbon::parse($request->from_date));
                })
                ->when($request->to_date, function ($q) use ($request) {
                    $q->where('items.created_at', '<=', Carbon::parse($request->to_date));
                })
                ->select('items.*');

            $stockMutations = StockMutation::with(['item.unit'])
                ->join('items', 'items.id', 'stock_mutations.item_id')
                ->orderBy('stock_mutations.created_at')
                ->select('stock_mutations.*')
                ->when($request->item_id && $request->item_id != 'null', function ($q) use ($request) {
                    $q->where('stock_mutations.item_id', $request->item_id);
                })
                ->when($request->warehouse_id && $request->warehouse_id != 'null', function ($q) use ($request) {
                    $q->where('stock_mutations.ware_house_id', $request->warehouse_id);
                })
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->filterColumn('kode', function ($q, $search) {
                    $q->where('items.kode', 'like', "%{$search}%");
                })
                ->editColumn('kode', function ($row) use ($request) {
                    $route = route('admin.stock-card.show', [
                        'id' => $row->id,
                        'warehouse_id' => $request->warehouse_id,
                    ]);
                    return "<a href='{$route}' class='text-primary text-decoration-underline hover_text-dark'>{$row->kode}</a>";
                })
                ->editColumn('minimum_stock', function ($row) {
                    return formatNumber($row->branch_min_stock);
                })
                ->editColumn('stock', function ($row) use ($request, $stockMutations) {
                    $in = $stockMutations
                        ->where('item_id', $row->id)
                        ->when($request->warehouse_id && $request->warehouse_id != 'null', fn ($q) => $q->where('ware_house_id', $request->warehouse_id))
                        ->whereNull('is_return');

                    return formatNumber($in->sum('in') - $in->sum('out'));
                })
                ->editColumn('unit', function ($row) use ($request) {
                    $query = Item::join('units', 'units.id', 'items.unit_id')->where('items.id', $row->id)->select('units.name')->first();

                    return $query->name;
                })
                ->rawColumns(['action', 'kode'])
                ->make(true);
        }

        $general_warehouse = WareHouse::where('type', 'general')->first();
        $trading_warehouse = WareHouse::where('type', 'trading')->first();

        return view('admin.' . $this->view_folder . '.index', compact('general_warehouse', 'trading_warehouse'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id, $warehouse_id)
    {
        $warehouse_id = $warehouse_id != 'null' ? $warehouse_id : null;

        if ($request->ajax()) {
            $data = StockMutation::with(['item'])
                ->where('item_id', $id)
                ->when($warehouse_id, fn($q) => $q->where('ware_house_id', $warehouse_id))
                ->orderBy('ordering', 'desc');

            $stockMutations = StockMutation::with(['item'])
                ->where('item_id', $id)
                ->when($warehouse_id, fn($q) => $q->where('ware_house_id', $warehouse_id))
                ->orderBy('ordering', 'asc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()

                ->editColumn(
                    'date',
                    fn($row) =>
                    Carbon::parse($row->date)->format('d/m/Y')
                )

                ->editColumn('document', function ($row) {
                    if (!$row->document_id || !$row->document_model) {
                        return '-';
                    }

                    $modelClass = $row->document_model;

                    if (!class_exists($modelClass)) {
                        return '-';
                    }

                    $doc = $modelClass::find($row->document_id);
                    if (!$doc) {
                        return '-';
                    }

                    $code = $doc->kode ?? $doc->code ?? '-';

                    $link = match ($modelClass) {
                        \App\Models\DeliveryOrder::class =>
                        route('admin.delivery-order.show', $doc->id),

                        \App\Models\InvoiceTrading::class =>
                        route('admin.invoice-trading.show', $doc->id),

                        default => null,
                    };

                    return $link
                        ? "<a href='{$link}' target='_blank'
                        class='text-primary text-decoration-underline hover_text-dark'>
                        {$code}
                      </a>"
                        : $code;
                })

                ->editColumn(
                    'vendor',
                    fn($row) =>
                    $row->vendor_id ? $row->vendor->nama : '-'
                )

                ->editColumn('stock_before', function ($row) use ($stockMutations, $warehouse_id) {
                    $sumIn = $stockMutations
                        ->where('ordering', '<', $row->ordering)
                        ->sum('in');

                    $sumOut = $stockMutations
                        ->where('ordering', '<', $row->ordering)
                        ->sum('out');

                    return checkNumber($sumIn - $sumOut);
                })

                ->editColumn('in', fn($row) => checkNumber($row->in))
                ->editColumn('out', fn($row) => checkNumber($row->out))

                ->editColumn('left', function ($row) use ($stockMutations) {
                    $sumIn = $stockMutations
                        ->where('ordering', '<=', $row->ordering)
                        ->sum('in');

                    $sumOut = $stockMutations
                        ->where('ordering', '<=', $row->ordering)
                        ->sum('out');

                    return checkNumber($sumIn - $sumOut);
                })

                ->rawColumns(['document'])
                ->make(true);
        }

        return view('admin.' . $this->view_folder . '.show', [
            'item' => Item::find($id),
            'warehouse' => WareHouse::find($warehouse_id),
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
}
