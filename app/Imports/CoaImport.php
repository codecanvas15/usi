<?php

namespace App\Imports;

use App\Models\Coa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CoaImport implements ToCollection, WithHeadingRow
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
            $parent = null;
            if ($row['parent_code']) {
                $parent = Coa::where('account_code', $row['parent_code'])
                    ->orderByDesc('id')
                    ->orderByDesc('created_at')
                    ->first();
            }

            $coa = Coa::where('account_code', $row['account_number'])
                ->orderByDesc('id')
                ->orderByDesc('created_at')
                ->first();

            if ($coa) {
                $can_have_children = true;
                if ($parent) {
                    if ($parent?->parent != null) {
                        $can_have_children = false;
                    }
                }

                if (!$parent) {
                    $is_parent = true;
                } else {
                    $is_parent = false;
                    // * update parent
                    if ($parent) {
                        if (!$parent->is_parent) {
                            $parent->is_parent = true;
                            $parent->save();
                        }
                    }
                }

                if (in_array($row['account_category'], [
                    'pasiva',
                    'equity',
                    'revenue',
                ])) {
                    $normal_balance = 'credit';
                } else {
                    $normal_balance = 'debit';
                }

                DB::table('coas')
                    ->where('id', $coa->id)
                    ->update([
                        'account_code' => $row['account_number'],
                        'name' => $row['name'],
                        'account_type' => $row['account_type'],
                        'account_category' => $row['account_category'],
                        'parent_id' => $parent->id ?? null,
                        'updated_at' => now(),
                        'can_have_children' => $can_have_children,
                        'is_parent' => $is_parent,
                        'normal_balance' => $normal_balance,
                    ]);
            } else {
                $can_have_children = true;
                if ($parent) {
                    if ($parent?->parent != null) {
                        $can_have_children = false;
                    }
                }

                if (!$parent) {
                    $is_parent = true;
                } else {
                    $is_parent = false;
                    // * update parent
                    if ($parent) {
                        if (!$parent->is_parent) {
                            $parent->is_parent = true;
                            $parent->save();
                        }
                    }
                }

                if (in_array($row['account_category'], [
                    'pasiva',
                    'equity',
                    'revenue',
                ])) {
                    $normal_balance = 'credit';
                } else {
                    $normal_balance = 'debit';
                }

                DB::table('coas')
                    ->insert([
                        'account_code' => $row['account_number'],
                        'name' => $row['name'],
                        'account_type' => $row['account_type'],
                        'account_category' => $row['account_category'],
                        'parent_id' => $parent->id ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'can_have_children' => $can_have_children,
                        'is_parent' => $is_parent,
                        'normal_balance' => $normal_balance,
                    ]);
            }

            // $coa = Coa::where('account_code', $row['account_code'])
            //     ->first();
            // if ($coa) {
            //     $account_code = str_replace('.', '', $row['account_code']);
            //     $coa->account_code = $account_code;
            //     if ($row['parent_code']) {
            //         $parent_code = str_replace('.', '', $row['parent_code']);
            //         $parent = Coa::where('account_code', $parent_code)
            //             ->first();

            //         if ($parent) {
            //             $coa->parent_id = $parent->id;
            //             $coa->is_parent = 0;

            //             $parent->is_parent = 1;
            //             $parent->save();
            //         }
            //     } else {
            //         $coa->is_parent = 1;
            //         $coa->parent_id = null;
            //     }
            //     $coa->can_have_children = 1;
            //     $coa->save();
            // }
        }
    }
}
