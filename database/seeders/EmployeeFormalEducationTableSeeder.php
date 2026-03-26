<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeFormalEducationTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('employee_formal_education')->delete();
        
        \DB::table('employee_formal_education')->insert(array (
            0 => 
            array (
                'id' => 1,
                'employee_id' => 169,
                'level' => 'D4',
                'name' => 'Politeknik Negeri Samarinda',
                'city' => 'Samarinda',
                'faculty' => 'Administrasi Bisnis',
                'major' => NULL,
                'from' => '2023-12-04',
                'to' => '2023-12-04',
                'gpa' => '3.53',
                'graduate' => '2019',
                'created_at' => '2023-12-04 21:15:38',
                'updated_at' => '2023-12-04 21:15:38',
            ),
            1 => 
            array (
                'id' => 3,
                'employee_id' => 28,
                'level' => 'Smk',
                'name' => 'Smk sore',
                'city' => 'Tulungagung',
                'faculty' => NULL,
                'major' => NULL,
                'from' => '1999-06-01',
                'to' => '2022-01-01',
                'gpa' => NULL,
                'graduate' => NULL,
                'created_at' => '2024-01-05 17:52:55',
                'updated_at' => '2024-01-05 17:52:55',
            ),
            2 => 
            array (
                'id' => 4,
                'employee_id' => 161,
                'level' => 'S1',
                'name' => 'POLTEKPEL BOROMBONG',
                'city' => 'Makassar',
                'faculty' => 'Teknika',
                'major' => 'Teknika',
                'from' => '2018-08-01',
                'to' => '2020-04-12',
                'gpa' => NULL,
                'graduate' => '2020',
                'created_at' => '2024-01-05 22:20:43',
                'updated_at' => '2024-01-05 22:20:43',
            ),
            3 => 
            array (
                'id' => 5,
                'employee_id' => 49,
                'level' => 'S1',
                'name' => 'Institut Adhitama Surabaya',
                'city' => 'Surabaya',
                'faculty' => 'Teknologi Industri',
                'major' => 'Teknik Kimia',
                'from' => '2024-01-26',
                'to' => '2024-01-26',
                'gpa' => '3.07',
                'graduate' => NULL,
                'created_at' => '2024-01-26 13:27:27',
                'updated_at' => '2024-01-26 13:27:27',
            ),
        ));
        
        
    }
}