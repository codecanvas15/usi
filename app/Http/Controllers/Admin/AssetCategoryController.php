<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AssetCategoryController extends Controller
{
    /**
     * initial
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->permission", ['only' => ['index', 'show']])->only('index', 'show');
        $this->middleware("permission:create $this->permission", ['only' => ['store']])->only('store');
        $this->middleware("permission:edit $this->permission", ['only' => ['update', 'edit']])->only('update', 'edit');
        $this->middleware("permission:delete $this->permission", ['only' => ['destroy']])->only('destroy');
    }

    /**
     * Permission access names
     */
    private string $permission = 'asset-category';

    /**
     * Where the view should be rendered
     */
    private string $view = 'admin.asset-category';

    /**
     * Route name
     */
    private string $routeName = 'admin.asset-category';

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = AssetCategory::orderByDesc('created_at')->select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->permission,
                        'permission_name' => $this->permission,
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
                ->rawColumns(['name', 'action'])
                ->make(true);
        };

        return view("{$this->view}.index", [
            'title' => 'Kategori Asset',
            'permission' => $this->permission,
            'routeName' => $this->routeName,
            'view' => $this->view,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("{$this->view}.create", [
            'title' => 'Kategori Asset',
            'permission' => $this->permission,
            'routeName' => $this->routeName,
            'view' => $this->view,
            'model' => []
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
        // * validate data
        $this->validate($request, [
            'name' => 'required|unique:asset_categories,name',
        ]);

        DB::beginTransaction();

        // * store data
        $model = new AssetCategory();
        $model->fill([
            'name' => $request->name,
            'percentage' => thousand_to_float($request->percentage),
        ]);

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withInput($request->all())->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("$this->routeName.index")->with($this->ResponseMessageCRUD(true, 'create'));
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
        return view("{$this->view}.edit", [
            'title' => 'Kategori Asset',
            'permission' => $this->permission,
            'routeName' => $this->routeName,
            'view' => $this->view,
            'model' => AssetCategory::findOrFail($id),
        ]);
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
        // * validate the request
        $this->validate($request, [
            'name' => 'required|unique:asset_categories,name,' . $id,
        ]);

        DB::beginTransaction();
        $model = AssetCategory::findOrFail($id);

        // * update data
        $model->fill([
            'name' => $request->name,
            'percentage' => thousand_to_float($request->percentage),
        ]);
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withInput($request->all())->with($this->ResponseMessageCRUD(false, 'edit', $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("$this->routeName.index")->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            AssetCategory::findOrFail($id)->delete();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("$this->routeName.index")->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * Select Kategori Asset api
     */
    public function select(Request $request)
    {
        return $this->ResponseJsonData(
            data: AssetCategory::when($request->search, fn ($q) => $q->where('name', 'like', "%$request->search%"))
                ->orderBy('name')
                ->limit(10)
                ->get()
        );
    }
}
