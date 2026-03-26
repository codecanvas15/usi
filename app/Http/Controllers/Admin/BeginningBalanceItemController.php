<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Admin\BeginningBalanceItem;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Price;
use App\Models\StockMutation;
use App\Models\WareHouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BeginningBalanceItemController extends Controller
{
    /**
     * Display form for importing data
     */
    public function index()
    {
        return view('admin.item-beginning-balance.index');
    }

    /**
     * Download import format
     */
    public function importFormat(Request $request)
    {
        $this->validate($request, [
            'ware_house_id' => 'required|exists:ware_houses,id',
        ]);

        $data['models'] = Item::leftJoin('item_categories', 'items.item_category_id', '=', 'item_categories.id')
            ->leftJoin('item_types', 'item_categories.item_type_id', '=', 'item_types.id')
            ->where('item_types.nama', 'purchase item')
            ->whereIn('type', ['general', 'trading'])
            ->with(['item_category', 'unit'])
            ->select('items.*')
            ->get();

        $data['warehouses'] = WareHouse::whereIn('type', ['general', 'trading'])
            ->where('id', $request->ware_house_id)
            ->get();

        return Excel::download(new BeginningBalanceItem($data), 'item-beginning-import-format.xlsx');
    }

    /**
     * Upload and import the make preview the imported data
     */
    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx'
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

        $itemCategories = DB::table('item_categories')
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

        // * Looping through the rows
        foreach ($row_range as $row) {
            $quantity = thousand_to_float($sheet->getCell('I' . $row)->getValue() ?? '0');
            $sell_price = thousand_to_float($sheet->getCell('J' . $row)->getValue() ?? '0');
            $buy_price = thousand_to_float($sheet->getCell('K' . $row)->getValue() ?? '0');

            // if ($quantity > 0 or $sell_price  > 0 or $buy_price > 0) {
            $data[] = [
                'ware_house_id' => $sheet->getCell('A' . $row)->getValue(),
                'ware_house_name' => $sheet->getCell('B' . $row)->getValue(),
                'nama' => $sheet->getCell('C' . $row)->getValue(),
                'kode' => $sheet->getCell('D' . $row)->getValue(),
                'deskripsi' => $sheet->getCell('E' . $row)->getValue(),
                'status' => "active",
                'item_category' => $sheet->getCell('F' . $row)->getValue(),
                'unit' => $sheet->getCell('G' . $row)->getValue(),
                'type' => $sheet->getCell('H' . $row)->getValue(),
                'quantity' => $sheet->getCell('I' . $row)->getValue(),
                'sell_price' => $sheet->getCell('J' . $row)->getValue(),
                'buy_price' => $sheet->getCell('K' . $row)->getValue(),
            ];
            // }
        }

        $results = collect($data)->map(function ($item) use ($itemTypes, $itemCategories, $items, $units) {
            if (!in_array($item['type'], array_keys($itemTypes))) {
                $item['type'] = 'general';
            }

            $itemCategory  = $itemCategories
                ->where('nama', $item['item_category'])
                ->first();

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
                $item['unit_id'] = null;
            }

            return $item;
        });

        // * Return the data
        return view('admin.item-beginning-balance.preview', compact('results'));
    }

    /**
     * Store the imported data
     */
    public function store(Request $request)
    {
        // * Get the warehouses
        $warehouses = WareHouse::whereIn('id', $request->ware_house_id)->get();

        DB::beginTransaction();

        $itemCategories = DB::table('item_categories')
            ->whereNull('deleted_at')
            ->get();

        $itemCategoryCoas = DB::table('item_category_coas')
            ->whereIn('item_category_id', $itemCategories->pluck('id')->toArray())
            ->get();

        $items = DB::table('items')
            ->get();

        $units = DB::table('units')
            ->whereNull('deleted_at')
            ->get();

        // * Looping through the data
        foreach ($request->ware_house_id as $key => $value) {

            $findItem = $items->where('nama', removeSpecialChar($request->name[$key]))
                ->where('kode', removeSpecialChar($request->code[$key]))
                ->whereNull('deleted_at')
                ->first();

            $findItemDeleteNotNull = DB::table('items')
                ->where('kode', removeSpecialChar($request->code[$key]))
                ->first();

            $buy_price = thousand_to_float($request->buy_price[$key]);
            $sell_price = thousand_to_float($request->sell_price[$key]);
            $quantity = thousand_to_float($request->quantity[$key]);

            // if ($quantity <= 0) {
            //     continue;
            // }

            // if ($buy_price <= 0) {
            //     continue;
            // }

            // if ($sell_price <= 0) {
            //     continue;
            // }

            // * If the item not found create new
            if (!$findItem) {


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

                $dataItems = [
                    'kode' => $request->code[$key],
                    'nama' => $request->name[$key],
                    'deskripsi' => $request->description[$key],
                    'status' => $request->status[$key],
                    'item_category_id' => $itemCategory->id,
                    'unit_id' => $unit->id,
                    'type' => $request->type[$key],
                    'is_complete' => $itemCategoryCoas->where('item_category_id', $itemCategory->id)->count() > 0 ? true : false,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];

                if (!empty($findItemDeleteNotNull)) {
                    $item = $findItemDeleteNotNull;
                    if (!is_null($findItemDeleteNotNull->deleted_at)) {
                        $priceValue = \App\Models\Price::where('item_id', $item->id);
                        try {
                            $priceValue->delete();
                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                    }
                    try {
                        $this->delete_file($findItemDeleteNotNull->file);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }

                    try {
                        DB::table('items')->where('id', $findItemDeleteNotNull->id)->update([
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

                        $dataItems = [];
                    } catch (\Throwable $th) {
                    }
                } else {
                    $item = new \App\Models\Item();
                    $item->fill($dataItems);

                    try {
                        $item->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        return redirect()->route("admin.item.beginning-balance.index")->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
                    }
                }

                $items = DB::table('items')
                    ->whereNull('deleted_at')
                    ->get();

                // * Create price
                $price = new Price();
                $price->fill([
                    'item_id' => $item->id,
                    'harga_beli' => $buy_price,
                    'harga_jual' => $sell_price,
                ]);

                try {
                    $price->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    throw $th;
                }

                // * Get the stock before
                $stockBefore = StockMutation::where('item_id', $item->id)->latest('id')->first();
                $stockBeforeAllIn = StockMutation::where('item_id', $item->id)->sum('in');
                $stockBeforeAllOut = StockMutation::where('item_id', $item->id)->sum('out');

                // * Get the total before
                $totalBefore = $stockBefore ? $stockBefore->total : 0;
                $finalStock = $stockBeforeAllIn - $stockBeforeAllOut + $quantity;

                // * Create stock mutation
                $stock_mutation = new StockMutation();
                $stock_mutation->fill([
                    'ware_house_id' => $request->ware_house_id[$key],
                    'branch_id' => $warehouses->where('id', $request->ware_house_id[$key])->first()->branch_id,
                    'item_id' => $item->id,
                    'price_id' => $price->id,
                    'type' => 'beginning balance',
                    'in' => $quantity,
                    'note' => 'Beginning Balance',
                    'price_unit' => $buy_price,
                    'subtotal' => $quantity * $buy_price,
                    'total' => $totalBefore + ($quantity * $buy_price),
                    'value' => $finalStock != 0 ? (($totalBefore + ($quantity * $buy_price)) / $finalStock) : 0,
                    'date' => Carbon::now(),
                ]);

                try {
                    $stock_mutation->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    throw $th;
                }
            } else {

                // * Create price
                $price = new Price();
                $price->fill([
                    'item_id' => $findItem->id,
                    'harga_beli' => $buy_price,
                    'harga_jual' => $sell_price,
                ]);

                try {
                    $price->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    throw $th;
                }

                // * Get the stock before
                $stockBefore = StockMutation::where('item_id', $findItem->id)->latest('id')->first();
                $stockBeforeAllIn = StockMutation::where('item_id', $findItem->id)->sum('in');
                $stockBeforeAllOut = StockMutation::where('item_id', $findItem->id)->sum('out');

                // * Get the total before
                $totalBefore = $stockBefore ? $stockBefore->total : 0;
                $finalStock = $stockBeforeAllIn - $stockBeforeAllOut + $quantity;

                // * Create stock mutation
                $stock_mutation = new StockMutation();
                $stock_mutation->fill([
                    'ware_house_id' => $request->ware_house_id[$key],
                    'branch_id' => $warehouses->where('id', $request->ware_house_id[$key])->first()->branch_id,
                    'item_id' => $findItem->id,
                    'price_id' => $price->id,
                    'type' => 'beginning balance',
                    'in' => $quantity,
                    'note' => 'Beginning Balance',
                    'price_unit' => $buy_price,
                    'subtotal' => $quantity * $buy_price,
                    'total' => $totalBefore + ($quantity * $buy_price),
                    'value' => $finalStock != 0 ? (($totalBefore + ($quantity * $buy_price)) / $finalStock) : 0,
                    'date' => Carbon::now(),
                ]);

                try {
                    $stock_mutation->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    throw $th;
                }
            }
        }

        DB::commit();

        return redirect()->route('admin.item.index')->with($this->ResponseMessageCRUD(true, 'import', 'Successfully imported data'));
    }
}
