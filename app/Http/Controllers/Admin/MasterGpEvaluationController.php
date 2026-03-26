<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterGpEvaluation as model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MasterGpEvaluationController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view master-evaluation", ['only' => ['index', 'show']]);
        $this->middleware("permission:create master-evaluation", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit master-evaluation", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete master-evaluation", ['only' => ['destroy']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'master-gp-evaluation';

    /**
     * search  value
     *
     * @var string|null
     */
    protected string|null $search = null;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = model::select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('type', function ($row) {
                    return ucwords($row->type);
                })
                ->editColumn('description', function ($row) {
                    return ucfirst($row->description);
                })
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'permission_name' => 'master-evaluation',
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

        return view('admin.' . $this->view_folder . '.create', compact($model));
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
        if ($request->ajax()) {
            $this->validate_api($request->all(), model::rules());
        } else {
            $this->validate($request, model::rules());
        }

        // * create data
        $model = new model();
        $model->loadModel($request->all());

        // * saving and make reponse
        DB::beginTransaction();
        try {
            $model->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'create'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
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

        // * validate
        if ($request->ajax()) {
            $this->validate($request, model::rules());
        } else {
            $this->validate_api($request->all(), model::rules());
        }

        // * update data
        $model->loadModel($request->all());

        // * saving and make reponse
        DB::beginTransaction();
        try {
            $model->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'edit'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $model = model::findOrFail($id);

        DB::beginTransaction();
        try {
            $model->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'delete', null, $th->getMessage(), 422);
            }
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
        }

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
            $data = model::where('type', 'like', "%{$request->search}%")
                ->where('description', 'like', "%{$request->search}%")
                ->limit(10)
                ->orderByDesc('created_at')
                ->get();

            if ($request->not_in_ids) {
                $data = model::whereNotIn('id', $request->not_in_ids)
                    ->limit(10)
                    ->orderByDesc('created_at')
                    ->get();
            }
        } else {
            $data = model::orderByDesc('created_at')->get();

            if ($request->not_in_ids) {
                $data = model::orderByDesc('created_at')
                    ->whereNotIn('id', $request->not_in_ids)
                    ->get();
            }
        }

        return $this->ResponseJsonData($data);
    }

    public function findById(Request $request)
    {
        $model = model::findOrFail($request->id);
        return response()->json($model);
    }
}
