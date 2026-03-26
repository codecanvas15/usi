<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\DownloadQueue;
use App\Models\Download;
use App\Models\Item;
use App\Models\WareHouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryReportController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'inventory-report';

    /**
     * where the route will be defined
     *
     * @var string
     */
    protected string $route = 'inventory-report';

    /**
     * Display page from selecting report type or format
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("admin.$this->view_folder.index");
    }

    /**
     * Display report in some of type view
     *
     * @param string $type
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(string $type, Request $request)
    {
        $data = [];

        $file_path = "admin.$this->view_folder.$type." . $request->format;

        switch ($type) {
            case "stock-card-report":
                if ($request->format == "preview") {
                    $data = $this->reportStockCard($request);
                }
                $orientation = 'landscape';
                $paper_size = 'a3';
                break;
            case "stock-mutation-report":
                if ($request->format == "preview") {
                    $data = $this->reportStockMutation($request);
                }
                $orientation = 'landscape';
                $paper_size = 'a3';
                break;
            case "end-of-monthly-stock-report":
                $this->validate($request, [
                    'month' => 'required',
                ]);
                if ($request->format == "preview") {
                    $data = $this->reportStockEndOfMonthlyStock($request);
                }
                $orientation = 'landscape';
                $paper_size = 'a3';
                break;
            case "stock-value":
                if ($request->format == "preview") {
                    $data = $this->stockValueReport($request);
                }
                $orientation = 'landscape';
                $paper_size = 'a3';
                break;
            default:
                return redirect()->route("admin.$this->route.index")->with($this->ResponseMessageCRUD(false, "report", "selected report type was not found"));
        }

        if ($request->format == 'preview') {
            return view($file_path, $data);
        } else {
            try {
                $download = Download::create([
                    'user_id' => auth()->user()->id,
                    'path' => '',
                    'status' => 'pending',
                    'type' => $type,
                ]);

                $request_params = $request->all();
                $request_params['type'] = $type;
                $request_params['from_date'] = $request->from_date;
                $request_params['to_date'] = $request->to_date;

                DownloadQueue::dispatch($request_params, $file_path, $paper_size, $orientation, $download->id);

                return redirect()->route('admin.download-report.index');
            } catch (\Throwable $th) {
                DB::rollBack();

                throw $th;
                return redirect()->route("admin.$this->route.index")->with($this->ResponseMessageCRUD(false, "report", $th->getMessage()));
            }
        }
    }

    /**
     * Generate report for stock card
     *
     * @param $request
     * @return array
     */
    public function reportStockCard($request): array
    {
        $model = DB::table('stock_mutations')
            ->leftJoin('items', 'items.id', '=', 'stock_mutations.item_id')
            ->leftJoin('units', 'units.id', '=', 'items.unit_id')
            ->leftJoin('branches', 'branches.id', '=', 'stock_mutations.branch_id')
            ->leftJoin('ware_houses', 'ware_houses.id', '=', 'stock_mutations.ware_house_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'stock_mutations.vendor_id')
            ->whereNull('stock_mutations.deleted_at')
            ->when(isset($request['from_date']), function ($query) use ($request) {
                return $query->whereDate('stock_mutations.date', '>=', Carbon::parse($request['from_date']));
            })
            ->when(isset($request['to_date']), function ($query) use ($request) {
                return $query->whereDate('stock_mutations.date', '<=', Carbon::parse($request['to_date']));
            })
            ->when(isset($request['ware_house_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.ware_house_id', $request['ware_house_id']);
            })
            ->when(isset($request['vendor']), function ($query) use ($request) {
                return $query->where('stock_mutations.vendor_id', $request['vendor']);
            })
            ->when(isset($request['branch_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.branch_id', $request['branch_id']);
            })
            ->when(isset($request['item_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.item_id', $request['item_id']);
            })
            ->selectRaw('
                stock_mutations.date,
                stock_mutations.created_at,
                items.id as item_id,
                items.nama as item_name,
                items.kode as item_code,
                vendors.nama as vendor_name,
                branches.name as branch_name,
                branches.id as branch_id,
                ware_houses.nama as ware_house_name,
                ware_houses.id as ware_house_id,
                stock_mutations.note,
                stock_mutations.document_code,
                units.name as unit_name,
                stock_mutations.document_code,
                stock_mutations.in,
                stock_mutations.out,
                stock_mutations.total,
                stock_mutations.ordering
            ')
            ->orderBy('stock_mutations.ordering', 'asc')
            ->get();

        $stockMutations = DB::table('stock_mutations')
            ->leftJoin('items', 'items.id', '=', 'stock_mutations.item_id')
            ->leftJoin('units', 'units.id', '=', 'items.unit_id')
            ->leftJoin('branches', 'branches.id', '=', 'stock_mutations.branch_id')
            ->leftJoin('ware_houses', 'ware_houses.id', '=', 'stock_mutations.ware_house_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'stock_mutations.vendor_id')
            ->whereNull('stock_mutations.deleted_at')
            // ->when(isset($request['from_date']), function ($query) use ($request) {
            //     return $query->whereDate('stock_mutations.date', '>=', $request['from_date']);
            // })
            // ->when(isset($request['to_date']), function ($query) use ($request) {
            //     return $query->whereDate('stock_mutations.date', '<=', $request['to_date']);
            // })
            ->when(isset($request['ware_house_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.ware_house_id', $request['ware_house_id']);
            })
            ->when(isset($request['vendor']), function ($query) use ($request) {
                return $query->where('stock_mutations.vendor_id', $request['vendor']);
            })
            ->when(isset($request['branch_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.branch_id', $request['branch_id']);
            })
            ->when(isset($request['item_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.item_id', $request['item_id']);
            })
            ->selectRaw('
                stock_mutations.date,
                stock_mutations.created_at,
                items.id as item_id,
                items.nama as item_name,
                items.kode as item_code,
                vendors.nama as vendor_name,
                branches.name as branch_name,
                branches.id as branch_id,
                ware_houses.nama as ware_house_name,
                ware_houses.id as ware_house_id,
                stock_mutations.note,
                stock_mutations.document_code,
                units.name as unit_name,
                stock_mutations.in,
                stock_mutations.out,
                stock_mutations.total,
                stock_mutations.ordering
            ')
            ->orderBy('stock_mutations.ordering', 'asc')
            ->get();

        $items = Item::whereHas('stock_mutations')
            ->join('units', 'units.id', '=', 'items.unit_id')
            ->select('items.*', 'units.name as unit_name')
            ->when(isset($request['item_id']), function ($query) use ($request) {
                return $query->where('items.id', $request['item_id']);
            })
            ->orderBy('nama', 'asc')
            ->get();

        $warhouses = WareHouse::with('branch')
            ->whereHas('stock_mutations')
            ->when(isset($request['ware_house_id']), function ($query) use ($request) {
                return $query->where('ware_houses.id', $request['ware_house_id']);
            })
            ->orderBy('nama', 'asc')->get();

        $results =  $warhouses->map(function ($ware_house) use ($model, $stockMutations, $items, $request) {
            $warehouse_data = $items->map(function ($item_data) use ($stockMutations, $ware_house, $model, $request) {
                $data = $model->where('item_id', $item_data->id)
                    ->where('ware_house_id', $ware_house->id);
                if ($data->count() > 0) {
                    $get_data = $data->map(function ($stock) use ($stockMutations) {
                        $data_before = $stockMutations
                            ->where('ware_house_id', $stock->ware_house_id)
                            ->where('item_id', $stock->item_id)
                            ->where('ordering', '<', $stock->ordering);

                        $stock_before = $data_before->sum('in') - $data_before->sum('out');

                        $stock_final = $stock_before + $stock->in - $stock->out;

                        return [
                            'date' => $stock->date,
                            'note' => $stock->note,
                            'document_code' => $stock->document_code ?? '-',
                            'stock_before' => $stock_before,
                            'in' => $stock->in ?? 0,
                            'out' => $stock->out ?? 0,
                            'stock_final' => $stock_final,
                            'created_at' => $stock->created_at,
                        ];
                    });
                } else {
                    $data_before = $stockMutations
                        ->where('ware_house_id', $ware_house->id)
                        ->where('item_id', $item_data->id)
                        ->filter(function ($stock) use ($request) {
                            return Carbon::parse($stock->date)->lt(Carbon::parse($request['from_date']));
                        });

                    $stock = $data_before->sum('in') - $data_before->sum('out');
                    $get_data =  collect([
                        0 => [
                            'date' => '',
                            'note' => 'SALDO',
                            'document_code' => '-',
                            'stock_before' => $stock,
                            'in' => 0,
                            'out' =>  0,
                            'stock_final' => $stock,
                            'created_at' => null,
                        ]
                    ]);
                }

                if ($get_data->count() > 0) {
                    return [
                        'item_id' => $item_data->id,
                        'item_name' => $item_data->nama,
                        'item_code' => $item_data->kode,
                        'unit_name' => $item_data->unit_name,
                        'data' => $get_data,
                        'sum_in' => $get_data->sum('in'),
                        'sum_out' => $get_data->sum('out'),
                        'sum_stock_final' => $get_data->sum('stock_final'),
                    ];
                }
            });

            if ($warehouse_data->count() > 0) {                    # code...
                return [
                    'ware_house_id' => $ware_house->id,
                    'ware_house_name' => $ware_house->nama,
                    'data' => $warehouse_data->filter(function ($data) {
                        return $data !== null;
                    }),
                ];
            }
        });


        $return_data = [
            'data' => $results,
            'from_date' => isset($request['from_date']),
            'to_date' => isset($request['to_date']),
            'type' => "stock-card-report",
        ];

        return $return_data;
    }

    /**
     * Generate report for stock mutation
     *
     * @param $request
     * @return array
     */
    public function reportStockMutation($request): array
    {
        $model = DB::table('stock_mutations')
            ->leftJoin('items', 'items.id', '=', 'stock_mutations.item_id')
            ->leftJoin('units', 'units.id', '=', 'items.unit_id')
            ->whereNull('stock_mutations.deleted_at')
            ->when(isset($request['from_date']), function ($query) use ($request) {
                return $query->whereDate('stock_mutations.date', '>=', Carbon::parse($request['from_date']));
            })
            ->when(isset($request['to_date']), function ($query) use ($request) {
                return $query->whereDate('stock_mutations.date', '<=', Carbon::parse($request['to_date']));
            })
            ->when(isset($request['ware_house_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.ware_house_id', $request['ware_house_id']);
            })
            ->when(isset($request['vendor']), function ($query) use ($request) {
                return $query->where('stock_mutations.vendor_id', $request['vendor']);
            })
            ->when(isset($request['branch_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.branch_id', $request['branch_id']);
            })
            ->when(isset($request['item_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.item_id', $request['item_id']);
            })
            ->selectRaw('
                stock_mutations.document_code,
                stock_mutations.date,
                stock_mutations.created_at,
                items.id as item_id,
                items.nama as item_name,
                items.kode as item_code,
                units.name as unit_name,
                stock_mutations.in,
                stock_mutations.out,
                stock_mutations.ordering
            ')
            ->orderBy('items.nama', 'asc')
            ->orderBy('stock_mutations.ordering', 'asc')
            ->get();

        $stockMutations = DB::table('stock_mutations')
            ->leftJoin('items', 'items.id', '=', 'stock_mutations.item_id')
            ->leftJoin('units', 'units.id', '=', 'items.unit_id')
            ->whereNull('stock_mutations.deleted_at')
            ->when(isset($request['ware_house_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.ware_house_id', $request['ware_house_id']);
            })
            ->when(isset($request['vendor']), function ($query) use ($request) {
                return $query->where('stock_mutations.vendor_id', $request['vendor']);
            })
            ->when(isset($request['branch_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.branch_id', $request['branch_id']);
            })
            ->when(isset($request['item_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.item_id', $request['item_id']);
            })
            ->selectRaw('
                stock_mutations.document_code,
                stock_mutations.date,
                stock_mutations.created_at,
                items.id as item_id,
                items.nama as item_name,
                items.kode as item_code,
                units.name as unit_name,
                stock_mutations.in,
                stock_mutations.out,
                stock_mutations.ordering
            ')
            ->orderBy('stock_mutations.ordering', 'asc')
            ->get();

        $model->map(function ($item) use ($model, $stockMutations) {
            $stock_before_in = $stockMutations->where('item_id', $item->item_id)
                ->where('ordering', '<', $item->ordering)
                ->sum('in');
            $stock_before_out = $stockMutations->where('item_id', $item->item_id)
                ->where('ordering', '<', $item->ordering)
                ->sum('out');

            $item->stock_before = ($stock_before_in - $stock_before_out);
            $item->stock_final = ($stock_before_in - $stock_before_out) + ($item->in - $item->out);
            return $item;
        });

        $get_unique_items = $model->unique('item_id')->flatten(1);

        $get_unique_items->map(function ($item) use ($model) {
            $item->data = $model->where('item_id', $item->item_id);
            return $item;
        });

        return [
            'data' => $get_unique_items,
            'from_date' => isset($request['from_date']),
            'to_date' => isset($request['to_date']),
            'type' => "stock-mutation-report",
        ];
    }

    /**
     * Generate report for end of monthly stock
     *
     * @param $request
     * @return array
     */
    public function reportStockEndOfMonthlyStock($request): array
    {
        $selected_month = \Carbon\Carbon::createFromFormat('m-Y', $request['month'])->format('m');
        $selected_year = \Carbon\Carbon::createFromFormat('m-Y', $request['month'])->format('Y');

        $items = DB::table('items')
            ->join('item_categories', 'item_categories.id', '=', 'items.item_category_id')
            ->join('item_types', 'item_types.id', '=', 'item_categories.item_type_id')
            ->when(isset($request['item_id']), function ($query) use ($request) {
                return $query->where('items.id', $request['item_id']);
            })
            ->when(isset($request['item_category_id']), function ($query) use ($request) {
                return $query->where('items.item_category_id', $request['item_category_id']);
            })
            ->where('item_types.nama', 'purchase item')
            ->join('units', 'units.id', '=', 'items.unit_id')
            ->select('items.*', 'item_categories.nama as category_name', 'units.name as unit_name')
            ->whereNull('items.deleted_at')
            ->orderBy('items.nama', 'asc')
            ->get();

        $data_current_month = DB::table('stock_mutations')
            ->leftJoin('items', 'items.id', '=', 'stock_mutations.item_id')
            ->leftJoin('units', 'units.id', '=', 'items.unit_id')
            ->leftJoin('item_categories', 'item_categories.id', '=', 'items.item_category_id')
            ->whereNull('stock_mutations.deleted_at')
            ->whereMonth('stock_mutations.date', $selected_month)
            ->whereYear('stock_mutations.date', $selected_year)
            ->when(isset($request['ware_house_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.ware_house_id', $request['ware_house_id']);
            })
            ->when(isset($request['vendor']), function ($query) use ($request) {
                return $query->where('stock_mutations.vendor_id', $request['vendor']);
            })
            ->when(isset($request['branch_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.branch_id', $request['branch_id']);
            })
            ->selectRaw('
                items.id as item_id,
                items.nama as item_name,
                items.kode as item_code,
                units.name as unit_name,
                item_categories.id as item_category_id,
                item_categories.nama as item_category_name,
                stock_mutations.in,
                stock_mutations.out,
                stock_mutations.price_unit,
                stock_mutations.subtotal,
                stock_mutations.value,
                stock_mutations.created_at,
                stock_mutations.created_at,
                stock_mutations.ordering
            ')
            ->get();

        $data_previous = DB::table('stock_mutations')
            ->leftJoin('items', 'items.id', '=', 'stock_mutations.item_id')
            ->leftJoin('units', 'units.id', '=', 'items.unit_id')
            ->leftJoin('item_categories', 'item_categories.id', '=', 'items.item_category_id')
            ->whereNull('stock_mutations.deleted_at')
            ->whereDate('stock_mutations.date', '<', \Carbon\Carbon::createFromFormat('m-Y', $request['month'])->startOfMonth())
            ->when(isset($request['ware_house_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.ware_house_id', $request['ware_house_id']);
            })
            ->when(isset($request['vendor']), function ($query) use ($request) {
                return $query->where('stock_mutations.vendor_id', $request['vendor']);
            })
            ->when(isset($request['branch_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.branch_id', $request['branch_id']);
            })
            ->when(isset($request['item_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.item_id', $request['item_id']);
            })
            ->selectRaw('
                items.id as item_id,
                items.nama as item_name,
                items.kode as item_code,
                units.name as unit_name,
                item_categories.id as item_category_id,
                item_categories.nama as item_category_name,
                stock_mutations.in,
                stock_mutations.out,
                stock_mutations.price_unit,
                stock_mutations.subtotal,
                stock_mutations.created_at,
                stock_mutations.ordering
            ')
            ->get();

        $results = $items->groupBy('item_category_id')->map(function ($item) use ($items, $data_current_month, $data_previous) {
            $category = new \stdClass();
            $category->item_category_id = $item->first()->item_category_id;
            $category->item_category_name = $item->first()->category_name;

            $data = $items->where('item_category_id', $item->first()->item_category_id)->map(function ($item_data) use ($data_current_month, $data_previous) {
                $result = new \stdClass();
                $result->item_id = $item_data->id;
                $result->item_name = $item_data->nama;
                $result->item_code = $item_data->kode;
                $result->item_category_name = $item_data->category_name;
                $result->unit_name = $item_data->unit_name;

                $stock_before_in = $data_previous->where('item_id', $item_data->id)
                    ->sum('in');
                $stock_before_out = $data_previous->where('item_id', $item_data->id)
                    ->sum('out');

                $value_before = $data_previous->where('item_id', $item_data->id)
                    ->filter(function ($data) {
                        return $data->in != null;
                    })
                    ->sum('subtotal') - $data_previous->where('item_id', $item_data->id)
                    ->filter(function ($data) {
                        return $data->out != null;
                    })
                    ->sum('subtotal');

                $result->stock_before = $stock_before_in - $stock_before_out;
                $result->value_before = $value_before;

                $result->stock_in = $data_current_month->where('item_id', $item_data->id)
                    ->sum('in');
                $result->value_in = $data_current_month->where('item_id', $item_data->id)
                    ->filter(function ($data) {
                        return $data->in != null;
                    })
                    ->sum('subtotal');

                $result->quantity = $result->stock_before + $result->stock_in;
                $result->value = $result->value_before + $result->value_in;

                $result->stock_out = $data_current_month->where('item_id', $item_data->id)
                    ->sum('out');
                $result->value_out = $data_current_month->where('item_id', $item_data->id)
                    ->filter(function ($data) {
                        return $data->out != null;
                    })
                    ->sum('subtotal');

                $result->stock_final = $result->stock_before + $result->stock_in - $result->stock_out;
                $result->value_final = $result->value_before + $result->value_in - $result->value_out;
                $result->price = $data_current_month
                    ->where('item_id', $item_data->id)
                    ->sortBy('ordering')->last()->value ?? $data_previous->where('item_id', $item_data->id)->sortBy('ordering')->last()->value ?? 0;

                return $result;
            });

            $category->data = $data;
            return $category;
        });

        return [
            'data' => $results,
            'period' => $request['month'] ?? '-',
            'type' => "end-of-monthly-stock-report",
        ];
    }

    /**
     * Stock value report
     */
    public function stockValueReport($request)
    {
        $model = DB::table('stock_mutations')
            ->whereNull('stock_mutations.deleted_at')
            ->leftJoin('items', 'items.id', 'stock_mutations.item_id')
            ->leftJoin('ware_houses', 'ware_houses.id', 'stock_mutations.ware_house_id')
            ->leftJoin('branches', 'branches.id', 'stock_mutations.branch_id')
            ->when(isset($request['item_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.item_id', $request['item_id']);
            })
            ->when(isset($request['ware_house_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.ware_house_id', $request['ware_house_id']);
            })
            ->when(isset($request['branch_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.branch_id', $request['branch_id']);
            })
            ->when(isset($request['from_date']), function ($query) use ($request) {
                return $query->whereDate('stock_mutations.date', '>=', Carbon::parse($request['from_date']));
            })
            ->when(isset($request['to_date']), function ($query) use ($request) {
                return $query->whereDate('stock_mutations.date', '<=', Carbon::parse($request['to_date']));
            })
            ->selectRaw('
                stock_mutations.id,
                stock_mutations.item_id,

                items.nama as item_name,
                items.kode as item_code,

                ware_houses.nama as ware_house_name,

                branches.name as branch_name,

                stock_mutations.note,
                stock_mutations.document_code,
                stock_mutations.date,
                stock_mutations.in,
                stock_mutations.out,
                stock_mutations.price_unit,
                stock_mutations.subtotal,
                stock_mutations.total,
                stock_mutations.value,
                stock_mutations.created_at,
                stock_mutations.ordering
            ')
            ->orderBy('stock_mutations.date', 'asc')
            ->orderBy('stock_mutations.ordering', 'asc')
            ->get();

        $all_stock_mutations  = DB::table('stock_mutations')
            ->whereNull('stock_mutations.deleted_at')
            ->when(isset($request['to_date']), function ($query) use ($request) {
                return $query->whereDate('stock_mutations.date', '<=', Carbon::parse($request['to_date']));
            })
            ->when(isset($request['item_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.item_id', $request['item_id']);
            })
            ->when(isset($request['ware_house_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.ware_house_id', $request['ware_house_id']);
            })
            ->when(isset($request['branch_id']), function ($query) use ($request) {
                return $query->where('stock_mutations.branch_id', $request['branch_id']);
            })
            ->orderBy('stock_mutations.date', 'asc')
            ->orderBy('stock_mutations.ordering', 'asc')
            ->get();

        $item_ids = Item::whereHas('stock_mutations')
            ->when(isset($request['item_id']), function ($query) use ($request) {
                return $query->where('items.id', $request['item_id']);
            })
            ->select('id', 'nama')
            ->get();

        $results = $item_ids->map(function ($item) use ($model, $all_stock_mutations, $request) {
            $total = 0;

            $stock_mutations = $model->where('item_id', $item->id)->flatten(1);
            $stock_mutations->map(function ($stock_mutation) use (&$total, $stock_mutations, $all_stock_mutations, $request, $item) {
                $stock_mutation->in_value = $stock_mutation->in * $stock_mutation->price_unit;
                $stock_mutation->out_value = $stock_mutation->out * $stock_mutation->price_unit;

                $stock_mutation->stock_before = $all_stock_mutations
                    ->where('item_id', $item->id)
                    ->where('ordering', '<', $stock_mutation->ordering)->sum('in') -
                    $all_stock_mutations
                    ->where('item_id', $item->id)
                    ->where('ordering', '<', $stock_mutation->ordering)->sum('out');

                $stock_mutation->final_stock = $stock_mutation->stock_before + $stock_mutation->in - $stock_mutation->out;

                $stock_mutation->final_stock_value = $stock_mutation->total;

                $total = $stock_mutation->final_stock * $stock_mutation->value;

                return $stock_mutation;
            })->flatten(3);

            if ($stock_mutations->count() > 0) {
                $beginning_balance = $all_stock_mutations
                    ->where('item_id', $item->id)
                    ->where('ordering', '<', $stock_mutations->first()->ordering);
            } else {
                $beginning_balance = $all_stock_mutations
                    ->where('item_id', $item->id)
                    ->filter(function ($stock_mutation) use ($request) {
                        return Carbon::parse($stock_mutation->date)->lt(Carbon::parse($request['from_date']));
                    });
            }

            return [
                'item' => $item,
                'total' => $total,
                'beginning_balance' => $beginning_balance->sum('in') - $beginning_balance->sum('out'),
                'last_mutation' => $beginning_balance->last(),
                'stock_mutations' => $stock_mutations,
            ];
        });

        return [
            'data' => $results,
            'type' => 'stock-value',
            'title' => 'Laporan Nilai Persediaan Barang',
        ];
    }
}
