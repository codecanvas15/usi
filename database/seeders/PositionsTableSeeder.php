<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PositionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('positions')->delete();
        
        \DB::table('positions')->insert(array (
            0 => 
            array (
                'id' => 2,
                'code' => 'S02',
                'nama' => 'MANAGER',
                'deleted_at' => NULL,
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2024-02-16 23:36:06',
            ),
            1 => 
            array (
                'id' => 3,
                'code' => NULL,
                'nama' => 'Finance',
                'deleted_at' => '2023-09-20 22:00:26',
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2023-09-20 22:00:26',
            ),
            2 => 
            array (
                'id' => 5,
                'code' => NULL,
                'nama' => 'Sales',
                'deleted_at' => '2023-09-20 22:00:38',
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2023-09-20 22:00:38',
            ),
            3 => 
            array (
                'id' => 7,
                'code' => NULL,
                'nama' => 'Accounting',
                'deleted_at' => '2023-09-20 22:00:48',
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2023-09-20 22:00:48',
            ),
            4 => 
            array (
                'id' => 8,
                'code' => NULL,
                'nama' => 'Hrd',
                'deleted_at' => '2023-09-20 22:00:59',
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2023-09-20 22:00:59',
            ),
            5 => 
            array (
                'id' => 11,
                'code' => NULL,
                'nama' => 'Driver',
                'deleted_at' => '2023-09-20 22:00:04',
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2023-09-20 22:00:04',
            ),
            6 => 
            array (
                'id' => 21,
                'code' => 'S04',
                'nama' => 'ADMIN',
                'deleted_at' => NULL,
                'created_at' => '2023-03-21 23:55:19',
                'updated_at' => '2024-02-16 23:35:48',
            ),
            7 => 
            array (
                'id' => 22,
                'code' => NULL,
                'nama' => 'Cleaning',
                'deleted_at' => '2023-09-20 21:58:44',
                'created_at' => '2023-03-23 21:06:32',
                'updated_at' => '2023-09-20 21:58:44',
            ),
            8 => 
            array (
                'id' => 23,
                'code' => NULL,
                'nama' => 'Operasional',
                'deleted_at' => '2023-09-20 21:58:27',
                'created_at' => '2023-04-28 18:33:05',
                'updated_at' => '2023-09-20 21:58:27',
            ),
            9 => 
            array (
                'id' => 24,
                'code' => 'C04',
                'nama' => 'NAHKODA',
                'deleted_at' => NULL,
                'created_at' => '2023-05-31 19:06:30',
                'updated_at' => '2024-02-16 23:36:56',
            ),
            10 => 
            array (
                'id' => 25,
                'code' => 'C04',
                'nama' => 'MUALIM 1',
                'deleted_at' => NULL,
                'created_at' => '2023-05-31 19:07:00',
                'updated_at' => '2024-02-20 19:14:45',
            ),
            11 => 
            array (
                'id' => 26,
                'code' => 'C04',
                'nama' => 'MUALIM 2',
                'deleted_at' => NULL,
                'created_at' => '2023-05-31 19:07:28',
                'updated_at' => '2024-02-20 19:11:38',
            ),
            12 => 
            array (
                'id' => 28,
                'code' => 'C04',
                'nama' => 'KKM',
                'deleted_at' => NULL,
                'created_at' => '2023-05-31 19:08:14',
                'updated_at' => '2024-02-20 19:15:01',
            ),
            13 => 
            array (
                'id' => 29,
                'code' => 'C04',
                'nama' => 'MASINIS 2',
                'deleted_at' => NULL,
                'created_at' => '2023-05-31 22:41:44',
                'updated_at' => '2024-02-20 19:15:17',
            ),
            14 => 
            array (
                'id' => 30,
                'code' => 'C04',
                'nama' => 'MASINIS 3',
                'deleted_at' => NULL,
                'created_at' => '2023-05-31 22:44:40',
                'updated_at' => '2024-02-20 19:15:33',
            ),
            15 => 
            array (
                'id' => 31,
                'code' => 'C04',
                'nama' => 'JURU MINYAK',
                'deleted_at' => '2024-02-20 19:20:00',
                'created_at' => '2023-05-31 22:45:09',
                'updated_at' => '2024-02-20 19:20:00',
            ),
            16 => 
            array (
                'id' => 32,
                'code' => 'C04',
                'nama' => 'KOKI',
                'deleted_at' => NULL,
                'created_at' => '2023-05-31 22:45:29',
                'updated_at' => '2024-02-20 19:16:16',
            ),
            17 => 
            array (
                'id' => 33,
                'code' => 'C04',
                'nama' => 'KELASI',
                'deleted_at' => NULL,
                'created_at' => '2023-08-22 22:04:24',
                'updated_at' => '2024-02-20 19:16:46',
            ),
            18 => 
            array (
                'id' => 34,
                'code' => 'C04',
                'nama' => 'JURU MUDI 1',
                'deleted_at' => NULL,
                'created_at' => '2023-08-23 00:05:35',
                'updated_at' => '2024-02-20 19:17:13',
            ),
            19 => 
            array (
                'id' => 35,
                'code' => 'C04',
                'nama' => 'BARGE MASTER',
                'deleted_at' => NULL,
                'created_at' => '2023-08-23 18:39:27',
                'updated_at' => '2024-02-20 19:21:27',
            ),
            20 => 
            array (
                'id' => 36,
                'code' => NULL,
                'nama' => 'Perpajakan',
                'deleted_at' => '2023-09-20 21:57:59',
                'created_at' => '2023-08-23 23:41:19',
                'updated_at' => '2023-09-20 21:57:59',
            ),
            21 => 
            array (
                'id' => 37,
                'code' => NULL,
                'nama' => 'belum',
                'deleted_at' => '2023-09-20 21:56:53',
                'created_at' => '2023-08-24 01:01:28',
                'updated_at' => '2023-09-20 21:56:53',
            ),
            22 => 
            array (
                'id' => 38,
                'code' => 'S03',
                'nama' => 'SUPERVISOR',
                'deleted_at' => NULL,
                'created_at' => '2023-08-25 21:43:15',
                'updated_at' => '2024-02-16 23:39:26',
            ),
            23 => 
            array (
                'id' => 39,
                'code' => 'D04',
                'nama' => 'SUPIR',
                'deleted_at' => NULL,
                'created_at' => '2023-08-25 21:43:15',
                'updated_at' => '2024-02-16 23:37:38',
            ),
            24 => 
            array (
                'id' => 40,
                'code' => 'C04',
                'nama' => 'JURU MUDI 3',
                'deleted_at' => NULL,
                'created_at' => '2023-08-29 00:22:04',
                'updated_at' => '2024-02-20 19:20:58',
            ),
            25 => 
            array (
                'id' => 41,
                'code' => 'C04',
                'nama' => 'JURU MINYAK 1',
                'deleted_at' => NULL,
                'created_at' => '2023-08-29 00:22:04',
                'updated_at' => '2024-02-20 19:20:32',
            ),
            26 => 
            array (
                'id' => 42,
                'code' => 'C04',
                'nama' => 'BOSUN',
                'deleted_at' => NULL,
                'created_at' => '2023-08-29 00:22:04',
                'updated_at' => '2024-02-20 19:19:20',
            ),
            27 => 
            array (
                'id' => 43,
                'code' => 'C04',
                'nama' => 'JURU MUDI 2',
                'deleted_at' => NULL,
                'created_at' => '2023-08-29 00:22:04',
                'updated_at' => '2024-02-20 19:19:01',
            ),
            28 => 
            array (
                'id' => 44,
                'code' => NULL,
                'nama' => 'Juru  Mudi 1',
                'deleted_at' => '2024-02-20 19:18:34',
                'created_at' => '2023-08-31 00:52:13',
                'updated_at' => '2024-02-20 19:18:34',
            ),
            29 => 
            array (
                'id' => 45,
                'code' => 'C04',
                'nama' => 'WIPER',
                'deleted_at' => NULL,
                'created_at' => '2023-08-31 19:17:43',
                'updated_at' => '2024-02-20 19:17:37',
            ),
            30 => 
            array (
                'id' => 47,
                'code' => 'S04',
                'nama' => 'STAFF',
                'deleted_at' => NULL,
                'created_at' => '2023-09-21 00:56:34',
                'updated_at' => '2024-02-20 19:17:58',
            ),
            31 => 
            array (
                'id' => 48,
                'code' => 'M01',
                'nama' => 'DIREKTUR',
                'deleted_at' => NULL,
                'created_at' => '2023-09-25 21:45:51',
                'updated_at' => '2024-02-16 23:38:06',
            ),
        ));
        
        
    }
}