<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency as model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CurrencyController extends Controller
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
    protected string $view_folder = 'currency';

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

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('is_local', function ($row) {
                    return $row->is_local ? 'Ya' : 'Tidak';
                })
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
        $request_data = $request->all();
        $request_data['active'] = $request->active ?? false;
        $model->loadModel($request_data);

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

        return redirect()->back()->with($this->ResponseMessageCRUD());
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
        $model = model::findOrFail($id);
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

        if ($model->is_local) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, 'Currency Local tidak bisa di edit'));
        }

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
        if ($model->is_local) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, 'Currency Local tidak bisa di edit'));
        }
        DB::beginTransaction();
        // * validate
        if ($request->ajax()) {
            $this->validate($request, model::rules());
        } else {
            $this->validate_api($request->all(), model::rules());
        }
        // * update data
        $request_data = $request->all();
        $request_data['active'] = $request->active ?? false;
        $model->loadModel($request_data);

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
            return $this->ResponseJsonMessageCRUD();
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
        if ($model->is_local) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, 'Currency Local tidak bisa di hapus'));
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
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request)
    {
        if ($request->search) {
            $first = DB::table('currencies')
                ->where('is_local', 1);

            $model = DB::table('currencies')
                ->union($first)
                ->where('is_local', 0)
                ->where(function ($m) use ($request) {
                    $m->orWhere('kode', 'like', "%$request->search%");
                    $m->orWhere('nama', 'like', "%$request->search%");
                    $m->orWhere('negara', 'like', "%$request->search%");
                })
                ->whereNull('deleted_at')
                ->orderByDesc('created_at')
                ->paginate(10);
        } else {
            $first = DB::table('currencies')
                ->where('is_local', true);

            $model = DB::table('currencies')
                ->union($first)
                ->where('is_local', false)
                ->whereNull('deleted_at')
                ->orderByDesc('created_at')
                ->paginate(10);
        }

        return $this->ResponseJson($model);
    }

    /**
     * select detail api
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function select_detail($id)
    {
        $model = model::findOrFail($id);
        return $this->ResponseJsonData($model);
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select_with_condition(Request $request)
    {
        $first = DB::table('currencies')
            ->where('is_local', 1);

        $model = DB::table('currencies')
            ->union($first)
            ->where('is_local', 0);

        if ($request->allow_foreign == "false") {
            $model = $model->where('id', $request->selected_id);
        }

        if ($request->search) {
            $model = $model->where(function ($m) use ($request) {
                $m->orWhere('kode', 'like', "%$request->search%");
                $m->orWhere('nama', 'like', "%$request->search%");
                $m->orWhere('negara', 'like', "%$request->search%");
            });
        }

        $model = $model
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->paginate(10);

        return $this->ResponseJson($model);
    }
}
