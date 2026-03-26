<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Price as model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ItemPriceController extends Controller
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
    protected string $view_folder = 'item-price';

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
    public function index(Request $request, $id)
    {
        $data = model::orderByDesc('created_at')->where('item_id', $id);

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return view("admin.item.datatable.price-btn", [
                    'row' => $row,
                    'main' => 'item.price',
                    'btn_config' => [
                        'detail' => [
                            'display' => false,
                        ],
                        'edit' => [
                            'display' => false,
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            $this->validate_api($request->all(), [
                'item_id' => 'required|exists:items,id',
                'harga_jual' => 'required',
                'harga_beli' => 'required',
            ]);
        } else {
            $this->validate($request, [
                'item_id' => 'required|exists:items,id',
                'harga_jual' => 'required',
                'harga_beli' => 'required',
            ]);
        }

        // * create data
        $model = new model();
        $model->loadModel([
            'item_id' => $request->item_id,
            'harga_jual' => thousand_to_float_commas($request->harga_jual),
            'harga_beli' => thousand_to_float_commas($request->harga_beli),
        ]);

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
    public function show(Request $request, $id, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $model = model::findOrFail($id);

        if ($model->item_id != $item_id) {
            return abort(404);
        }

        return $this->ResponseJsonData($model);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id, $price_id)
    {
        $item = Item::findOrFail($id);
        $model = model::findOrFail($price_id);

        if ($model->item_id != $id) {
            return abort(404);
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

        return redirect()->route("admin.item.show", $item)->with($this->ResponseMessageCRUD(true, 'delete'));
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
            $model = model::where('nama', 'like', "%$request->search%")->orderByDesc('created_at')->limit(10)->get();
        } else {
            $model = model::orderByDesc('created_at')->limit(10)->get();
        }

        return $this->ResponseJsonData($model);
    }
}
