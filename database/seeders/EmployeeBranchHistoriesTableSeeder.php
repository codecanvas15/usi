<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeBranchHistoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('employee_branch_histories')->delete();
        
        \DB::table('employee_branch_histories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'causer_id' => 159,
                'employee_id' => 159,
                'from_branch_id' => 1,
                'to_branch_id' => 1,
                'created_at' => '2023-11-29 19:50:24',
                'updated_at' => '2023-11-29 19:50:24',
            ),
            1 => 
            array (
                'id' => 2,
                'causer_id' => 159,
                'employee_id' => 159,
                'from_branch_id' => 1,
                'to_branch_id' => 1,
                'created_at' => '2024-01-17 21:42:47',
                'updated_at' => '2024-01-17 21:42:47',
            ),
            2 => 
            array (
                'id' => 3,
                'causer_id' => 159,
                'employee_id' => 159,
                'from_branch_id' => 1,
                'to_branch_id' => 1,
                'created_at' => '2024-01-17 21:43:45',
                'updated_at' => '2024-01-17 21:43:45',
            ),
        ));
        
        
    }
}