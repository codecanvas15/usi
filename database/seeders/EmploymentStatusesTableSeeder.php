<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmploymentStatusesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('employment_statuses')->delete();
        
        \DB::table('employment_statuses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Tetap',
                'deleted_at' => NULL,
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2022-12-16 09:05:35',
                'is_default' => 0,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Kontrak',
                'deleted_at' => NULL,
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2022-12-16 09:05:35',
                'is_default' => 0,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Magang',
                'deleted_at' => NULL,
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2022-12-16 09:05:35',
                'is_default' => 0,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Outsourcing',
                'deleted_at' => NULL,
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2022-12-16 09:05:35',
                'is_default' => 0,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Freelance',
                'deleted_at' => NULL,
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2022-12-16 09:05:35',
                'is_default' => 0,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'PKWT',
                'deleted_at' => NULL,
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2022-12-16 09:05:35',
                'is_default' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'NON-PKWT',
                'deleted_at' => NULL,
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2022-12-16 09:05:35',
                'is_default' => 1,
            ),
        ));
        
        
    }
}