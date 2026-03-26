<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeReferencesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('employee_references')->delete();
        
        
        
    }
}