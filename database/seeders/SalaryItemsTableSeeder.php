<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SalaryItemsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('salary_items')->delete();
        
        \DB::table('salary_items')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Tunjangan Jabatan',
                'type' => 'tunjangan',
                'percentage' => 10.0,
                'deleted_at' => NULL,
                'created_at' => '2023-08-21 15:52:37',
                'updated_at' => '2023-08-21 15:52:37',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Potongan BPJS',
                'type' => 'potongan',
                'percentage' => 0.0,
                'deleted_at' => NULL,
                'created_at' => '2023-08-21 15:53:22',
                'updated_at' => '2023-08-21 15:53:22',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Gaji Pokok',
                'type' => 'upah',
                'percentage' => 90.0,
                'deleted_at' => NULL,
                'created_at' => '2023-08-21 15:53:55',
                'updated_at' => '2023-08-21 15:53:55',
            ),
        ));
        
        
    }
}