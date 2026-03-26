<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DeductionSalariesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('deduction_salaries')->delete();
        
        \DB::table('deduction_salaries')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 4,
                'salary_id' => 1,
                'name' => 'PPh 21',
                'type' => 'income-tax',
                'percentage' => '5.00',
                'amount' => 231250.0,
                'qty' => 1,
                'total' => 231250.0,
                'deleted_at' => NULL,
                'created_at' => '2024-02-05 23:47:48',
                'updated_at' => '2024-02-05 23:47:48',
            ),
        ));
        
        
    }
}