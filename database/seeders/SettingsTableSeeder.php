<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('settings')->delete();
        
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type' => 'payroll',
                'name' => 'biaya jabatan',
                'value' => '5%',
                'created_at' => NULL,
                'updated_at' => '2023-08-18 12:03:41',
            ),
            1 => 
            array (
                'id' => 2,
                'type' => 'payroll',
                'name' => 'non npwp',
                'value' => '20%',
                'created_at' => NULL,
                'updated_at' => '2023-08-18 12:03:41',
            ),
            2 => 
            array (
                'id' => 3,
                'type' => 'payroll',
                'name' => 'max biaya jabatan',
                'value' => '6000000.00',
                'created_at' => NULL,
                'updated_at' => '2023-08-18 12:03:48',
            ),
        ));
        
        
    }
}