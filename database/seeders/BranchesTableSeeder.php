<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BranchesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('branches')->delete();
        
        \DB::table('branches')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Surabaya',
                'address' => 'Jl. Gondosuli no. 8 Surabaya.',
                'phone' => '08155110111',
                'is_primary' => 1,
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2022-12-16 09:05:35',
                'sort' => 'SBY',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Jakarta',
                'address' => 'jl. Danau sunter selatan, kompl. Mega Sunter C-16 jakarta utara',
                'phone' => ' 087786147737',
                'is_primary' => 0,
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2022-12-16 09:05:35',
                'sort' => 'JKT',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Balikpapan',
                'address' => 'jl. Mt Haryono Perum. Balikpapan 2 Blok J. No 200',
                'phone' => '081347436101',
                'is_primary' => 0,
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2022-12-16 09:05:35',
                'sort' => 'BPN',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Samarinda',
                'address' => 'jl. Untung suropati no 55-56',
                'phone' => '081347436101',
                'is_primary' => 0,
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2022-12-16 09:05:35',
                'sort' => 'SMR',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Banjarmasin',
                'address' => 'Jl tes',
                'phone' => '01234',
                'is_primary' => 0,
                'created_at' => '2023-03-14 21:43:39',
                'updated_at' => '2023-09-28 16:26:47',
                'sort' => 'BJR',
                'deleted_at' => '2023-09-28 16:26:47',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Gresik',
                'address' => 'tes',
                'phone' => '12345',
                'is_primary' => 0,
                'created_at' => '2023-03-14 21:44:31',
                'updated_at' => '2023-09-28 16:26:54',
                'sort' => 'GRS',
                'deleted_at' => '2023-09-28 16:26:54',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Berau',
                'address' => 'tes',
                'phone' => '12345',
                'is_primary' => 0,
                'created_at' => '2023-03-14 21:44:53',
                'updated_at' => '2023-09-28 16:27:02',
                'sort' => 'BRA',
                'deleted_at' => '2023-09-28 16:27:02',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Tenggarong',
                'address' => 'Tenggarong',
                'phone' => '00000',
                'is_primary' => 0,
                'created_at' => '2023-05-23 00:54:15',
                'updated_at' => '2023-05-23 00:54:15',
                'sort' => 'TGR',
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Loakulu',
                'address' => 'Tes',
                'phone' => '6789',
                'is_primary' => 0,
                'created_at' => '2023-07-18 13:05:31',
                'updated_at' => '2023-09-28 16:26:34',
                'sort' => 'LKL',
                'deleted_at' => '2023-09-28 16:26:34',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Banjarmasin',
                'address' => 'Jalan Belitung Darat No. 1A RT.026 RW.002 Belitung Selatan, Banjarmasin Barat',
                'phone' => '085245864008',
                'is_primary' => 0,
                'created_at' => '2023-11-29 18:02:08',
                'updated_at' => '2023-11-29 18:02:44',
                'sort' => 'BJM',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}