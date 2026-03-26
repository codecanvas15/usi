<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AssetCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('asset_categories')->delete();
        
        \DB::table('asset_categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Level 1',
                'percentage' => '0.00',
                'deleted_at' => '2023-10-04 21:33:15',
                'created_at' => NULL,
                'updated_at' => '2023-10-04 21:33:15',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Level 2',
                'percentage' => '0.00',
                'deleted_at' => '2023-10-04 21:33:21',
                'created_at' => NULL,
                'updated_at' => '2023-10-04 21:33:21',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Level 3',
                'percentage' => '0.00',
                'deleted_at' => '2023-10-04 21:33:26',
                'created_at' => NULL,
                'updated_at' => '2023-10-04 21:33:26',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Level 4',
                'percentage' => '0.00',
                'deleted_at' => '2023-10-04 21:33:30',
                'created_at' => NULL,
                'updated_at' => '2023-10-04 21:33:30',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Kelompok 1',
                'percentage' => '25.00',
                'deleted_at' => NULL,
                'created_at' => '2023-10-04 21:29:21',
                'updated_at' => '2023-10-04 21:29:21',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Kelompok 2',
                'percentage' => '6.25',
                'deleted_at' => NULL,
                'created_at' => '2023-10-04 21:32:42',
                'updated_at' => '2024-04-03 15:25:26',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Kelompok 3',
                'percentage' => '6.25',
                'deleted_at' => NULL,
                'created_at' => '2023-10-04 21:32:59',
                'updated_at' => '2023-10-04 21:32:59',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Kelompok 4',
                'percentage' => '5.00',
                'deleted_at' => NULL,
                'created_at' => '2023-10-04 21:33:10',
                'updated_at' => '2023-10-04 21:33:10',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Bangunan Permanen',
                'percentage' => '5.00',
                'deleted_at' => NULL,
                'created_at' => '2023-10-04 21:33:57',
                'updated_at' => '2023-10-04 21:33:57',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Bangunan Non Permanen',
                'percentage' => '10.00',
                'deleted_at' => NULL,
                'created_at' => '2023-10-04 21:34:11',
                'updated_at' => '2023-10-04 21:34:11',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Tanah',
                'percentage' => '0.00',
                'deleted_at' => NULL,
                'created_at' => '2023-10-10 22:28:43',
                'updated_at' => '2023-10-10 22:28:43',
            ),
        ));
        
        
    }
}