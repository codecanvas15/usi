<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Period as model;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PeriodController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder", ['only' => ['index']]);
        $this->middleware("permission:generate $this->view_folder", ['only' => ['generate']]);
    }
    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'period';

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
                ->make(true);
        }

        return view('admin.' . $this->view_folder . '.index');
    }

    /**
     * generate data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {
        // * validate
        if ($request->ajax()) {
            $this->validate_api($request->all(), [
                'year' => 'required|digits:4',
            ]);
        } else {
            $this->validate($request, [
                'year' => 'required|digits:4',
            ]);
        }

        // *  validate model if exist
        $model = model::where('tahun', $request->year)->get();
        if ($model->count() > 0) {
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', 'Data sudah ada');
            } else {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'Data sudah ada'));
            }
        }

        // * create data
        try {
            generate_period($request->year);
        } catch (\Throwable $th) {
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', $th->getMessage());
            } else {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', $th->getMessage()));
            }
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'create'));
    }

    /**
     * select 2 form search
     *
     * @param  int  $year
     */
    public function select(Request $request, $year = null)
    {
        if ($year) {
            $model = model::where('tahun', $year);
        } else {
            $model = new model();
        }

        if ($request->search) {
            $model->where('value', 'like', "%$request->search%")->orderByDesc('created_at')->limit(10);
        } else {
            $model->orderByDesc('created_at')->limit(10);
        }

        return $this->ResponseJsonData($model->get());
    }
}
