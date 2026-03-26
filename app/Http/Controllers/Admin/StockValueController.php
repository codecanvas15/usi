<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\WareHouse;
use Illuminate\Http\Request;
use App\Models\StockMutation;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class StockValueController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view stock-card-value', ['only' => ['index']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'stock-value';


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = StockMutation::with(['item.unit'])
                ->join('items', 'items.id', '=', 'stock_mutations.item_id')
                ->join('units', 'units.id', '=', 'items.unit_id')
                ->select(['stock_mutations.*', 'items.nama as item_name', 'units.name as unit_name'])
                ->when($request->warehouse_id && $request->warehouse_id != 'null', function ($q) use ($request) {
                    $q->where('stock_mutations.ware_house_id', $request->warehouse_id);
                })
                ->when($request->item_id && $request->item_id != 'null', function ($q) use ($request) {
                    $q->where('stock_mutations.item_id', $request->item_id);
                })
                ->when($request->from_date && $request->from_date != 'null', function ($q) use ($request) {
                    $q->whereDate('stock_mutations.date', '>=', Carbon::parse($request->from_date));
                })
                ->when($request->to_date && $request->to_date != 'null', function ($q) use ($request) {
                    $q->whereDate('stock_mutations.date', '<=', Carbon::parse($request->to_date));
                })
                ->orderBy('items.nama', 'asc')
                ->orderBy('stock_mutations.date', 'asc')
                ->orderBy('stock_mutations.ordering', 'asc');

            $stockMutations = StockMutation::with(['item.unit'])
                ->join('items', 'items.id', '=', 'stock_mutations.item_id')
                ->select(['stock_mutations.*', 'items.nama as item_name'])
                ->when($request->warehouse_id && $request->warehouse_id != 'null', function ($q) use ($request) {
                    $q->where('stock_mutations.ware_house_id', $request->warehouse_id);
                })
                ->when($request->item_id && $request->item_id != 'null', function ($q) use ($request) {
                    $q->where('stock_mutations.item_id', $request->item_id);
                })
                ->when($request->from_date && $request->from_date != 'null', function ($q) use ($request) {
                    $q->whereDate('stock_mutations.date', '>=', Carbon::parse($request->from_date));
                })
                ->when($request->to_date && $request->to_date != 'null', function ($q) use ($request) {
                    $q->whereDate('stock_mutations.date', '<=', Carbon::parse($request->to_date));
                })
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->filterColumn('item_code', function ($q, $search) {
                    $q->where('items.kode', 'like', "%{$search}%");
                })
                ->addColumn('item_code', function ($row) {
                    return $row->item->kode;
                })
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)->format('d/m/Y');
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d/m/Y');
                })
                ->editColumn('nama', function ($row) {
                    return $row->item->nama;
                })
                ->editColumn('stock_before', function ($row) use ($stockMutations) {
                    $sum_in = $stockMutations
                        ->where('item_id', $row->item_id)
                        ->where('ordering', '<', $row->ordering);

                    $sum_in = $sum_in->sum('in');

                    $sum_out = $stockMutations
                        ->where('item_id', $row->item_id)
                        ->where('ordering', '<', $row->ordering);

                    $sum_out = $sum_out->sum('out');

                    return formatNumber(($sum_in - $sum_out));
                })
                ->editColumn('in', function ($row) {
                    if ($row->in) {
                        return formatNumber($row->in);
                    } else {
                        return;
                    }
                })
                ->editColumn('out', function ($row) {
                    if ($row->out) {
                        return formatNumber($row->out);
                    } else {
                        return;
                    }
                })
                ->editColumn('left', function ($row) use ($stockMutations) {
                    $sum_in = $stockMutations
                        ->where('item_id', $row->item_id)
                        ->where('ordering', '<=', $row->ordering);

                    $sum_in = $sum_in->sum('in');

                    $sum_out = $stockMutations
                        ->where('item_id', $row->item_id)
                        ->where('ordering', '<=', $row->ordering);

                    $sum_out = $sum_out->sum('out');

                    return formatNumber(($sum_in - $sum_out));
                })
                ->editColumn('price_unit', fn ($row) => formatNumber($row->price_unit))
                ->editColumn('subtotal', fn ($row) => formatNumber($row->subtotal))
                ->editColumn('total', fn ($row) => formatNumber($row->total))
                ->editColumn('value', fn ($row) => formatNumber($row->value))
                ->rawColumns(['action'])
                ->make(true);
        }

        $warehouse = WareHouse::first();

        return view('admin.' . $this->view_folder . '.index', compact('warehouse'));
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
    public function show($id)
    {
        //
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
