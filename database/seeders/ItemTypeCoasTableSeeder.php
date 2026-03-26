<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItemTypeCoasTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('item_type_coas')->delete();
        
        \DB::table('item_type_coas')->insert(array (
            0 => 
            array (
                'id' => 30,
                'item_type_id' => 2,
                'coa_id' => NULL,
                'type' => 'Sales',
                'created_at' => '2023-03-15 22:57:05',
                'updated_at' => '2023-03-15 22:57:05',
            ),
            1 => 
            array (
                'id' => 31,
                'item_type_id' => 2,
                'coa_id' => NULL,
                'type' => 'Expense',
                'created_at' => '2023-03-15 22:57:05',
                'updated_at' => '2023-03-15 22:57:05',
            ),
            2 => 
            array (
                'id' => 32,
                'item_type_id' => 3,
                'coa_id' => NULL,
                'type' => 'Asset',
                'created_at' => '2023-03-15 22:57:28',
                'updated_at' => '2023-03-15 22:57:28',
            ),
            3 => 
            array (
                'id' => 33,
                'item_type_id' => 4,
                'coa_id' => NULL,
                'type' => 'biaya dibayar dimuka',
                'created_at' => '2023-05-17 18:18:14',
                'updated_at' => '2023-05-17 18:18:14',
            ),
            4 => 
            array (
                'id' => 34,
                'item_type_id' => 1,
                'coa_id' => NULL,
                'type' => 'Sales Return',
                'created_at' => '2023-05-29 18:11:43',
                'updated_at' => '2023-05-29 18:11:43',
            ),
            5 => 
            array (
                'id' => 35,
                'item_type_id' => 1,
                'coa_id' => NULL,
                'type' => 'Hpp',
                'created_at' => '2023-05-29 18:11:43',
                'updated_at' => '2023-05-29 18:11:43',
            ),
            6 => 
            array (
                'id' => 36,
                'item_type_id' => 1,
                'coa_id' => NULL,
                'type' => 'Work In Progress',
                'created_at' => '2023-05-29 18:11:43',
                'updated_at' => '2023-05-29 18:11:43',
            ),
            7 => 
            array (
                'id' => 37,
                'item_type_id' => 1,
                'coa_id' => NULL,
                'type' => 'Inventory',
                'created_at' => '2023-05-29 18:11:43',
                'updated_at' => '2023-05-29 18:11:43',
            ),
            8 => 
            array (
                'id' => 38,
                'item_type_id' => 1,
                'coa_id' => NULL,
                'type' => 'Sales',
                'created_at' => '2023-05-29 18:11:43',
                'updated_at' => '2023-05-29 18:11:43',
            ),
            9 => 
            array (
                'id' => 39,
                'item_type_id' => 1,
                'coa_id' => NULL,
                'type' => 'Purchase Inventory Return',
                'created_at' => '2023-05-29 18:11:43',
                'updated_at' => '2023-05-29 18:11:43',
            ),
            10 => 
            array (
                'id' => 40,
                'item_type_id' => 1,
                'coa_id' => NULL,
                'type' => 'Expense',
                'created_at' => '2023-05-29 18:11:43',
                'updated_at' => '2023-05-29 18:11:43',
            ),
            11 => 
            array (
                'id' => 41,
                'item_type_id' => 1,
                'coa_id' => NULL,
                'type' => 'goods_in_transit',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}