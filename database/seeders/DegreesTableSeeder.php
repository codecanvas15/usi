<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DegreesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('degrees')->delete();
        
        \DB::table('degrees')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'AKUNTANSI',
                'deleted_at' => NULL,
                'created_at' => '2023-02-07 16:36:05',
                'updated_at' => '2024-02-16 23:46:58',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'ADMINISTRASI',
                'deleted_at' => NULL,
                'created_at' => '2023-02-07 16:36:05',
                'updated_at' => '2024-02-16 23:51:33',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'MESIN',
                'deleted_at' => NULL,
                'created_at' => '2023-02-07 16:36:05',
                'updated_at' => '2024-02-16 23:48:58',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'TEKNIK MESIN',
                'deleted_at' => NULL,
                'created_at' => '2023-02-07 16:36:05',
                'updated_at' => '2024-02-16 23:50:01',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'TEKNIK ELEKTRO',
                'deleted_at' => NULL,
                'created_at' => '2023-02-07 16:36:05',
                'updated_at' => '2024-02-16 23:52:00',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'TEKNIK INFORMATIKA',
                'deleted_at' => NULL,
                'created_at' => '2023-02-07 16:36:05',
                'updated_at' => '2024-02-16 23:51:13',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'TEKNIK SIPIL',
                'deleted_at' => NULL,
                'created_at' => '2023-02-07 16:36:05',
                'updated_at' => '2024-02-16 23:50:26',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'HUKUM',
                'deleted_at' => NULL,
                'created_at' => '2023-03-14 21:31:31',
                'updated_at' => '2024-02-16 23:47:52',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'TEKNIK PERKAPALAN',
                'deleted_at' => NULL,
                'created_at' => '2023-03-14 21:32:37',
                'updated_at' => '2024-02-16 23:48:35',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'tidak ada',
                'deleted_at' => NULL,
                'created_at' => '2023-08-23 00:05:35',
                'updated_at' => '2023-08-23 00:05:35',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'belum',
                'deleted_at' => NULL,
                'created_at' => '2023-08-23 23:41:19',
                'updated_at' => '2023-08-23 23:41:19',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'ILMU KOMUNIKASI',
                'deleted_at' => NULL,
                'created_at' => '2023-09-18 21:42:01',
                'updated_at' => '2024-02-16 23:48:15',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'TEKNOLOGI PANGAN',
                'deleted_at' => NULL,
                'created_at' => '2023-09-18 21:42:26',
                'updated_at' => '2024-02-16 23:47:34',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'KIMIA',
                'deleted_at' => NULL,
                'created_at' => '2024-02-16 23:46:25',
                'updated_at' => '2024-02-16 23:46:25',
            ),
        ));
        
        
    }
}