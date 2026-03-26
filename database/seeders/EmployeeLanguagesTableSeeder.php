<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeLanguagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('employee_languages')->delete();
        
        \DB::table('employee_languages')->insert(array (
            0 => 
            array (
                'id' => 1,
                'employee_id' => 169,
                'language' => 'Inggris',
                'speak' => 'Kurang',
                'listening' => 'Kurang',
                'write' => 'Kurang',
                'read' => 'Sedang',
                'created_at' => '2023-12-04 21:15:38',
                'updated_at' => '2023-12-04 21:15:38',
            ),
            1 => 
            array (
                'id' => 3,
                'employee_id' => 38,
                'language' => 'English',
                'speak' => 'Fasih',
                'listening' => 'Fasih',
                'write' => 'Fasih',
                'read' => 'Fasih',
                'created_at' => '2023-12-23 16:13:07',
                'updated_at' => '2023-12-23 16:13:07',
            ),
            2 => 
            array (
                'id' => 4,
                'employee_id' => 161,
                'language' => 'Inggris',
                'speak' => 'Sedang',
                'listening' => 'Sedang',
                'write' => 'Sedang',
                'read' => 'Sedang',
                'created_at' => '2024-01-05 22:20:43',
                'updated_at' => '2024-01-05 22:20:43',
            ),
            3 => 
            array (
                'id' => 5,
                'employee_id' => 190,
                'language' => 'Baik',
                'speak' => 'Baik',
                'listening' => 'Baik',
                'write' => 'Baik',
                'read' => 'Baik',
                'created_at' => '2024-01-10 00:20:27',
                'updated_at' => '2024-01-10 00:20:27',
            ),
            4 => 
            array (
                'id' => 6,
                'employee_id' => 58,
                'language' => 'Inggris',
                'speak' => 'Baik',
                'listening' => 'Baik',
                'write' => 'Baik',
                'read' => 'Baik',
                'created_at' => '2024-01-10 22:37:53',
                'updated_at' => '2024-01-10 22:37:53',
            ),
            5 => 
            array (
                'id' => 7,
                'employee_id' => 59,
                'language' => 'Bahasa Indonesia',
                'speak' => 'Baik',
                'listening' => 'Baik',
                'write' => 'Baik',
                'read' => 'Baik',
                'created_at' => '2024-01-23 17:29:15',
                'updated_at' => '2024-01-23 17:29:15',
            ),
        ));
        
        
    }
}