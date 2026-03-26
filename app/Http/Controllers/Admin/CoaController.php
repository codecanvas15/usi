<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coa as model;
use App\Models\Coa;
use App\Models\JournalDetail;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
use View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class CoaController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder", ['only' => ['index']]);
        $this->middleware("permission:create $this->view_folder", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit $this->view_folder", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete $this->view_folder", ['only' => ['destroy']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'coa';

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
            $data = model::select('coas.*', 'bank_internals.id as bank_internal_id')
                ->leftJoin('bank_internals', function ($bank_internal) {
                    $bank_internal->on('bank_internals.coa_id', 'coas.id');
                    $bank_internal->whereNull('bank_internals.deleted_at');
                })
                ->when($request->filter_bank_internal == "done", function ($query) {
                    $query->whereNotNull('bank_internals.id')
                        ->where('coas.account_type', 'Cash & Bank')
                        ->where('coas.is_parent', 0);
                })
                ->when($request->filter_bank_internal == "not", function ($query) {
                    $query->whereNull('bank_internals.id')
                        ->where('coas.account_type', 'Cash & Bank')
                        ->where('coas.is_parent', 0);
                });

            return DataTables::of($data)
                ->addIndexColumn()
                // ->editColumn('nama', fn ($row) => view('components.datatable.detail-link', [
                //     'field' => $row->nama,
                //     'row' => $row,
                //     'main' => $this->view_folder,
                // ]))
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
                ->addColumn('bank_internal', function ($row) {
                    if ($row->account_type == "Cash & Bank" && $row->is_parent == 0) {
                        if ($row->bank_internal_id) {
                            $color = "success";
                            $text = "fa fa-check";
                        } else {
                            $color = "danger";
                            $text = "fa fa-times";
                        }

                        return "<span class='badge badge-$color'><i class='$text'></i></span>";
                    }
                })

                ->rawColumns(['action', 'bank_internal'])
                ->make(true);
        }

        return view('admin.' . $this->view_folder . '.index', [
            'tree' => $this->tree_view_api(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $model = [];

        return view("admin.$this->view_folder.create", [
            'model' => $model,
            'tree' => $this->tree_view_api(),
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
        DB::beginTransaction();
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

        app('App\Http\Controllers\Admin\ProfitLossSettingController')->refresh();
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
        $model = model::with('bank_internal')
            ->with('currency')
            ->findOrFail($id);
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

        return view("admin.$this->view_folder.edit", [
            'model' => $model,
            'tree' => $this->tree_view_api(),
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
        $model = model::findOrFail($id);

        DB::beginTransaction();

        // * validate
        if ($request->ajax()) {
            $this->validate($request, model::rules());
        } else {
            $this->validate_api($request->all(), model::rules());
        }

        // get_coa_types

        // * update data
        $request['branch_id'] = $request->branch_id ?? null;
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

        app('App\Http\Controllers\Admin\ProfitLossSettingController')->refresh();
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
            if ($model->journal_details()->exists()) {
                DB::rollBack();

                throw new \Exception('Can not delete coa with journal details');
            }
            $model->delete();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'delete', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
        }
        DB::commit();
        app('App\Http\Controllers\Admin\ProfitLossSettingController')->refresh();
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
        $model = model::where('is_parent', false)
            ->when(get_current_branch(), function ($query) {
                $query->when(!get_current_branch()->is_primary, function ($query) {
                    $query->where(function ($query) {
                        $query->where('branch_id', get_current_branch()->id)
                            ->orWhere('branch_id', null);
                    });
                });
            })
            ->leftJoin('journal_details', 'journal_details.coa_id', 'coas.id')
            ->groupBy('coas.id')
            ->selectRaw('coas.*, COALESCE(count(journal_details.id),0) as freq')
            ->orderBy('freq', 'desc')
            ->orderBy('account_code')
            ->when($request->currency_id, function ($query) use ($request) {
                $query->where('coas.currency_id', $request->currency_id);
            });

        if ($request->search) {
            $model = $model->where(function ($query) use ($request) {
                $query->where('name', 'like', "%$request->search%");
                $query->orWhere('account_code', 'like', "%$request->search%");
            });
        }
        if ($request->branch_id) {
            $model->where('coas.branch_id', $request->branch_id);
        }
        if ($request->account_type) {
            if ($request->account_type == "Cash & Bank") {
                $model->where(function ($query) {
                    $query->where('account_type', 'Cash & Bank')
                        ->orWhereHas('bank_internal');
                });
            } elseif (is_array($request->account_type)) {
                // Handle multiple account types
                $model->whereIn('account_type', $request->account_type);
            } else {
                // Single account type fallback
                $model->where('account_type', $request->account_type);
            }
        }
        if ($request->account_category) {
            $model->where('account_category', $request->account_category);
        }

        if ($request->filled('selected_start_id')) {
            $selectedStart = model::find($request->selected_start_id);
            if ($selectedStart) {
                $model->where('account_code', '>=', $selectedStart->account_code);
            }
        }
        $model = $model
            ->havingRaw('CHAR_LENGTH(coas.account_code) = 6')
            ->paginate(10);

        return $this->ResponseJsonData($model);
    }

    public function select_for_bank_internal(Request $request)
    {
        $model = model::where('is_parent', false)
            ->leftJoin('journal_details', 'journal_details.coa_id', 'coas.id')
            ->groupBy('coas.id')
            ->selectRaw('coas.*, COALESCE(count(journal_details.id),0) as freq')
            ->orderBy('freq', 'desc')
            ->orderBy('account_code')
            ->when($request->currency_id, function ($query) use ($request) {
                $query->where('coas.currency_id', $request->currency_id);
            })
            ->whereDoesntHave('bank_internal');

        if ($request->search) {
            $model = $model->where(function ($query) use ($request) {
                $query->where('name', 'like', "%$request->search%");
                $query->orWhere('account_code', 'like', "%$request->search%");
            });
        }
        if ($request->branch_id) {
            $model->where('coas.branch_id', $request->branch_id);
        }
        if ($request->account_type) {
            if ($request->account_type == "Cash & Bank") {
                $model->where(function ($query) {
                    $query->where('account_type', 'Cash & Bank')
                        ->orWhereHas('parent', function ($query) {
                            $query->where('name', 'Hutang bank');
                        });
                });
            } else {
                $model->where('account_type', $request->account_type);
            }
        }
        $model = $model
            ->limit(10)
            ->get();

        return $this->ResponseJsonData($model);
    }

    public function select_coa_types()
    {
        return $this->ResponseJsonData(get_coa_types());
    }

    public function select_coa_parents(Request $request)
    {
        if ($request->search) {
            $model = model::where('can_have_children', true)
                ->whereDoesntHave('journal_details', function ($journal_detail) {
                    $journal_detail->whereHas('journal', function ($journal) {
                        $journal->whereIn('status', ['approve', 'revert', 'pending']);
                    });
                })
                ->where(function ($query) use ($request) {
                    $query->orWhere('name', 'like', "%$request->search%");
                    $query->orWhere('account_code', 'like', "%$request->search%");
                })
                ->limit(10)
                ->get();
        } else {
            $model = model::orderByDesc('created_at')
                ->where('can_have_children', true)
                ->limit(10)
                ->get();
        }

        return $this->ResponseJsonData($model);
    }

    public function select_with_type(Request $request, $coa_type = null)
    {
        if ($request->search) {
            $model = model::where(function ($query) use ($coa_type) {
                $query->where('account_type', $coa_type);
                $query->where('is_parent', false);
            })->where(function ($query) use ($request) {
                $query->orWhere('name', 'like', "%$request->search%");
                $query->orWhere('account_code', 'like', "%$request->search%");
            })
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        } else {
            $model = model::orderByDesc('created_at')
                ->where('is_parent', false)
                ->where('account_type', $coa_type)
                ->limit(10)
                ->get();
        }

        return $this->ResponseJsonData($model);
    }

    /**
     * COA Tree API
     *
     * @return \Illuminate\Http\Response
     */
    public function tree_view_api()
    {
        $coas = Coa::all();

        $parents = $coas->where('is_parent', 1)
            ->where('parent_id', null);

        $data = collect();
        foreach ($parents as $coa) {
            $coa->children = $this->get_all_childs($coa, $coas);
            $data->push($coa);
        }

        $return_data = $data;

        return View::make("admin.coa.tree")
            ->with("results", $return_data)
            ->render();
    }

    /**
     * detail
     *
     * @param int|null $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        $model = model::with(['bank_internal', 'currency'])
            ->findOrFail($id);

        return $this->ResponseJsonData($model);
    }

    /**
     * export excel format
     *
     * @return \Illuminate\Http\Response
     */
    public function import_format()
    {
        return $this->ResponseDownload(public_path('import/admin/coa.xlsx'));;
    }

    /**
     * import excel
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            Excel::import(new \App\Imports\CoaImport(), $file);
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'import', null, $th->getMessage()));
        }

        DB::commit();
        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'import', 'import data'));
    }

    public function export()
    {
        try {
            $coas = Coa::all();

            $parents = $coas->where('is_parent', 1)
                ->where('parent_id', null);

            $data = collect();
            foreach ($parents as $coa) {
                $coa->children = $this->get_all_childs($coa, $coas);
                $data->push($coa);
            }

            $return_data = $data;

            $pdf = FacadePdf::loadView("admin.coa.export", compact('return_data'))->setPaper('a4', 'portrait');
            return $pdf->stream();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function get_all_childs($coa, $all_coas)
    {
        $children = $all_coas->where('parent_id', $coa->id);

        $childs = collect();

        foreach ($children as $child) {
            $childs->push($child);
            $childs = $childs->merge($this->get_all_childs($child, $all_coas));
        }

        return $childs;
    }

    public function repair_beginning_balance()
    {
        $data = [
            [
                'id' => 5,
                'debit' => 144300.00,
                'debit_exchanged' => 144300.00,
                'credit' => 0.00,
                'credit_exchanged' => 0.00
            ],
            [
                'id' => 7,
                'debit' => 10544853.41,
                'debit_exchanged' => 10544853.41,
                'credit' => 0.00,
                'credit_exchanged' => 0.00
            ],
            [
                'id' => 16,
                'debit' => 162074430.00,
                'debit_exchanged' => 162074430.00,
                'credit' => 0.00,
                'credit_exchanged' => 0.00
            ],
            [
                'id' => 30,
                'debit' => 102081244.58,
                'debit_exchanged' => 102081244.58,
                'credit' => 0.00,
                'credit_exchanged' => 0.00
            ],
            [
                'id' => 41,
                'debit' => 8230000.00,
                'debit_exchanged' => 8230000.00,
                'credit' => 0.00,
                'credit_exchanged' => 0.00
            ],
            [
                'id' => 59,
                'debit' => 5371500.00,
                'debit_exchanged' => 5371500.00,
                'credit' => 0.00,
                'credit_exchanged' => 0.00
            ],
            [
                'id' => 64,
                'debit' => 0.00,
                'debit_exchanged' => 0.00,
                'credit' => 3581000.00,
                'credit_exchanged' => 3581000.00
            ],
            [
                'id' => 65,
                'debit' => 0.00,
                'debit_exchanged' => 0.00,
                'credit' => 8162275.00,
                'credit_exchanged' => 8162275.00
            ],
            [
                'id' => 88,
                'debit' => 0.00,
                'debit_exchanged' => 0.00,
                'credit' => 90643256.25,
                'credit_exchanged' => 90643256.25
            ],
            [
                'id' => 100,
                'debit' => 2735208.00,
                'debit_exchanged' => 2735208.00,
                'credit' => 0.00,
                'credit_exchanged' => 0.00
            ],
            [
                'id' => 101,
                'debit' => 0.00,
                'debit_exchanged' => 0.00,
                'credit' => 6803101.00,
                'credit_exchanged' => 6803101.00
            ],
            [
                'id' => 105,
                'debit' => 0.00,
                'debit_exchanged' => 0.00,
                'credit' => 780660.75,
                'credit_exchanged' => 780660.75
            ],
            [
                'id' => 110,
                'debit' => 0.00,
                'debit_exchanged' => 0.00,
                'credit' => 39346576.00,
                'credit_exchanged' => 39346576.00
            ],
            [
                'id' => 129,
                'debit' => 0.00,
                'debit_exchanged' => 0.00,
                'credit' => 45414737.78,
                'credit_exchanged' => 45414737.78
            ],
            [
                'id' => 142,
                'debit' => 0.00,
                'debit_exchanged' => 0.00,
                'credit' => 732899677.90,
                'credit_exchanged' => 732899677.90
            ],
            [
                'id' => 156,
                'debit' => 534021735.13,
                'debit_exchanged' => 534021735.13,
                'credit' => 0.00,
                'credit_exchanged' => 0.00
            ],
            [
                'id' => 160,
                'debit' => 82000000.00,
                'debit_exchanged' => 82000000.00,
                'credit' => 0.00,
                'credit_exchanged' => 0.00
            ],
            [
                'id' => 164,
                'debit' => 3665341.71,
                'debit_exchanged' => 3665341.71,
                'credit' => 0.00,
                'credit_exchanged' => 0.00
            ],
            [
                'id' => 170,
                'debit' => 16000.00,
                'debit_exchanged' => 16000.00,
                'credit' => 0.00,
                'credit_exchanged' => 0.00
            ],
            [
                'id' => 174,
                'debit' => 3702833.56,
                'debit_exchanged' => 3702833.56,
                'credit' => 0.00,
                'credit_exchanged' => 0.00
            ],
            [
                'id' => 221,
                'debit' => 411000.00,
                'debit_exchanged' => 411000.00,
                'credit' => 0.00,
                'credit_exchanged' => 0.00
            ],
            [
                'id' => 223,
                'debit' => 2995200.00,
                'debit_exchanged' => 2995200.00,
                'credit' => 0.00,
                'credit_exchanged' => 0.00
            ],
            [
                'id' => 231,
                'debit' => 3581000.00,
                'debit_exchanged' => 3581000.00,
                'credit' => 0.00,
                'credit_exchanged' => 0.00
            ],
            [
                'id' => 233,
                'debit' => 5936200.00,
                'debit_exchanged' => 5936200.00,
                'credit' => 0.00,
                'credit_exchanged' => 0.00
            ],
            [
                'id' => 238,
                'debit' => 0.00,
                'debit_exchanged' => 0.00,
                'credit' => 33595.22,
                'credit_exchanged' => 33595.22
            ],
            [
                'id' => 242,
                'debit' => 0.00,
                'debit_exchanged' => 0.00,
                'credit' => 956.54,
                'credit_exchanged' => 956.54
            ],
            [
                'id' => 247,
                'debit' => 361000.00,
                'debit_exchanged' => 361000.00,
                'credit' => 0.00,
                'credit_exchanged' => 0.00
            ],
            [
                'id' => 248,
                'debit' => 5251.33,
                'debit_exchanged' => 5251.33,
                'credit' => 0.00,
                'credit_exchanged' => 0.00
            ],
            [
                'id' => 255,
                'debit' => 0.00,
                'debit_exchanged' => 0.00,
                'credit' => 211261.29,
                'credit_exchanged' => 211261.29
            ],
        ];

        DB::beginTransaction();
        try {

            foreach ($data as $key => $d) {
                $journal_detail = JournalDetail::where('journal_id', 1)
                    ->where('coa_id', $d['id'])
                    ->first();
                if (!$journal_detail) {
                    $journal_detail = new JournalDetail();
                }

                $journal_detail->journal_id = 1;
                $journal_detail->coa_id = $d['id'];
                $journal_detail->debit = $d['debit'];
                $journal_detail->credit = $d['credit'];
                $journal_detail->debit_exchanged = $d['debit_exchanged'];
                $journal_detail->credit_exchanged = $d['credit_exchanged'];
                $journal_detail->remark = "Saldo Awal COA";
                $journal_detail->save();
            }

            DB::commit();

            return response()->json('success insert data');
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json($th->getMessage());
        }
    }

    public function refresh()
    {
        $coas = Coa::all();

        DB::beginTransaction();
        try {
            foreach ($coas as $key => $coa) {
                if ($coa->journal_details()->exists()) {
                    $coa->can_have_children = 0;
                } else {
                    $coa->can_have_children = 1;
                }

                if ($coa->childs()->exists()) {
                    $coa->is_parent = 1;
                } else {
                    $coa->is_parent = 0;
                }

                $coa->save();
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }
}
