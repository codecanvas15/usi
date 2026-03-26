<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeOrganizationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('employee_organizations')->delete();
        
        
        
    }
}