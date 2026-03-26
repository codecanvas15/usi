<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemCategory as model;
use App\Models\ItemCategoryCoa;
use App\Models\ItemTypeCoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ItemCategoryController extends Controller
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
    protected string $view_folder = 'item-category';

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
            $data = model::orderByDesc('created_at')->with(['item_type'])->select('item_categories.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('kode', fn ($row) => view('components.datatable.detail-link', [
                    'field' => $row->kode,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('nama', function ($row) {
                    $return_text = $row->nama;

                    if (!$row->is_complete) {
                        $return_text .= '<br> <div class="text-capitalize badge bg-' . complete_status()[$row->is_complete]['color'] . '">' . complete_status()[$row->is_complete]['text'] . '</div>';
                    }

                    return $return_text;
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
                ->rawColumns(['nama', 'action'])
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
        // * get item type coa
        $item_type = \App\Models\ItemType::findOrFail($request->item_type_id);
        $item_type_coas = \App\Models\ItemTypeCoa::where('item_type_id', $item_type->id)->get();

        // * if item type coa is empty
        if ($item_type_coas->isEmpty()) {
            DB::rollBack();

            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, 'item_type_coa is empty');
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'item_type_coa is empty'));
        }

        // * create item category coas
        if (is_array($request->type)) {
            // * if request coa ids count is not equal to number of item type coas
            // if (count($request->coa_id) != $item_type_coas->count()) {
            //     DB::rollBack();
            //     return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'coa_id is not equal to number of item type coas'));
            // }

            foreach ($request->type as $key => $value) {
                $item_category_coa = new ItemCategoryCoa();
                $item_category_coa->loadModel([
                    'item_category_id' => $model->id,
                    'coa_id' => $request->coa_id[$key] ?? null,
                    'type' => $value,
                ]);

                try {
                    $item_category_coa->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
                    }

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
                }
            }
        } else {
            DB::rollBack();

            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, 'coa_id is empty');
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'coa_id is empty'));
        }

        if ($model->item_category_coas()->whereNull('coa_id')->count() > 0) {
            $model->is_complete = false;
            $model->save();
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
        // * update data
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

        // * create item category coas
        if (count($model->item_category_coas) != count($request->type)) {
            ItemCategoryCoa::where('item_category_id', $id)->delete();
        }

        // * get item type coa
        $item_type = \App\Models\ItemType::findOrFail($request->item_type_id);
        $item_type_coas = \App\Models\ItemTypeCoa::where('item_type_id', $item_type->id)->get();
        // * if item type coa is empty
        if ($item_type_coas->isEmpty()) {
            DB::rollBack();

            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, 'item_type_coa is empty');
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'item_type_coa is empty'));
        }

        if (is_array($request->type)) {
            // * if request coa ids count is not equal to number of item type coas
            if (count($request->type) != $item_type_coas->count()) {
                DB::rollBack();
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'coa_id is not equal to number of item type coas'));
            }

            foreach ($request->type as $key => $value) {
                $item_category_coa = ItemCategoryCoa::where('item_category_id', $id)
                    ->where('type', $request->type[$key])
                    ->first();

                if (!$item_category_coa) {
                    $item_category_coa = new ItemCategoryCoa();
                }

                $item_category_coa->loadModel([
                    'item_category_id' => $model->id,
                    'coa_id' => $request->coa_id[$key] ?? null,
                    'type' => $value,
                ]);

                try {
                    $item_category_coa->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
                    }

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
                }
            }
        } else {
            DB::rollBack();

            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, 'coa_id is empty');
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'coa_id is empty'));
        }

        $model->is_complete = true;
        $model->save();

        Item::where('item_category_id', $id)->update([
            'is_complete' => true,
        ]);

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
            ItemCategoryCoa::where('item_category_id', $id)->delete();
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
            $model = model::where('nama', 'like', "%$request->search%")
                ->when($request->item_type_id, function ($query) use ($request) {
                    return $query->whereIn('item_type_id', explode(',',$request->item_type_id));
                })
                ->get();
        } else {
            $model = model::when($request->item_type_id, function ($query) use ($request) {
                return $query->whereIn('item_type_id', explode(',',$request->item_type_id));
            })
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
        }

        return $this->ResponseJsonData($model);
    }

    public function get_item_type_coa(Request $request, $id)
    {
        if ($request->ajax()) {
            $item_type_coas = ItemTypeCoa::where('item_type_id', $id)->get();
            $default_item_type_coa = $item_type_coas;
            $edit = $request->is_edit == 1 ? true : false;

            return view('admin.item-category._item_type_coa', compact('item_type_coas', 'edit', 'default_item_type_coa'))->render();
        }
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
            Excel::import(new \App\Imports\ItemCategoryImport(), $file);
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'import', null, $th->getMessage()));
        }

        DB::commit();
        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'import', 'import data'));
    }
}
