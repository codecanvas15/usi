<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollPeriod as model;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PayrollPeriodController extends Controller
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
    protected string $view_folder = 'payroll-period';

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
            $data = model::orderByDesc('created_at')
                ->when($request->from_date, fn ($q) => $q->where('date', '>=', Carbon::parse($request->from_date)))
                ->when($request->to_date, fn ($q) => $q->where('date', '<=', Carbon::parse($request->to_date)))
                ->select('*');


            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', fn ($row) => localDate($row->date))
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => 'payroll-period',
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
        $rules = [
            'name'  => ['required'],
            'type'  => ['required'],
            'date'  => ['required'],
            'date_end' => ['required'],
        ];

        $messages = [
            'name.required' => 'Nama periode belum diisi.',
            'type.required' => 'Tipe periode belum dipilih.',
            'date.required' => 'Dari tanggal belum dipilih.',
            'date_end.required' => 'Sampai tanggal belum dipilih.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        DB::beginTransaction();
        try {
            $period = new model();
            $period->name = $request->name;
            $period->type = $request->type;
            $period->date = Carbon::parse($request->date);
            $period->end_date = Carbon::parse($request->date_end);

            if (!$period->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
            }

            $period->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD());
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
        $model = model::findOrFail($id);

        if (!$model->check_available_date) {
            return abort(403);
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
        $rules = [
            'name'  => ['required'],
            'type'  => ['required'],
            'date'  => ['required'],
            'date_end' => ['required'],
        ];

        $messages = [
            'name.required' => 'Nama periode belum diisi.',
            'type.required' => 'Tipe periode belum dipilih.',
            'date.required' => 'Dari tanggal belum dipilih.',
            'date_end.required' => 'Sampai tanggal belum dipilih.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        DB::beginTransaction();
        try {
            $model = model::findOrFail($id);
            $model->name = $request->name;
            $model->type = $request->type;
            $model->date = Carbon::parse($request->date);
            $model->end_date = Carbon::parse($request->date_end);
            if (!$model->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal sudah closing'));
            }
            $model->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
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
            $model->forceDelete();
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

    public function detail($id)
    {
        $model = model::find($id);
        return $this->ResponseJsonData($model);
    }

    public function select(Request $request)
    {
        if ($request->search) {
            $model = model::orWhere('name', 'like', "%$request->search%")
                ->orWhere('type', 'like', "%$request->search%")
                ->orderByDesc('created_at')
                ->get();
        } else {
            $model = model::orderByDesc('created_at')->get();
        }

        return $this->ResponseJsonData($model);
    }
}
