<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NonTaxableIncome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class NonTaxableIncomeController extends Controller
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
    private string $permission = 'non-taxable-income';

    /**
     * Where the view should be rendered
     */
    private string $view = 'admin.non-taxable-income';

    /**
     * Route name
     */
    private string $routeName = 'admin.non-taxable-income';

    private string $title = 'Penghasilan Tidak Kena Pajak';

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = NonTaxableIncome::select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('amount', function ($row) {
                    return formatNumber($row->amount);
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

        return view("{$this->view}.index", [
            'title' => $this->title,
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
        $request['amount'] = thousand_to_float($request->amount);

        // * validate data
        $this->validate($request, [
            'name' => 'required',
            'amount' => 'required|numeric',
            'note' => 'required',
        ]);

        DB::beginTransaction();

        // * store data
        $model = new NonTaxableIncome();
        $model->fill([
            'name' => $request->name,
            'amount' => $request->amount,
            'note' => $request->note
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
        $data = NonTaxableIncome::findOrFail($id);
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
            'model' => NonTaxableIncome::findOrFail($id),
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
        $request['amount'] = thousand_to_float($request->amount);

        // * validate the request
        $this->validate($request, [
            'name' => 'required',
            'amount' => 'required|numeric',
            'note' => 'required',
        ]);

        DB::beginTransaction();
        $model = NonTaxableIncome::findOrFail($id);

        // * update data
        $model->fill([
            'name' => $request->name,
            'amount' => $request->amount,
            'note' => $request->note
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
            NonTaxableIncome::findOrFail($id)->delete();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("$this->routeName.index")->with($this->ResponseMessageCRUD(true, 'delete'));
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
            $model = NonTaxableIncome::where('nama', 'like', "%$request->search%")
                ->orderByDesc('note')->limit(10)
                ->get();
        } else {
            $model = NonTaxableIncome::orderByDesc('note')->limit(10)->get();
        }

        return $this->ResponseJsonData($model);
    }
}
