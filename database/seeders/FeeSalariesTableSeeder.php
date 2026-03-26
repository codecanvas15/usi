<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FeeSalariesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('fee_salaries')->delete();
        
        \DB::table('fee_salaries')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 4,
                'salary_id' => 1,
                'name' => 'GAJI POKOK',
                'type' => 'other',
                'percentage' => '90.00',
                'amount' => 9000000.0,
                'qty' => 1,
                'total' => 9000000.0,
                'deleted_at' => NULL,
                'created_at' => '2024-02-05 23:47:48',
                'updated_at' => '2024-02-05 23:47:48',
            ),
        ));
        
        
    }
}