<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IncomeTax;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class IncomeTaxController extends Controller
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
    private string $permission = 'income-tax';

    /**
     * Where the view should be rendered
     */
    private string $view = 'admin.income-tax';

    /**
     * Route name
     */
    private string $routeName = 'admin.income-tax';

    private string $title = 'PPh 21';

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = IncomeTax::select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('min', function ($row) {
                    return formatNumber($row->min);
                })
                ->editColumn('max', function ($row) {
                    return formatNumber($row->max);
                })
                ->editColumn('range', function ($row) {
                    return formatNumber($row->min) . ' - ' . formatNumber($row->max);
                })
                ->editColumn('percentage', function ($row) {
                    return formatNumber($row->percentage);
                })
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
        }

        $settings = Setting::where('type', 'payroll')
            ->get();

        return view("{$this->view}.index", [
            'title' => $this->title,
            'permission' => $this->permission,
            'routeName' => $this->routeName,
            'view' => $this->view,
            'settings' => $settings
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
            'title' => $this->title,
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
        $request['min'] = thousand_to_float($request->min);
        $request['max'] = thousand_to_float($request->max);
        $request['percentage'] = thousand_to_float($request->percentage);

        // * validate data
        $this->validate($request, [
            'min' => 'required|numeric',
            'max' => 'required|numeric',
            'percentage' => 'required|numeric',
        ]);

        DB::beginTransaction();

        // * store data
        $model = new IncomeTax();
        $model->fill([
            'min' => $request->min,
            'max' => $request->max,
            'percentage' => $request->percentage,
        ]);

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()
                ->withInput($request->all())
                ->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
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
    public function show($id, Request $request)
    {
        $data = IncomeTax::findOrFail($id);
        if ($request->ajax()) {
            return $this->ResponseJsonData($data);
        }
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
            'title' => $this->title,
            'permission' => $this->permission,
            'routeName' => $this->routeName,
            'view' => $this->view,
            'model' => IncomeTax::findOrFail($id),
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
        $request['min'] = thousand_to_float($request->min);
        $request['max'] = thousand_to_float($request->max);
        $request['percentage'] = thousand_to_float($request->percentage);

        // * validate the request
        $this->validate($request, [
            'min' => 'required|numeric',
            'max' => 'required|numeric',
            'percentage' => 'required|numeric',
        ]);

        DB::beginTransaction();
        $model = IncomeTax::findOrFail($id);

        // * update data
        $model->fill([
            'min' => $request->min,
            'max' => $request->max,
            'percentage' => $request->percentage,
        ]);
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()
                ->withInput($request->all())->with($this->ResponseMessageCRUD(false, 'edit', $th->getMessage()));
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
            IncomeTax::findOrFail($id)->delete();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("$this->routeName.index")->with($this->ResponseMessageCRUD(true, 'delete'));
    }
}
