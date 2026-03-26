<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResetLeave as model;
use App\Models\ResetLeave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ResetLeaveController extends Controller
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
    protected string $view_folder = 'reset-leave';

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
                ->editColumn('from_date', function ($row) {
                    return localDate($row->from_date) . ' - ' . localDate($row->to_date);
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'open') {
                        $class = 'bg-success';
                    } else {
                        $class = 'bg-danger';
                    }
                    return "<span class='badge $class'>" . $row->status . '</span>';
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
                ->rawColumns(['action', 'status'])
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

        // check existing data
        $check = ResetLeave::where(function ($c) use ($request) {
            $c->where(function ($c) use ($request) {
                $c->whereDate('from_date', '<=', Carbon::parse($request->from_date))
                    ->whereDate('to_date', '>=', Carbon::parse($request->from_date));
            })
                ->orWhere(function ($c) use ($request) {
                    $c->whereDate('from_date', '<=', Carbon::parse($request->to_date))
                        ->whereDate('to_date', '>=', Carbon::parse($request->to_date));
                });
        })
            ->first();

        if ($check) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'sudah ada cut off di range tanggal yang dipilih!'));
        }

        // * create data
        $model = new model();
        $request_data = $request->all();
        $request_data['from_date'] = Carbon::parse($request->from_date);
        $request_data['to_date'] = Carbon::parse($request->to_date);
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

        // check existing data
        $check = ResetLeave::where(function ($c) use ($request) {
            $c->where(function ($c) use ($request) {
                $c->whereDate('from_date', '<=', Carbon::parse($request->from_date))
                    ->whereDate('to_date', '>=', Carbon::parse($request->from_date));
            })
                ->orWhere(function ($c) use ($request) {
                    $c->whereDate('from_date', '<=', Carbon::parse($request->to_date))
                        ->whereDate('to_date', '>=', Carbon::parse($request->to_date));
                });
        })
            ->where('id', '!=', $id)
            ->first();

        if ($check) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'sudah ada cut off cuti di tahun yang dipilih!'));
        }

        // * update data
        $request_data = $request->all();
        $request_data['from_date'] = Carbon::parse($request->from_date);
        $request_data['to_date'] = Carbon::parse($request->to_date);
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

    public function select(Request $request)
    {
        $model = DB::table('reset_leaves')
            ->where(function ($m) use ($request) {
                $m->orWhere('from_date', 'like', "%$request->search%");
                $m->orWhere('to_date', 'like', "%$request->search%");
                $m->orWhere('status', 'like', "%$request->search%");
                $m->orWhere('note', 'like', "%$request->search%");
            })
            ->orderByDesc('from_date', 'desc')
            ->paginate(10);

        $model->getCollection()->transform(function ($m) {
            $m->period = localDate($m->from_date) . ' - ' . localDate($m->to_date);

            return $m;
        });

        return $this->ResponseJson($model);
    }
}
