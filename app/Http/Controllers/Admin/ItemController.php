<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ItemExcelExport;
use App\Models\Price;
use Illuminate\Http\Request;
use App\Models\Item as model;
use App\Models\StockMutation;
use Yajra\DataTables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemType;
use App\Models\Unit;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class ItemController extends Controller
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
    protected string $view_folder = 'item';

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
        return view('admin.' . $this->view_folder . '.index');
    }

    /**
     * data_per_types
     *
     * @param string|null $type
     * @return mixed
     */
    public function data_per_type($type)
    {
        $data = model::where('type', $type)->orderByDesc('created_at')->with(['item_category'])->select('items.*');

        return Datatables::of($data)
            ->addIndexColumn()
            ->editColumn('kode', fn($row) => view('components.datatable.detail-link', [
                'field' => $row->kode,
                'row' => $row,
                'main' => $this->view_folder,
            ]))
            ->editColumn('item_category.nama', function ($row) {
                if ($row->item_category) {
                    $url = route('admin.item-category.show', $row->item_category->id);
                    return '<a href="' . $url . '" target="_blank" rel="noopener noreferrer">' . $row->item_category->nama . '</a>';
                }
                return '';
            })
            ->editColumn('status', function ($row) {
                $status = get_item_status()[$row->status];

                $str = '<div class="badge badge-lg badge-' . $status['color'] . '">
                                ' . $status['label'] . ' - ' . $status['text'] . '
                            </div>';

                if (!$row->is_complete) {
                    $str .= '<br> <div class="text-capitalize badge bg-' . complete_status()[$row->is_complete]['color'] . '">' . complete_status()[$row->is_complete]['text'] . '</div>';
                }

                return $str;
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
            ->rawColumns(['action', 'status', 'item_category.nama'])
            ->make(true);
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

        $item = DB::table('items')
            ->where('kode', $request->kode)
            ->whereNotNull('deleted_at');

        $item_category = ItemCategory::find($request->item_category_id);

        if ($item->first()) {
            $old_file = $item->first()->file;
            $this->validate_api($request->all(), model::rules('update'));

            try {
                $this->delete_file($old_file);
            } catch (\Throwable $th) {
                //throw $th;
            }

            try {
                $item->update([
                    'nama' => $request->nama,
                    'deskripsi' => $request->deskripsi,
                    'status' => $request->status,
                    'item_category_id' => $request->item_category_id,
                    'unit_id' => $request->unit_id,
                    'type' => $request->type,
                    'is_complete' => $item_category->is_complete,
                    'deleted_at' => null,
                    'file' => $request->hasFile('file') ? $this->upload_file($request->file('file'), 'item-file') : null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD());
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
            }
        }

        // * validate
        if ($request->ajax()) {
            $this->validate_api($request->all(), model::rules());
        } else {
            $this->validate($request, model::rules());
        }

        if (model::where('nama', $request->nama)->first()) {
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, 'Nama item sudah digunakan', 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Nama item sudah digunakan'))->withInput();
        }

        // * create data

        $model = new model();
        $model->loadModel(array_merge($request->all(), [
            'is_complete' => $item_category->is_complete,
        ]));

        $model->file = $this->upload_file($request->file('file'), 'item-file');

        // * saving and make reponse
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
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
        $model = model::with(['prices', 'unit'])->findOrFail($id);

        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        return view("admin.$this->view_folder.show", compact('model'));
    }


    public function clearValue($item_id, $warehouse_id)
    {
        $query = StockMutation::where('ware_house_id', $warehouse_id);
        if ($item_id) {
            $query->where('item_id', $item_id);
        }
        $query = $query
            ->update([
                'price_unit' => null,
                'subtotal' => null,
                'total' => null,
                'value' => null,
            ]);

        return true;
    }

    public function regenerateValue($item_id = null, $warehouse_id = null)
    {
        DB::beginTransaction();
        try {
            $stocks = StockMutation::where('ware_house_id', $warehouse_id)
                ->orderBy('created_at', 'asc');
            if ($item_id) {
                $stocks->where('item_id', $item_id);
            }
            $stocks = $stocks->get();

            foreach ($stocks as $key => $stock) {
                $stock_before = $stock->dataBefore();
                if (!$stock->subtotal) {
                    if ($stock->in) {
                        $price = $stock->price->harga_beli;
                        $subtotal =  $price * $stock->in;
                    } else {
                        $price = ($stock_before->value ?? 0);
                        $subtotal =  $price * $stock->out;
                    }
                } else {
                    $price = $stock->price_unit;
                    $subtotal = $stock->subtotal;
                }
                $stock->price_unit = $price;
                $stock->subtotal = $subtotal;
                if ($stock->in) {
                    $get_total = ($stock_before->total ?? 0) + $subtotal;
                } else {
                    $get_total = ($stock_before->total ?? 0) - $subtotal;
                }
                if ($get_total < 0) {
                    $get_total = 0;
                }
                $stock->total = $get_total;
                if ($stock->in) {
                    if (($stock->stockBefore() + $stock->in) > 0) {
                        $value = replaceComma($get_total / ($stock->stockBefore() + $stock->in));
                    }
                }
                if ($stock->out) {
                    if (($stock->stockBefore() - $stock->out) > 0) {
                        $value = replaceComma($get_total / ($stock->stockBefore() - $stock->out));
                    }
                }

                $stock->value = $value ?? $stock->dataBefore()->value ?? 0;
                $stock->save();
            }
            DB::commit();
        } catch (\Throwable $th) {
            // DB::rollBack();
            throw $th;
        }
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
        $old_file = $model->file;
        DB::beginTransaction();
        // * validate
        if ($request->ajax()) {
            $this->validate_api($request->all(), model::rules('update', $id));
        } else {
            $this->validate($request, model::rules('update', $id));
        }
        // * update data
        $item_category = ItemCategory::find($request->item_category_id);
        $model->loadModel(array_merge($request->all(), [
            'is_complete' => $item_category->is_complete,
        ]));

        if (null !== $request->file('file')) {
            try {
                $this->delete_file($old_file);
            } catch (\Throwable $th) {
                //throw $th;
            }
            $model->file = $this->upload_file($request->file('file'), 'item-file');
        }


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
            return $this->ResponseJsonMessageCRUD(true, 'edit');
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
        if ($model->stock_mutations()->exists()) {
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'delete', null, 'Item telah memiliki transaksi', 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, 'Item telah memiliki transaksi'));
        }
        DB::beginTransaction();
        try {
            if ($model->file) {
                $this->delete_file($model->file);
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
        $model = model::where('items.is_complete', true)
            ->when($request->search, function ($query) use ($request) {
                return $query->where(function ($query) use ($request) {
                    $query->where('nama', 'like', "%$request->search%")
                        ->orWhere('kode', 'like', "%$request->search%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return $this->ResponseJson($model);
    }

    public function export_item_excel()
    {
        $type = request()->input('type');

        return Excel::download(new ItemExcelExport($type), 'Item.xlsx');
    }

    /**
     * select general item
     *
     * @param Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function select_general(Request $request)
    {
        $model = model::where('items.is_complete', true)
            ->when($request->search, function ($query) use ($request) {
                return $query->where(function ($query) use ($request) {
                    $query->where('nama', 'like', "%$request->search%")
                        ->orWhere('kode', 'like', "%$request->search%");
                });
            })
            ->where('type', 'general')
            ->orderByDesc('created_at')
            ->paginate(10);

        return $this->ResponseJson($model);
    }

    /**
     * select trading item
     *
     * @param Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function select_trading(Request $request)
    {
        $model = model::where('items.is_complete', true)
            ->when($request->search, function ($query) use ($request) {
                return $query->where(function ($query) use ($request) {
                    $query->where('nama', 'like', "%$request->search%")
                        ->orWhere('kode', 'like', "%$request->search%");
                });
            })
            ->where('type', 'trading')
            ->orderByDesc('created_at')
            ->paginate(10);

        return $this->ResponseJson($model);
    }

    /**
     * select api for each item type
     *
     * @param Request $request
     * @param string|null $type
     */
    public function select_by_type(Request $request, $type = null)
    {
        $model = model::where('items.is_complete', true)
            ->where('items.status', 'active')
            ->where(function ($query) use ($request) {
                $query->where('nama', 'like', "%$request->search%");
                $query->orWhere('kode', 'like', "%$request->search%");
            })
            ->when($request->item_types, function ($query) use ($request) {
                $item_types = explode(',', $request->item_types);
                $query->whereHas('item_category.item_type', function ($query) use ($item_types) {
                    $query->whereIn('nama', $item_types);
                });
            })
            ->when($type && $type != 'all', function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when($request->not_in_ids, fn($q) => $q->whereNotIn('id', $request->not_in_ids))
            ->orderByDesc('created_at')
            ->paginate(10);

        return $this->ResponseJson($model);
    }

    /**
     * sale_order_price_history
     *
     * @param int|null $id
     * @return mixed
     */
    public function sale_order_price_history($id = null)
    {
        $model = model::findOrFail($id);
        // $sales_order = SoTrafing
        return $this->ResponseJsonData($model);
    }

    /**
     * latest_price
     *
     * @param int|null $id = null
     * @return mixed
     */
    public function latest_price(int|null $id = null)
    {
        $price = Price::where('item_id', $id)->orderByDesc('created_at')->first();
        return $this->ResponseJsonData($price);
    }

    /**
     * get item unit
     *
     * @param Request $request
     * @param int|null $id
     */
    public function item_unit(Request $request, $id = null)
    {
        if ($request->ajax()) {
            $model = model::with(['unit'])->findOrFail($id);
            return $this->ResponseJsonData($model);
        }
    }

    /**
     * Select item for purchase request
     *
     * @param Request $request
     * @param string $type Item
     * @return \Illuminate\Http\JsonResponse
     */
    public function select_for_purchase_request(Request $request, $type = null)
    {
        $model = model::where('items.is_complete', true)
            ->leftJoin('item_categories', 'item_categories.id', '=', 'items.item_category_id')
            ->leftJoin('item_types', 'item_types.id', '=', 'item_categories.item_type_id')
            ->where(function ($query) use ($request) {
                $query->where('items.nama', 'like', "%$request->search%");
                $query->orWhere('items.kode', 'like', "%$request->search%");
            })
            ->where('items.type', $type)
            ->when($type == 'general', function ($query) {
                $query->whereIn('item_types.nama', ['purchase item', 'asset', 'biaya dibayar dimuka']);
            })
            ->when($type == 'service', function ($query) {
                $query->whereIn('item_types.nama', ['service', 'biaya dibayar dimuka']);
            })
            ->orderByDesc('items.created_at')
            ->select('items.*')
            ->limit(10)
            ->get();

        return $this->ResponseJsonData($model);
    }

    /**
     *
     */
    public function viewImport()
    {
        return view("admin.item.import.index");
    }

    /**
     *
     */
    public function importFormat()
    {
        return $this->ResponseDownload(public_path("import/admin/item-import-format.xlsx"));
    }

    /**
     *
     */
    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:xlsx',
        ]);

        $the_file = $request->file('file');

        // * Load the file
        $spreadsheet = IOFactory::load($the_file->getRealPath());

        // * Get the active sheet
        $sheet = $spreadsheet->getActiveSheet();

        // * Get the highest row and column
        $row_limit = $sheet->getHighestDataRow();
        $row_range = range(2, $row_limit);

        // * Prepare the data
        $data = array();

        // * Looping through the rows
        foreach ($row_range as $row) {
            // check the column is null or not
            if (!is_null($sheet->getCell("A" . $row)->getValue()) && !is_null($sheet->getCell("B" . $row)->getValue())) {
                $data[] = [
                    'kode' => trim($sheet->getCell("A" . $row)->getValue()),
                    'nama' => trim($sheet->getCell("B" . $row)->getValue()),
                    'deskripsi' => trim($sheet->getCell("C" . $row)->getValue()),
                    'status' => "active",
                    'item_category' => trim($sheet->getCell("D" . $row)->getValue()),
                    'unit' => trim($sheet->getCell("E" . $row)->getValue()),
                    'type' => trim($sheet->getCell("F" . $row)->getValue()),
                    'item_type' => strtolower(trim($sheet->getCell("G" . $row)->getValue())),
                ];
            }
        }

        $itemCategories = DB::table('item_categories')
            ->whereNull('deleted_at')
            ->get();

        $item_types = DB::table('item_types')
            ->whereNull('deleted_at')
            ->get();

        $itemTypes = [
            'general' => 'General',
            'trading' => 'Trading',
            'service' => 'Service',
            'transport' => 'Transport',
        ];

        $items = DB::table('items')
            ->whereNull('deleted_at')
            ->get();

        $units = DB::table('units')
            ->whereNull('deleted_at')
            ->get();

        $results = collect($data)->map(function ($item) use ($itemTypes, $item_types, $itemCategories, $items, $units) {

            $findItem = $items->filter(function ($item_data) use ($item) {
                return strtolower($item_data->nama) == strtolower($item['nama']);
            })
                ->first();

            if ($findItem) {
                $item['is_exists'] = true;
                $item['id'] = $findItem->id;
            } else {
                $item['is_exists'] = false;
                $item['id'] = null;
            }

            if (!in_array($item['type'], array_keys($itemTypes))) {
                $item['type'] = 'general';
            }

            $itemCategory  = $itemCategories
                ->where('nama', $item['item_category'])
                ->first();

            $itemType = $item_types->where('id', $itemCategory->item_type_id)
                ->first();

            if ($itemType) {
                $item['item_type_id'] = $itemType->id;
                $item['item_type_data'] = $itemType;
            } else {
                $item['item_type'] = null;
            }

            $item['item_type'] = $itemType->nama;
            $item['item_type_data'] = $itemType;

            if ($itemCategory) {
                $item['item_category_id'] = $itemCategory->id;
                $item['item_category_data'] = $itemCategory;
            } else {
                $item['item_category_id'] = null;
            }

            $unit = $units->filter(function ($unit) use ($item) {
                return $unit->name == $item['unit'] or  $unit->sort == $item['unit'];
            })->first();

            if ($unit) {
                $item['unit_id'] = $unit->id;
                $item['unit_data'] = $unit;
            } else {
                $item['unit_id'] = $item['unit'];
            }

            return $item;
        });

        $results = $results->filter(function ($item) {
            return !is_null($item);
        });

        return view("admin.item.import.show", compact('results'));
    }

    /**
     *
     */
    public function importStore(Request $request)
    {
        DB::beginTransaction();

        $itemCategories = DB::table('item_categories')
            ->whereNull('deleted_at')
            ->get();

        $itemCategoryCoas = DB::table('item_category_coas')
            ->whereIn('item_category_id', $itemCategories->pluck('id')->toArray())
            ->get();

        $items = DB::table('items')
            ->get();

        $items_data = Item::all();

        $units = DB::table('units')
            ->whereNull('deleted_at')
            ->get();

        $dataItems = [];

        foreach ($request->code as $key =>  $item) {
            $findItem = $items_data->filter(function ($item_data) use ($request, $key) {
                return strtolower($item_data->nama) == strtolower($request->name[$key]);
            })
                ->first();

            if ($findItem) {
                $findItem->kode = $request->code[$key];
                $findItem->save();
            }

            $findItemCode = $items->where('kode', $request->code[$key])->whereNotNull('deleted_at')->first();

            if (!$findItem) {

                $unit = $units->where('id', $request->unit_id[$key])->first();

                if (!$unit) {
                    $unit = Unit::updateOrCreate([
                        'name' => $request->unit_id[$key],
                    ], [
                        'name' => $request->unit_id[$key],
                        'sort' => $request->unit_id[$key],
                    ]);

                    $units = DB::table('units')
                        ->whereNull('deleted_at')
                        ->get();
                }


                $itemCategory  = $itemCategories->filter(function ($item_category) use ($request, $key) {
                    return strstr($item_category->id, $request->item_category_id[$key]) ||
                        strstr($item_category->nama, $request->item_category_id[$key]);
                })->first();

                if (!$itemCategory) {
                    $item_type = ItemType::where('nama', $request->item_type[$key])->first();
                    $itemCategory = new \App\Models\ItemCategory();
                    $itemCategory->fill([
                        'nama' => $request->item_category_id[$key],
                        'remark' => $request->item_category_id[$key],
                        'item_type_id' => $item_type->id,
                    ]);

                    try {
                        $itemCategory->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        return redirect()->route("admin.item.view-import")->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
                    }

                    $itemCategories = DB::table('item_categories')
                        ->whereNull('deleted_at')
                        ->get();

                    $itemCategoryCoas = DB::table('item_category_coas')
                        ->whereIn('item_category_id', $itemCategories->pluck('id')->toArray())
                        ->get();
                }


                $dataItems[] = [
                    'kode' => $request->code[$key],
                    'nama' => $request->name[$key],
                    'deskripsi' => $request->description[$key] ?? '',
                    'status' => $request->status[$key],
                    'item_category_id' => $itemCategory->id,
                    'unit_id' => $unit->id,
                    'type' => $request->type[$key],
                    'is_complete' => $itemCategoryCoas->where('item_category_id', $itemCategory->id)->count() > 0 ? true : false,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }

            if ($findItemCode) {
                if (!is_null($findItemCode->deleted_at)) {
                    $unit = $units->where('id', $request->unit_id[$key])->first();

                    if (!$unit) {
                        $unit = new \App\Models\Unit();
                        $unit->fill([
                            'name' => $request->unit_id[$key],
                            'sort' => $request->unit_id[$key],
                        ]);

                        try {
                            $unit->save();
                        } catch (\Throwable $th) {
                            DB::rollBack();

                            return redirect()->route("admin.item.view-import")->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
                        }

                        $units = DB::table('units')
                            ->whereNull('deleted_at')
                            ->get();
                    }


                    $itemCategory  = $itemCategories
                        ->where('id', $request->item_category_id[$key])
                        ->first();

                    if (!$itemCategory) {
                        $itemCategory = new \App\Models\ItemCategory();
                        $itemCategory->fill([
                            'nama' => $request->item_category_id[$key],
                            'remark' => $request->item_category_id[$key],
                        ]);

                        try {
                            $itemCategory->save();
                        } catch (\Throwable $th) {
                            DB::rollBack();
                            return redirect()->route("admin.item.view-import")->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
                        }

                        $itemCategories = DB::table('item_categories')
                            ->whereNull('deleted_at')
                            ->get();

                        $itemCategoryCoas = DB::table('item_category_coas')
                            ->whereIn('item_category_id', $itemCategories->pluck('id')->toArray())
                            ->get();
                    }

                    if (!is_null($findItemCode->deleted_at)) {

                        try {
                            $this->delete_file($findItemCode->file);
                        } catch (\Throwable $th) {
                            //throw $th;
                        }

                        try {
                            DB::table('items')->where('id', $findItemCode->id)->update([
                                'nama' => $request->name[$key],
                                'deskripsi' => $request->description[$key],
                                'status' => $request->status[$key],
                                'item_category_id' => $itemCategory->id,
                                'unit_id' => $unit->id,
                                'type' => $request->type[$key],
                                'is_complete' => $itemCategoryCoas->where('item_category_id', $itemCategory->id)->count() > 0 ? true : false,
                                'deleted_at' => null,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);

                            unset($dataItems[$key]);
                        } catch (Throwable $th) {
                            DB::rollBack();
                            return redirect()->route("admin.item.view-import")->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
                        }
                    }
                }
            }
        }

        if (count($dataItems) > 0) {
            try {
                DB::table('items')->insert($dataItems);
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->route("admin.item.view-import")->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
            }
        }

        DB::commit();

        return redirect()->route("admin.item.index")->with($this->ResponseMessageCRUD(true, 'import', "success impotr user data"));
    }
}
