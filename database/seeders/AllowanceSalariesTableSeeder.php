<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AllowanceSalariesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('allowance_salaries')->delete();
        
        \DB::table('allowance_salaries')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 4,
                'salary_id' => 1,
                'name' => 'TUNJANGAN JABATAN',
                'type' => 'other',
                'percentage' => '10.00',
                'amount' => 1000000.0,
                'qty' => 1,
                'total' => 1000000.0,
                'deleted_at' => NULL,
                'created_at' => '2024-02-05 23:47:48',
                'updated_at' => '2024-02-05 23:47:48',
            ),
        ));
        
        
    }
}