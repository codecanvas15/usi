<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DivisionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('divisions')->delete();
        
        \DB::table('divisions')->insert(array (
            0 => 
            array (
                'id' => 3,
                'name' => 'HRD & GA',
                'deleted_at' => NULL,
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2023-09-20 21:54:09',
            ),
            1 => 
            array (
                'id' => 4,
                'name' => 'Purchase',
                'deleted_at' => NULL,
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2022-12-16 09:05:35',
            ),
            2 => 
            array (
                'id' => 5,
                'name' => 'Finance',
                'deleted_at' => NULL,
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2022-12-16 09:05:35',
            ),
            3 => 
            array (
                'id' => 8,
                'name' => 'Tax',
                'deleted_at' => NULL,
                'created_at' => '2023-01-17 16:19:40',
                'updated_at' => '2023-01-17 16:19:40',
            ),
            4 => 
            array (
                'id' => 16,
                'name' => 'Operasional Darat',
                'deleted_at' => NULL,
                'created_at' => '2023-04-28 18:36:43',
                'updated_at' => '2023-08-15 19:28:48',
            ),
            5 => 
            array (
                'id' => 20,
                'name' => 'Accounting',
                'deleted_at' => NULL,
                'created_at' => '2023-08-09 21:11:29',
                'updated_at' => '2023-08-09 21:11:29',
            ),
            6 => 
            array (
                'id' => 21,
                'name' => 'Operasional Laut',
                'deleted_at' => NULL,
                'created_at' => '2023-08-15 19:29:06',
                'updated_at' => '2023-08-15 19:29:06',
            ),
            7 => 
            array (
                'id' => 27,
                'name' => 'Trading',
                'deleted_at' => NULL,
                'created_at' => '2023-09-19 22:23:01',
                'updated_at' => '2023-09-22 00:19:30',
            ),
            8 => 
            array (
                'id' => 30,
                'name' => 'Management',
                'deleted_at' => NULL,
                'created_at' => '2023-09-20 21:55:54',
                'updated_at' => '2023-09-20 21:55:54',
            ),
        ));
        
        
    }
}