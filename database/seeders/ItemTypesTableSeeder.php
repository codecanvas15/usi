<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItemTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('item_types')->delete();
        
        \DB::table('item_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'nama' => 'purchase item',
                'deleted_at' => NULL,
                'created_at' => '2022-12-09 16:25:36',
                'updated_at' => '2022-12-09 16:25:36',
            ),
            1 => 
            array (
                'id' => 2,
                'nama' => 'service',
                'deleted_at' => NULL,
                'created_at' => '2022-12-09 16:25:36',
                'updated_at' => '2022-12-09 16:25:36',
            ),
            2 => 
            array (
                'id' => 3,
                'nama' => 'asset',
                'deleted_at' => NULL,
                'created_at' => '2022-12-09 16:25:36',
                'updated_at' => '2022-12-09 16:25:36',
            ),
            3 => 
            array (
                'id' => 4,
                'nama' => 'biaya dibayar dimuka',
                'deleted_at' => NULL,
                'created_at' => '2022-12-09 16:25:36',
                'updated_at' => '2022-12-09 16:25:36',
            ),
        ));
        
        
    }
}