<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeStrengthWeaknessesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('employee_strength_weaknesses')->delete();
        
        \DB::table('employee_strength_weaknesses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'employee_id' => 5,
                'type' => 'strength',
                'description' => 'Sabar, tidak cepat emosi, suka peduli dg org lain.',
                'created_at' => '2023-12-22 18:05:18',
                'updated_at' => '2023-12-22 18:05:18',
            ),
            1 => 
            array (
                'id' => 7,
                'employee_id' => 191,
                'type' => 'strength',
                'description' => 'Pekerja keras',
                'created_at' => '2024-01-09 18:52:26',
                'updated_at' => '2024-01-09 18:52:26',
            ),
            2 => 
            array (
                'id' => 8,
                'employee_id' => 191,
                'type' => 'weakness',
                'description' => 'Kebiasaan begadang',
                'created_at' => '2024-01-09 18:52:26',
                'updated_at' => '2024-01-09 18:52:26',
            ),
            3 => 
            array (
                'id' => 9,
                'employee_id' => 179,
                'type' => 'strength',
                'description' => 'Bisa Masak',
                'created_at' => '2024-01-09 19:05:53',
                'updated_at' => '2024-01-09 19:05:53',
            ),
            4 => 
            array (
                'id' => 12,
                'employee_id' => 190,
                'type' => 'strength',
                'description' => 'Kelebihan Saya bisa komputer dengan baik',
                'created_at' => '2024-01-10 00:20:46',
                'updated_at' => '2024-01-10 00:20:46',
            ),
            5 => 
            array (
                'id' => 13,
                'employee_id' => 190,
                'type' => 'weakness',
                'description' => 'Kekurangan saya adalah dalam berbahasa inggris masih kurang',
                'created_at' => '2024-01-10 00:20:46',
                'updated_at' => '2024-01-10 00:20:46',
            ),
        ));
        
        
    }
}