<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tax as model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TaxController extends Controller
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
    protected string $view_folder = 'tax';

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
            $data = model::orderByDesc('created_at')->select('taxes.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('name', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->name,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('name', function ($row) {
                    $str = $row->name;
                    if (is_null($row->coa_sale)) {
                        $str .= '<br><span class="text-capitalize badge bg-' . complete_status()[$row->is_complete ?? 0]['color'] . '">' . complete_status()[$row->is_complete ?? 0]['text'] . '</span>';
                    }

                    return $str;
                })
                ->editColumn('value', fn($row) => $row->value * 100 . '%')
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
                                'display' => true,
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
        $model = new model();
        $model->loadModel(array_merge($request->all(), [
            'value' => str_replace(',', '.', $request->value) / 100,
            'is_show_percent' => $request->is_show_percent ? 1 : 0,
            'is_default' => $request->is_default ? 1 : 0,
            'category' => $request->category ?? $request->name,
        ]));

        // * saving and make reponse
        try {
            if ($request->is_discount) {
                $model->is_discount = 1;
            }
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
        $model = model::with(['coa_sale_data', 'coa_purchase_data'])->findOrFail($id);
        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        return view("admin.$this->view_folder.show", compact('model'));
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
        $model->loadModel(array_merge($request->all(), [
            'value' => str_replace(',', '.', $request->value) / 100,
            'is_default' => $request->is_default ? 1 : 0,
            'is_show_percent' => $request->is_show_percent ? 1 : 0,
            'category' => $request->category ?? $request->name,
        ]));

        // * saving and make reponse
        try {
            if ($request->is_discount) {
                $model->is_discount = 1;
            }
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function select(Request $request)
    {
        $model = model::when($request->search, function ($query) use ($request) {
            return $query->where('name', 'like', "%$request->search%");
        })
            ->when($request->is_default, function ($query) use ($request) {
                return $query->where('is_default', 1);
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return $this->ResponseJson($model);
    }

    /**
     * get tax detail
     *
     * @param int|null $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id = null)
    {
        $model = model::findOrFail($id);
        return $this->ResponseJsonData($model);
    }

    /**
     * get tax detail
     *
     * @param int|null $id
     * @return \Illuminate\Http\Response
     */
    public function detail_post(Request $request)
    {
        $model = model::whereIn('id', $request->id ?? [])
            ->get();

        return $this->ResponseJsonData($model);
    }

    /**
     * select 2 form search
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function select_post(Request $request)
    {
        $model = model::when($request->search, function ($query) use ($request) {
            return $query->where('name', 'like', "%$request->search%");
        })
            ->when($request->selected_item, function ($query) use ($request) {
                return $query->whereNotIn('id', $request->selected_item);
            })
            ->orderByDesc('name')
            ->paginate(10);

        return $this->ResponseJson($model);
    }
}
