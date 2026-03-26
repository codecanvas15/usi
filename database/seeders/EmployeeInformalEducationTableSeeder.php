<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeInformalEducationTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('employee_informal_education')->delete();
        
        \DB::table('employee_informal_education')->insert(array (
            0 => 
            array (
                'id' => 1,
                'employee_id' => 137,
                'name' => 'Ilmu Hukum',
                'initiator' => 'Fakultas Hukum Universitas Pembangunan Nasional "Veteran" Jawa Timur',
                'lama' => '4 Tahun',
                'year' => '2023',
                'financed_by' => 'Orang Tua',
                'created_at' => '2023-09-25 21:41:28',
                'updated_at' => '2023-09-25 21:41:28',
            ),
            1 => 
            array (
                'id' => 2,
                'employee_id' => 5,
                'name' => 'SMEA NEGERI TARAKAN',
                'initiator' => 'Tidak ada',
                'lama' => 'TIGA',
                'year' => '0000',
                'financed_by' => 'PRIBADI',
                'created_at' => '2023-12-22 18:25:10',
                'updated_at' => '2023-12-22 18:25:10',
            ),
        ));
        
        
    }
}