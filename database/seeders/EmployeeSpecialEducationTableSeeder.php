<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeSpecialEducationTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('employee_special_education')->delete();
        
        \DB::table('employee_special_education')->insert(array (
            0 => 
            array (
                'id' => 1,
                'employee_id' => 190,
                'name' => 'Ami Veteran Makassar',
                'year' => '2008',
                'created_at' => '2024-01-10 00:20:27',
                'updated_at' => '2024-01-10 00:20:27',
            ),
            1 => 
            array (
                'id' => 2,
                'employee_id' => 58,
                'name' => 'American Language Training',
                'year' => '2008',
                'created_at' => '2024-01-10 22:37:53',
                'updated_at' => '2024-01-10 22:37:53',
            ),
        ));
        
        
    }
}