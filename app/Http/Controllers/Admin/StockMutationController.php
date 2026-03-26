<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\WareHouse;
use Illuminate\Http\Request;
use App\Models\StockMutation;
use App\Http\Controllers\Controller;
use App\Jobs\DailyRefreshStockJob;
use App\Jobs\RefreshStock;
use App\Jobs\WeeklyRefreshStockJob;
use App\Models\ClosingPeriod;
use App\Models\RefreshStockLog;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockMutationController extends Controller
{
    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'stock-mutation';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = StockMutation::with(['item.unit'])
                ->join('items', 'items.id', 'stock_mutations.item_id')
                ->join('units', 'units.id', 'items.unit_id')
                ->select('stock_mutations.*', 'units.name as unit_name')
                ->when($request->item_id, function ($q) use ($request) {
                    $q->where('stock_mutations.item_id', $request->item_id);
                })
                ->when($request->warehouse_id, function ($q) use ($request) {
                    $q->where('stock_mutations.ware_house_id', $request->warehouse_id);
                })
                ->when($request->from_date, function ($q) use ($request) {
                    $q->whereDate('stock_mutations.date', '>=', Carbon::parse(($request->from_date))->format('Y-m-d'));
                })
                ->when($request->to_date, function ($q) use ($request) {
                    $q->whereDate('stock_mutations.date', '<=', Carbon::parse(($request->to_date))->format('Y-m-d'));
                })
                ->orderBy('stock_mutations.date', 'asc')
                ->orderBy('stock_mutations.ordering', 'asc');

            $stockMutations = StockMutation::with(['item.unit'])
                ->join('items', 'items.id', 'stock_mutations.item_id')
                ->select('stock_mutations.*')
                ->when($request->item_id, function ($q) use ($request) {
                    $q->where('stock_mutations.item_id', $request->item_id);
                })
                ->when($request->warehouse_id, function ($q) use ($request) {
                    $q->where('stock_mutations.ware_house_id', $request->warehouse_id);
                })
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'btn_config' => [
                            'detail' => [
                                'display' => true,
                            ],
                            'edit' => [
                                'display' => false,
                            ],
                            'delete' => [
                                'display' => false,
                            ],
                        ],
                    ]);
                })
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)->format('d/m/Y');
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d/m/Y');
                })
                ->editColumn('kode', fn($row) => $row->item->kode)
                ->editColumn('nama', function ($row) {
                    return $row->item->nama;
                })
                ->editColumn('stock_before', function ($row) use ($stockMutations, $request) {
                    $sum_in_before = $stockMutations->where('item_id', $row->item_id)
                        ->filter(function ($value, $key) use ($row, $request) {
                            if ($request->warehouse_id) {
                                return $value->ware_house_id == $row->ware_house_id;
                            }
                            return $value;
                        })
                        ->where('ordering', '<', $row->ordering);

                    $sum_in_before = $sum_in_before->sum('in');

                    $sum_out_before = $stockMutations->where('item_id', $row->item_id)
                        ->filter(function ($value, $key) use ($row, $request) {
                            if ($request->warehouse_id) {
                                return $value->ware_house_id == $row->ware_house_id;
                            }
                            return $value;
                        })
                        ->where('ordering', '<', $row->ordering);

                    $sum_out_before = $sum_out_before->sum('out');

                    return checkNumber(($sum_in_before - $sum_out_before));
                })
                ->editColumn('in', function ($row) {
                    return checkNumber($row->in);
                })
                ->editColumn('out', function ($row) {
                    return checkNumber($row->out);
                })
                ->editColumn('left', function ($row) use ($stockMutations, $request) {
                    $sum_in_before = $stockMutations->where('item_id', $row->item_id)
                        ->filter(function ($value, $key) use ($row, $request) {
                            if ($request->warehouse_id) {
                                return $value->ware_house_id == $row->ware_house_id;
                            }
                            return $value;
                        })
                        ->where('ordering', '<=', $row->ordering);

                    $sum_in_before = $sum_in_before->sum('in');

                    $sum_out_before = $stockMutations->where('item_id', $row->item_id)
                        ->filter(function ($value, $key) use ($row, $request) {
                            if ($request->warehouse_id) {
                                return $value->ware_house_id == $row->ware_house_id;
                            }
                            return $value;
                        })
                        ->where('ordering', '<=', $row->ordering);

                    $sum_out_before = $sum_out_before->sum('out');

                    return checkNumber(($sum_in_before - $sum_out_before));
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $warehouse = WareHouse::first();
        $refresh_log = RefreshStockLog::orderBy('id', 'desc')->first();

        return view('admin.' . $this->view_folder . '.index', compact('warehouse', 'refresh_log'));
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

    public function refresh(Request $request)
    {
        $period = null;
        if ($request->period) {
            $period = Carbon::parse('01-' . $request->period);

            $check_closing_period = ClosingPeriod::whereDate('to_date', '>=', Carbon::parse($period))
                ->where('status', 'close')
                ->first();

            if ($check_closing_period) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'Periode yang anda pilih sudah ditutup'));
            }
        }

        try {

            DB::table('stock_mutations')
                ->whereMonth('date', $period->toDateString())
                ->whereYear('date', $period)
                ->update([
                    'ordering' => null,
                    'available_qty' => DB::raw('`in`')
                ]);

            $stockMutations = DB::table('stock_mutations')
                ->whereMonth('date', $period)
                ->whereYear('date', $period)
                ->selectRaw('stock_mutations.*,
                CASE 
                WHEN stock_mutations.type IN ("supplier invoice") THEN 1
                ELSE 0
                END as priority
                ')
                ->orderBy('date', 'asc')
                ->orderBy('priority', 'desc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($stockMutations as $stockMutation) {
                DB::table('stock_mutations')
                    ->where('id', $stockMutation->id)
                    ->update(['ordering' => generate_stock_mutation_order($stockMutation->date)]);
            }

            DailyRefreshStockJob::dispatch($period, auth()->user()->id, true);

            return redirect()->back()->with($this->ResponseMessageCRUD(true, 'create'));
        } catch (\Throwable $th) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', $th->getMessage()));
        }
    }

    public function weekly_refresh(Request $request)
    {
        try {
            WeeklyRefreshStockJob::dispatch();
            return redirect()->back()->with($this->ResponseMessageCRUD(true, 'create'));
        } catch (\Throwable $th) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', $th->getMessage()));
        }
    }

    public function refresh_stock_log()
    {
        $data = RefreshStockLog::leftJoin('users', 'users.id', 'refresh_stock_logs.user_id')
            ->select('refresh_stock_logs.*', 'users.name as user_name');

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('status', function ($row) {
                return $row->status == 'success' ? '<span class="badge badge-success">Berhasil</span>' : '<span class="badge badge-danger">Gagal</span>';
            })
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('j F Y H:i:s');
            })
            ->editColumn('user_name', function ($row) {
                return $row->user_name ?? 'Otomatis by Sistem';
            })
            ->editColumn('period', function ($row) {
                return Carbon::parse($row->period)->format('F Y');
            })
            ->escapeColumns([])
            ->make(true);
    }
}
