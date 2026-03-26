<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\StockMutation;
use App\Models\WareHouse as model;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\WareHouse;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class WareHouseController extends Controller
{
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
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'ware-house';

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
        if ($request->ajax()) {
            $data = model::orderByDesc('created_at')->select('*');

            if (!get_current_branch()->is_primary) {
                $data->where('branch_id', get_current_branch_id());
            }

            if ($request->branch_id) {
                $data->where('branch_id', $request->branch_id);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('nama', fn ($row) => view('components.datatable.detail-link', [
                    'field' => $row->nama,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'btn_config' => [
                            'detail' => [
                                'display' => false,
                            ],
                            'edit' => [
                                'display' => true,
                            ],
                            'delete' => [
                                'display' => strtolower($row->nama) != 'gudang reject',
                            ],
                        ],
                    ]);
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.' . $this->view_folder . '.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $model = [];

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
        // * validate
        if ($request->ajax()) {
            $this->validate_api($request->all(), model::rules());
        } else {
            $this->validate($request, model::rules());
        }
        // * create data
        $data_create = $request->all();
        $data_create['branch_id'] = get_current_branch()->is_primary ? $request->branch_id : get_current_branch_id();
        $model = new model();
        $model->loadModel($data_create);

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

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD());
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
        $model = model::with(['stockUsages'])->findOrFail($id);
        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        return view("admin.$this->view_folder.show", compact('model', 'id'));
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
        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        return view("admin.$this->view_folder.edit", compact('model'));
    }

    public function getStockCard(Request $request)
    {
        $warehouse = WareHouse::find($request->warehouse_id);
        $data = Item::whereIn('type', ['general', 'trading'])->orderByDesc('created_at')
            ->when($warehouse, function ($query) use ($warehouse) {
                $query->where('type', $warehouse->type);
            });
        $branch_id = Auth::user()->branch_id ?? Auth::user()->temp_branch_id ?? $request->branch_id;

        $stock_mutations = DB::table('stock_mutations')
            ->whereNull('stock_mutations.deleted_at')
            ->whereIn('item_id', $data->pluck('id'))
            ->where('ware_house_id', $request->warehouse_id)
            ->when($request->from_date, fn ($q) => $q->whereDate('date', '<=', Carbon::parse($request->from_date)))
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('kode', function ($row) use ($request) {
                $route = route('admin.stock-card.show', [
                    'id' => $row->id,
                    'warehouse_id' => $request->warehouse_id,
                ]);
                return "<a href='{$route}' class='text-primary text-decoration-underline hover_text-dark'>{$row->kode}</a>";
            })
            ->editColumn('minimum_stock', function ($row) {
                return floatDotFormat($row->branch_min_stock);
            })
            ->editColumn('stock', function ($row) use ($stock_mutations) {
                $in = $stock_mutations->where('item_id', $row->id)
                    ->sum('in');
                $out = $stock_mutations->where('item_id', $row->id)
                    ->sum('out');

                return formatNumber($in - $out);
            })
            ->rawColumns(['action', 'kode'])
            ->make(true);
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

        DB::beginTransaction();
        // * validate
        if ($request->ajax()) {
            $this->validate($request, model::rules());
        } else {
            $this->validate_api($request->all(), model::rules());
        }
        // * update data
        $model->loadModel($request->all());

        // * saving and make reponse
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD(true, 'edit');
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'edit'));
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

        if ($model->stock_mutations()->exists()) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, 'Data ini terhubung dengan data lain.'));
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

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * select 2 form search
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function select(Request $request)
    {
        $model = model::where('nama', 'like', "%$request->search%");

        if ($request->type) {
            $model->where('type', $request->type);
        }

        $model = $model->orderByDesc('created_at')->paginate(10);
        return $this->ResponseJson($model);
    }

    /**
     * select_by_type
     *
     * @param Request  $request
     * @param string|null $type
     * @return mixed
     */
    public function select_by_type(Request $request, string|null $type)
    {
        $model = model::where('nama', 'like', "%$request->search%")->where('type', $type)->orderByDesc('created_at')->paginate(10);
        return $this->ResponseJson($model);
    }

    /**
     * detail
     *
     * @param $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function detail($id)
    {
        $model = model::findOrFail($id);

        return $this->ResponseJsonData($model);
    }
}
