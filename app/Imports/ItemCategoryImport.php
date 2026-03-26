<?php

namespace App\Imports;

use App\Models\Coa;
use App\Models\ItemCategory;
use App\Models\ItemCategoryCoa;
use App\Models\ItemType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemCategoryImport implements ToCollection, WithHeadingRow
{
    /**
     * collection
     *
     * @param mixed $rows
     * @return void
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {
            if ($row['type'] != '') {
                $item_type = ItemType::where('nama', $row['type'])->first();
                if ($item_type) {
                    $item_category = ItemCategory::updateOrCreate([
                        'item_type_id' => $item_type->id,
                        'nama' => $row['name'],
                    ], [
                        'item_type_id' => $item_type->id,
                        'nama' => $row['name'],
                        'remark' => $row['remark'],
                    ]);

                    if ($item_type->nama == 'purchase item') {
                        // Inventory
                        if ($row['coa_1'] != '') {
                            ItemCategoryCoa::create([
                                'item_category_id' => $item_category->id,
                                'coa_id' => Coa::where('account_code', $row['coa_1'])->first()->id ?? null,
                                'type' => 'Inventory',
                            ]);
                        }

                        // Sales
                        if ($row['coa_2'] != '') {
                            ItemCategoryCoa::create([
                                'item_category_id' => $item_category->id,
                                'coa_id' => Coa::where('account_code', $row['coa_2'])->first()->id ?? null,
                                'type' => 'Sales',
                            ]);
                        }

                        // Work In Progress
                        if ($row['coa_3'] != '') {
                            ItemCategoryCoa::create([
                                'item_category_id' => $item_category->id,
                                'coa_id' => Coa::where('account_code', $row['coa_3'])->first()->id ?? null,
                                'type' => 'Work In Progress',
                            ]);
                        }

                        // Hpp
                        if ($row['coa_4'] != '') {
                            ItemCategoryCoa::create([
                                'item_category_id' => $item_category->id,
                                'coa_id' => Coa::where('account_code', $row['coa_4'])->first()->id ?? null,
                                'type' => 'Hpp',
                            ]);
                        }

                        // Sales Return
                        if ($row['coa_5'] != '') {
                            ItemCategoryCoa::create([
                                'item_category_id' => $item_category->id,
                                'coa_id' => Coa::where('account_code', $row['coa_5'])->first()->id ?? null,
                                'type' => 'Sales Return',
                            ]);
                        }

                        // Expense
                        if ($row['coa_6'] != '') {
                            ItemCategoryCoa::create([
                                'item_category_id' => $item_category->id,
                                'coa_id' => Coa::where('account_code', $row['coa_6'])->first()->id ?? null,
                                'type' => 'Expense',
                            ]);
                        }


                        // Purchase Inventory Return
                        if ($row['coa_7'] != '') {
                            ItemCategoryCoa::create([
                                'item_category_id' => $item_category->id,
                                'coa_id' => Coa::where('account_code', $row['coa_7'])->first()->id ?? null,
                                'type' => 'Purchase Inventory Return',
                            ]);
                        }

                        // Goods in Transit
                        if ($row['coa_8'] != '') {
                            ItemCategoryCoa::create([
                                'item_category_id' => $item_category->id,
                                'coa_id' => Coa::where('account_code', $row['coa_8'])->first()->id ?? null,
                                'type' => 'goods_in_transit',
                            ]);
                        }
                    }

                    if ($item_type->nama == 'service') {
                        // Expense
                        if ($row['coa_1'] != '') {
                            ItemCategoryCoa::create([
                                'item_category_id' => $item_category->id,
                                'coa_id' => Coa::where('account_code', $row['coa_1'])->first()->id ?? null,
                                'type' => 'Expense',
                            ]);
                        }

                        // Sales
                        if ($row['coa_2'] != '') {
                            ItemCategoryCoa::create([
                                'item_category_id' => $item_category->id,
                                'coa_id' => Coa::where('account_code', $row['coa_2'])->first()->id ?? null,
                                'type' => 'Sales',
                            ]);
                        }
                    }

                    if ($item_type->nama == 'asset') {
                        // Asset
                        if ($row['coa_1'] != '') {
                            ItemCategoryCoa::create([
                                'item_category_id' => $item_category->id,
                                'coa_id' => Coa::where('account_code', $row['coa_1'])->first()->id ?? null,
                                'type' => 'Asset',
                            ]);
                        }
                    }

                    if ($item_type->nama == 'biaya dibayar dimuka') {
                        // biaya dibayar dimuka
                        if ($row['coa_1'] != '') {
                            ItemCategoryCoa::create([
                                'item_category_id' => $item_category->id,
                                'coa_id' => Coa::where('account_code', $row['coa_1'])->first()->id ?? null,
                                'type' => 'biaya dibayar dimuka',
                            ]);
                        }
                    }
                }
            }
        }
    }
}
