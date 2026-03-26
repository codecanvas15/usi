<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeHealthHistoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('employee_health_histories')->delete();
        
        \DB::table('employee_health_histories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'employee_id' => 5,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-09-18 20:48:21',
                'updated_at' => '2023-09-18 20:48:21',
            ),
            1 => 
            array (
                'id' => 2,
                'employee_id' => 39,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'tidak ada',
                'created_at' => '2023-09-18 21:18:36',
                'updated_at' => '2023-09-18 21:18:36',
            ),
            2 => 
            array (
                'id' => 3,
                'employee_id' => 133,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'belum ada',
                'created_at' => '2023-09-20 18:46:16',
                'updated_at' => '2023-09-20 18:46:16',
            ),
            3 => 
            array (
                'id' => 4,
                'employee_id' => 104,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'tidak ada',
                'created_at' => '2023-09-20 18:56:27',
                'updated_at' => '2023-09-20 18:56:27',
            ),
            4 => 
            array (
                'id' => 5,
                'employee_id' => 112,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'tidak ada',
                'created_at' => '2023-09-20 19:05:21',
                'updated_at' => '2023-09-20 19:05:21',
            ),
            5 => 
            array (
                'id' => 6,
                'employee_id' => 108,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'tidak ada',
                'created_at' => '2023-09-20 19:43:37',
                'updated_at' => '2023-09-20 19:43:37',
            ),
            6 => 
            array (
                'id' => 7,
                'employee_id' => 6,
                'condition' => 'Sehat',
                'description' => 'tidak pernah',
                'description_2' => 'tidak ada',
                'created_at' => '2023-09-20 19:58:07',
                'updated_at' => '2023-09-20 19:58:07',
            ),
            7 => 
            array (
                'id' => 8,
                'employee_id' => 4,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-09-20 20:03:56',
                'updated_at' => '2023-09-20 20:03:56',
            ),
            8 => 
            array (
                'id' => 9,
                'employee_id' => 38,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'tidak ada',
                'created_at' => '2023-09-21 23:45:39',
                'updated_at' => '2023-09-21 23:45:39',
            ),
            9 => 
            array (
                'id' => 10,
                'employee_id' => 34,
                'condition' => 'Sehat',
                'description' => 'tidak pernah',
                'description_2' => 'tidak ada',
                'created_at' => '2023-09-21 23:51:52',
                'updated_at' => '2023-09-21 23:51:52',
            ),
            10 => 
            array (
                'id' => 11,
                'employee_id' => 48,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-09-21 23:57:26',
                'updated_at' => '2023-09-21 23:57:26',
            ),
            11 => 
            array (
                'id' => 12,
                'employee_id' => 8,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-09-22 00:05:15',
                'updated_at' => '2023-09-22 00:05:15',
            ),
            12 => 
            array (
                'id' => 13,
                'employee_id' => 40,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'tidak ada',
                'created_at' => '2023-09-22 00:12:17',
                'updated_at' => '2023-09-22 00:12:17',
            ),
            13 => 
            array (
                'id' => 14,
                'employee_id' => 12,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'tidak ada',
                'created_at' => '2023-09-22 00:23:50',
                'updated_at' => '2023-09-22 00:23:50',
            ),
            14 => 
            array (
                'id' => 15,
                'employee_id' => 47,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'tidak ada',
                'created_at' => '2023-09-22 00:30:35',
                'updated_at' => '2023-09-22 00:30:35',
            ),
            15 => 
            array (
                'id' => 16,
                'employee_id' => 36,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-09-22 17:49:03',
                'updated_at' => '2023-09-22 17:49:03',
            ),
            16 => 
            array (
                'id' => 17,
                'employee_id' => 119,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'tidak ada',
                'created_at' => '2023-09-23 00:16:47',
                'updated_at' => '2023-09-23 00:16:47',
            ),
            17 => 
            array (
                'id' => 18,
                'employee_id' => 137,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-09-23 00:24:31',
                'updated_at' => '2023-09-23 00:24:31',
            ),
            18 => 
            array (
                'id' => 19,
                'employee_id' => 128,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-09-23 00:47:11',
                'updated_at' => '2023-09-23 00:47:11',
            ),
            19 => 
            array (
                'id' => 20,
                'employee_id' => 100,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-09-23 00:59:53',
                'updated_at' => '2023-12-08 23:57:53',
            ),
            20 => 
            array (
                'id' => 21,
                'employee_id' => 138,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-09-25 21:50:04',
                'updated_at' => '2023-09-25 21:50:04',
            ),
            21 => 
            array (
                'id' => 22,
                'employee_id' => 9,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-09-26 22:39:12',
                'updated_at' => '2023-12-08 22:10:13',
            ),
            22 => 
            array (
                'id' => 23,
                'employee_id' => 10,
                'condition' => 'Sehat',
                'description' => 'Tidak',
                'description_2' => 'Tidak',
                'created_at' => '2023-09-26 22:47:12',
                'updated_at' => '2023-09-26 22:47:12',
            ),
            23 => 
            array (
                'id' => 24,
                'employee_id' => 13,
                'condition' => 'Sehat',
                'description' => 'Tidak',
                'description_2' => 'Tidak',
                'created_at' => '2023-09-26 22:56:22',
                'updated_at' => '2023-09-26 22:56:22',
            ),
            24 => 
            array (
                'id' => 25,
                'employee_id' => 14,
                'condition' => 'Sehat',
                'description' => 'Tidak',
                'description_2' => 'Tidak',
                'created_at' => '2023-09-26 23:07:04',
                'updated_at' => '2023-09-26 23:07:04',
            ),
            25 => 
            array (
                'id' => 26,
                'employee_id' => 50,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-09-30 00:41:54',
                'updated_at' => '2023-09-30 00:41:54',
            ),
            26 => 
            array (
                'id' => 27,
                'employee_id' => 56,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'tidak ada',
                'created_at' => '2023-09-30 00:52:45',
                'updated_at' => '2023-09-30 00:52:45',
            ),
            27 => 
            array (
                'id' => 28,
                'employee_id' => 68,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-10-04 19:00:49',
                'updated_at' => '2023-12-23 00:26:48',
            ),
            28 => 
            array (
                'id' => 29,
                'employee_id' => 7,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-10-04 20:47:02',
                'updated_at' => '2023-12-04 21:04:59',
            ),
            29 => 
            array (
                'id' => 30,
                'employee_id' => 11,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'TIdak ada',
                'created_at' => '2023-10-04 22:14:23',
                'updated_at' => '2023-10-04 22:14:23',
            ),
            30 => 
            array (
                'id' => 31,
                'employee_id' => 16,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'tidak ada',
                'created_at' => '2023-10-04 22:21:40',
                'updated_at' => '2023-10-04 22:21:40',
            ),
            31 => 
            array (
                'id' => 32,
                'employee_id' => 18,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-10-05 00:46:46',
                'updated_at' => '2023-10-05 00:46:46',
            ),
            32 => 
            array (
                'id' => 33,
                'employee_id' => 30,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-10-05 00:55:56',
                'updated_at' => '2023-10-05 00:55:56',
            ),
            33 => 
            array (
                'id' => 34,
                'employee_id' => 31,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-10-05 01:02:56',
                'updated_at' => '2023-10-05 01:02:56',
            ),
            34 => 
            array (
                'id' => 35,
                'employee_id' => 37,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-10-05 17:39:15',
                'updated_at' => '2023-10-05 17:39:15',
            ),
            35 => 
            array (
                'id' => 36,
                'employee_id' => 35,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'tidak ada',
                'created_at' => '2023-10-05 18:10:17',
                'updated_at' => '2023-10-05 18:10:17',
            ),
            36 => 
            array (
                'id' => 37,
                'employee_id' => 33,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-10-05 18:53:25',
                'updated_at' => '2023-10-05 18:53:25',
            ),
            37 => 
            array (
                'id' => 38,
                'employee_id' => 57,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'tidak ada',
                'created_at' => '2023-10-05 19:21:18',
                'updated_at' => '2023-10-05 19:21:18',
            ),
            38 => 
            array (
                'id' => 39,
                'employee_id' => 45,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-10-05 19:37:04',
                'updated_at' => '2023-10-05 19:37:04',
            ),
            39 => 
            array (
                'id' => 40,
                'employee_id' => 44,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'tidak ada',
                'created_at' => '2023-10-05 19:54:52',
                'updated_at' => '2023-10-05 19:54:52',
            ),
            40 => 
            array (
                'id' => 41,
                'employee_id' => 159,
                'condition' => 'Baik',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-11-28 17:43:44',
                'updated_at' => '2023-11-28 17:43:44',
            ),
            41 => 
            array (
                'id' => 42,
                'employee_id' => 170,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-01 21:13:03',
                'updated_at' => '2023-12-04 21:00:30',
            ),
            42 => 
            array (
                'id' => 43,
                'employee_id' => 168,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-01 21:23:40',
                'updated_at' => '2023-12-04 21:09:02',
            ),
            43 => 
            array (
                'id' => 44,
                'employee_id' => 167,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-01 21:25:54',
                'updated_at' => '2023-12-04 21:20:29',
            ),
            44 => 
            array (
                'id' => 45,
                'employee_id' => 166,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-01 21:27:37',
                'updated_at' => '2023-12-04 21:29:36',
            ),
            45 => 
            array (
                'id' => 46,
                'employee_id' => 165,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-01 21:36:28',
                'updated_at' => '2023-12-04 21:32:40',
            ),
            46 => 
            array (
                'id' => 47,
                'employee_id' => 164,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-01 21:39:34',
                'updated_at' => '2023-12-04 21:34:01',
            ),
            47 => 
            array (
                'id' => 48,
                'employee_id' => 163,
                'condition' => 'Sehat',
                'description' => 'Tidak',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-01 21:41:03',
                'updated_at' => '2023-12-01 21:41:03',
            ),
            48 => 
            array (
                'id' => 49,
                'employee_id' => 160,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-01 21:50:06',
                'updated_at' => '2023-12-04 21:59:44',
            ),
            49 => 
            array (
                'id' => 50,
                'employee_id' => 157,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-01 21:59:00',
                'updated_at' => '2023-12-05 16:50:27',
            ),
            50 => 
            array (
                'id' => 51,
                'employee_id' => 153,
                'condition' => 'Sehat',
                'description' => 'Tidak',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-01 22:13:53',
                'updated_at' => '2023-12-01 22:13:53',
            ),
            51 => 
            array (
                'id' => 52,
                'employee_id' => 152,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-01 22:33:04',
                'updated_at' => '2023-12-05 17:37:22',
            ),
            52 => 
            array (
                'id' => 53,
                'employee_id' => 150,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-01 22:46:46',
                'updated_at' => '2023-12-05 18:00:06',
            ),
            53 => 
            array (
                'id' => 54,
                'employee_id' => 149,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-01 23:06:02',
                'updated_at' => '2023-12-05 18:07:01',
            ),
            54 => 
            array (
                'id' => 55,
                'employee_id' => 173,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-04 19:51:30',
                'updated_at' => '2023-12-04 19:51:30',
            ),
            55 => 
            array (
                'id' => 56,
                'employee_id' => 172,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-04 20:00:04',
                'updated_at' => '2023-12-04 20:00:04',
            ),
            56 => 
            array (
                'id' => 57,
                'employee_id' => 171,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-04 20:07:35',
                'updated_at' => '2023-12-04 20:07:35',
            ),
            57 => 
            array (
                'id' => 58,
                'employee_id' => 169,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-04 21:05:50',
                'updated_at' => '2023-12-04 21:05:50',
            ),
            58 => 
            array (
                'id' => 59,
                'employee_id' => 162,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-04 21:43:40',
                'updated_at' => '2023-12-04 21:43:40',
            ),
            59 => 
            array (
                'id' => 60,
                'employee_id' => 161,
                'condition' => 'Alhamdulillah sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-04 21:49:17',
                'updated_at' => '2024-01-05 22:17:26',
            ),
            60 => 
            array (
                'id' => 61,
                'employee_id' => 158,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-04 22:05:56',
                'updated_at' => '2023-12-04 22:05:56',
            ),
            61 => 
            array (
                'id' => 62,
                'employee_id' => 156,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-05 16:58:57',
                'updated_at' => '2023-12-05 16:58:57',
            ),
            62 => 
            array (
                'id' => 63,
                'employee_id' => 155,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-05 17:13:19',
                'updated_at' => '2023-12-05 17:13:19',
            ),
            63 => 
            array (
                'id' => 64,
                'employee_id' => 154,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-05 17:23:27',
                'updated_at' => '2023-12-05 17:23:27',
            ),
            64 => 
            array (
                'id' => 65,
                'employee_id' => 151,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-05 17:48:52',
                'updated_at' => '2023-12-05 17:48:52',
            ),
            65 => 
            array (
                'id' => 66,
                'employee_id' => 148,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Sehat',
                'created_at' => '2023-12-05 18:10:57',
                'updated_at' => '2023-12-05 18:10:57',
            ),
            66 => 
            array (
                'id' => 67,
                'employee_id' => 147,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-05 18:19:04',
                'updated_at' => '2023-12-05 18:19:04',
            ),
            67 => 
            array (
                'id' => 68,
                'employee_id' => 145,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-05 20:24:34',
                'updated_at' => '2023-12-05 20:24:34',
            ),
            68 => 
            array (
                'id' => 69,
                'employee_id' => 144,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-05 20:38:18',
                'updated_at' => '2023-12-05 20:38:18',
            ),
            69 => 
            array (
                'id' => 70,
                'employee_id' => 143,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-05 20:42:26',
                'updated_at' => '2023-12-05 20:42:26',
            ),
            70 => 
            array (
                'id' => 71,
                'employee_id' => 142,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-05 20:50:33',
                'updated_at' => '2023-12-05 20:50:33',
            ),
            71 => 
            array (
                'id' => 72,
                'employee_id' => 141,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-05 20:58:25',
                'updated_at' => '2023-12-05 20:58:25',
            ),
            72 => 
            array (
                'id' => 73,
                'employee_id' => 140,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-05 21:11:48',
                'updated_at' => '2023-12-05 21:11:48',
            ),
            73 => 
            array (
                'id' => 74,
                'employee_id' => 139,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-05 21:16:09',
                'updated_at' => '2023-12-05 21:16:09',
            ),
            74 => 
            array (
                'id' => 75,
                'employee_id' => 136,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-05 21:23:17',
                'updated_at' => '2023-12-05 21:23:17',
            ),
            75 => 
            array (
                'id' => 76,
                'employee_id' => 19,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 19:34:13',
                'updated_at' => '2023-12-08 19:34:13',
            ),
            76 => 
            array (
                'id' => 77,
                'employee_id' => 25,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 19:37:35',
                'updated_at' => '2023-12-08 19:37:35',
            ),
            77 => 
            array (
                'id' => 78,
                'employee_id' => 21,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 19:43:10',
                'updated_at' => '2023-12-08 19:43:10',
            ),
            78 => 
            array (
                'id' => 79,
                'employee_id' => 22,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 21:05:37',
                'updated_at' => '2023-12-08 21:05:37',
            ),
            79 => 
            array (
                'id' => 80,
                'employee_id' => 23,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 21:09:44',
                'updated_at' => '2023-12-08 21:09:44',
            ),
            80 => 
            array (
                'id' => 81,
                'employee_id' => 15,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 21:54:44',
                'updated_at' => '2023-12-08 21:54:44',
            ),
            81 => 
            array (
                'id' => 82,
                'employee_id' => 26,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 21:58:29',
                'updated_at' => '2023-12-08 21:58:29',
            ),
            82 => 
            array (
                'id' => 83,
                'employee_id' => 28,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 22:03:23',
                'updated_at' => '2023-12-08 22:03:23',
            ),
            83 => 
            array (
                'id' => 84,
                'employee_id' => 29,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 22:06:29',
                'updated_at' => '2023-12-08 22:06:29',
            ),
            84 => 
            array (
                'id' => 85,
                'employee_id' => 41,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 22:22:21',
                'updated_at' => '2023-12-08 22:22:21',
            ),
            85 => 
            array (
                'id' => 86,
                'employee_id' => 42,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 22:29:59',
                'updated_at' => '2023-12-08 22:29:59',
            ),
            86 => 
            array (
                'id' => 87,
                'employee_id' => 43,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 22:34:48',
                'updated_at' => '2023-12-08 22:34:48',
            ),
            87 => 
            array (
                'id' => 88,
                'employee_id' => 78,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 22:46:17',
                'updated_at' => '2023-12-08 22:46:17',
            ),
            88 => 
            array (
                'id' => 89,
                'employee_id' => 79,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 22:49:22',
                'updated_at' => '2023-12-08 22:49:22',
            ),
            89 => 
            array (
                'id' => 90,
                'employee_id' => 80,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 22:50:59',
                'updated_at' => '2023-12-08 22:50:59',
            ),
            90 => 
            array (
                'id' => 91,
                'employee_id' => 81,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 22:53:43',
                'updated_at' => '2023-12-08 22:53:43',
            ),
            91 => 
            array (
                'id' => 92,
                'employee_id' => 82,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 22:58:57',
                'updated_at' => '2023-12-08 22:58:57',
            ),
            92 => 
            array (
                'id' => 93,
                'employee_id' => 83,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 23:02:24',
                'updated_at' => '2023-12-08 23:02:24',
            ),
            93 => 
            array (
                'id' => 94,
                'employee_id' => 84,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 23:04:26',
                'updated_at' => '2023-12-08 23:04:26',
            ),
            94 => 
            array (
                'id' => 95,
                'employee_id' => 85,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 23:06:30',
                'updated_at' => '2023-12-08 23:06:30',
            ),
            95 => 
            array (
                'id' => 96,
                'employee_id' => 86,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 23:09:09',
                'updated_at' => '2023-12-08 23:09:09',
            ),
            96 => 
            array (
                'id' => 97,
                'employee_id' => 87,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 23:11:03',
                'updated_at' => '2023-12-08 23:11:03',
            ),
            97 => 
            array (
                'id' => 98,
                'employee_id' => 88,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 23:13:36',
                'updated_at' => '2023-12-08 23:13:36',
            ),
            98 => 
            array (
                'id' => 99,
                'employee_id' => 90,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 23:15:37',
                'updated_at' => '2023-12-08 23:15:37',
            ),
            99 => 
            array (
                'id' => 100,
                'employee_id' => 91,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 23:17:14',
                'updated_at' => '2023-12-08 23:17:14',
            ),
            100 => 
            array (
                'id' => 101,
                'employee_id' => 93,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 23:22:38',
                'updated_at' => '2023-12-08 23:22:38',
            ),
            101 => 
            array (
                'id' => 102,
                'employee_id' => 95,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 23:28:56',
                'updated_at' => '2023-12-08 23:28:56',
            ),
            102 => 
            array (
                'id' => 103,
                'employee_id' => 96,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 23:48:09',
                'updated_at' => '2023-12-08 23:48:09',
            ),
            103 => 
            array (
                'id' => 104,
                'employee_id' => 97,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-08 23:54:14',
                'updated_at' => '2023-12-08 23:54:14',
            ),
            104 => 
            array (
                'id' => 105,
                'employee_id' => 101,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-09 00:01:09',
                'updated_at' => '2023-12-09 00:01:09',
            ),
            105 => 
            array (
                'id' => 106,
                'employee_id' => 102,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-09 00:05:10',
                'updated_at' => '2023-12-09 00:05:10',
            ),
            106 => 
            array (
                'id' => 107,
                'employee_id' => 103,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-09 00:08:10',
                'updated_at' => '2023-12-09 00:08:10',
            ),
            107 => 
            array (
                'id' => 108,
                'employee_id' => 105,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-09 00:13:08',
                'updated_at' => '2023-12-09 00:13:08',
            ),
            108 => 
            array (
                'id' => 109,
                'employee_id' => 106,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-09 00:20:00',
                'updated_at' => '2023-12-09 00:20:00',
            ),
            109 => 
            array (
                'id' => 110,
                'employee_id' => 109,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-09 00:22:57',
                'updated_at' => '2023-12-09 00:22:57',
            ),
            110 => 
            array (
                'id' => 111,
                'employee_id' => 111,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-09 00:29:45',
                'updated_at' => '2023-12-09 00:29:45',
            ),
            111 => 
            array (
                'id' => 112,
                'employee_id' => 113,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-09 00:34:42',
                'updated_at' => '2023-12-09 00:34:42',
            ),
            112 => 
            array (
                'id' => 113,
                'employee_id' => 114,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-09 00:38:31',
                'updated_at' => '2023-12-09 00:38:31',
            ),
            113 => 
            array (
                'id' => 114,
                'employee_id' => 116,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-09 01:03:10',
                'updated_at' => '2023-12-09 01:03:10',
            ),
            114 => 
            array (
                'id' => 115,
                'employee_id' => 118,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-09 01:09:42',
                'updated_at' => '2023-12-09 01:09:42',
            ),
            115 => 
            array (
                'id' => 116,
                'employee_id' => 121,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-09 01:12:55',
                'updated_at' => '2023-12-09 01:12:55',
            ),
            116 => 
            array (
                'id' => 117,
                'employee_id' => 122,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-09 01:15:38',
                'updated_at' => '2023-12-09 01:15:38',
            ),
            117 => 
            array (
                'id' => 118,
                'employee_id' => 125,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-09 01:25:21',
                'updated_at' => '2023-12-09 01:25:21',
            ),
            118 => 
            array (
                'id' => 119,
                'employee_id' => 126,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-09 01:30:32',
                'updated_at' => '2023-12-09 01:30:32',
            ),
            119 => 
            array (
                'id' => 120,
                'employee_id' => 146,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-11 23:11:36',
                'updated_at' => '2023-12-11 23:11:36',
            ),
            120 => 
            array (
                'id' => 121,
                'employee_id' => 174,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-13 18:21:05',
                'updated_at' => '2023-12-13 18:21:05',
            ),
            121 => 
            array (
                'id' => 122,
                'employee_id' => 175,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-13 19:20:59',
                'updated_at' => '2023-12-13 19:20:59',
            ),
            122 => 
            array (
                'id' => 123,
                'employee_id' => 176,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-13 21:18:25',
                'updated_at' => '2023-12-13 21:18:25',
            ),
            123 => 
            array (
                'id' => 124,
                'employee_id' => 177,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-18 18:45:54',
                'updated_at' => '2023-12-18 18:45:54',
            ),
            124 => 
            array (
                'id' => 125,
                'employee_id' => 178,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-18 19:02:20',
                'updated_at' => '2023-12-18 19:02:20',
            ),
            125 => 
            array (
                'id' => 126,
                'employee_id' => 179,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-18 19:35:07',
                'updated_at' => '2023-12-18 19:35:07',
            ),
            126 => 
            array (
                'id' => 127,
                'employee_id' => 180,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-20 22:57:51',
                'updated_at' => '2023-12-20 22:57:51',
            ),
            127 => 
            array (
                'id' => 128,
                'employee_id' => 62,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'tidak ada',
                'created_at' => '2023-12-22 23:47:27',
                'updated_at' => '2023-12-22 23:47:27',
            ),
            128 => 
            array (
                'id' => 129,
                'employee_id' => 63,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-12-23 00:02:11',
                'updated_at' => '2023-12-23 00:02:11',
            ),
            129 => 
            array (
                'id' => 130,
                'employee_id' => 65,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-12-23 00:14:39',
                'updated_at' => '2023-12-23 00:14:39',
            ),
            130 => 
            array (
                'id' => 131,
                'employee_id' => 67,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-12-23 00:25:03',
                'updated_at' => '2023-12-23 00:25:03',
            ),
            131 => 
            array (
                'id' => 132,
                'employee_id' => 69,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-12-23 00:29:48',
                'updated_at' => '2023-12-23 00:29:48',
            ),
            132 => 
            array (
                'id' => 133,
                'employee_id' => 70,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-12-23 00:32:06',
                'updated_at' => '2023-12-23 00:32:06',
            ),
            133 => 
            array (
                'id' => 134,
                'employee_id' => 71,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-12-23 01:08:39',
                'updated_at' => '2023-12-23 01:08:39',
            ),
            134 => 
            array (
                'id' => 135,
                'employee_id' => 72,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-12-23 01:28:47',
                'updated_at' => '2023-12-23 01:28:47',
            ),
            135 => 
            array (
                'id' => 136,
                'employee_id' => 73,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-12-23 01:33:13',
                'updated_at' => '2023-12-23 01:33:13',
            ),
            136 => 
            array (
                'id' => 137,
                'employee_id' => 17,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-12-23 01:37:49',
                'updated_at' => '2023-12-23 01:37:49',
            ),
            137 => 
            array (
                'id' => 138,
                'employee_id' => 181,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak',
                'created_at' => '2023-12-23 01:44:46',
                'updated_at' => '2023-12-23 01:44:46',
            ),
            138 => 
            array (
                'id' => 139,
                'employee_id' => 74,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2023-12-23 02:00:17',
                'updated_at' => '2023-12-23 02:00:17',
            ),
            139 => 
            array (
                'id' => 140,
                'employee_id' => 61,
                'condition' => 'sehat',
                'description' => 'tidak',
                'description_2' => 'tidak',
                'created_at' => '2023-12-28 13:00:19',
                'updated_at' => '2023-12-28 13:00:19',
            ),
            140 => 
            array (
                'id' => 141,
                'employee_id' => 185,
                'condition' => 'Baik',
                'description' => 'Tidak',
                'description_2' => 'Todak ada',
                'created_at' => '2024-01-05 18:26:20',
                'updated_at' => '2024-01-05 18:26:20',
            ),
            141 => 
            array (
                'id' => 142,
                'employee_id' => 191,
                'condition' => 'Sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2024-01-08 22:47:33',
                'updated_at' => '2024-01-08 22:47:33',
            ),
            142 => 
            array (
                'id' => 143,
                'employee_id' => 190,
                'condition' => 'Kondisi Kesehatan saya sekarang sehat',
                'description' => 'Tidak pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2024-01-09 01:27:39',
                'updated_at' => '2024-01-09 01:27:39',
            ),
            143 => 
            array (
                'id' => 144,
                'employee_id' => 189,
                'condition' => 'sehat',
                'description' => 'tidak pernah',
                'description_2' => 'tidak ada',
                'created_at' => '2024-01-10 17:16:05',
                'updated_at' => '2024-01-10 17:16:05',
            ),
            144 => 
            array (
                'id' => 145,
                'employee_id' => 59,
                'condition' => 'Alhamdulillah sehat',
                'description' => 'Tidak',
                'description_2' => 'Tidak',
                'created_at' => '2024-01-23 17:26:10',
                'updated_at' => '2024-01-23 17:26:10',
            ),
            145 => 
            array (
                'id' => 146,
                'employee_id' => 49,
                'condition' => 'Cukup Baik',
                'description' => 'Pernah mengalami kecelakaan lengan patah',
                'description_2' => 'Pergerakan lengan belum sempurna',
                'created_at' => '2024-01-26 13:19:35',
                'updated_at' => '2024-01-26 13:19:35',
            ),
            146 => 
            array (
                'id' => 147,
                'employee_id' => 197,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak ada',
                'created_at' => '2024-02-27 23:45:07',
                'updated_at' => '2024-02-27 23:45:07',
            ),
            147 => 
            array (
                'id' => 148,
                'employee_id' => 198,
                'condition' => 'SEHAT',
                'description' => 'TIDAK PERNAH',
                'description_2' => 'TIDAK ADA',
                'created_at' => '2024-02-28 00:26:58',
                'updated_at' => '2024-02-28 00:26:58',
            ),
            148 => 
            array (
                'id' => 149,
                'employee_id' => 199,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak Ada',
                'created_at' => '2024-03-21 16:38:40',
                'updated_at' => '2024-03-21 16:38:40',
            ),
            149 => 
            array (
                'id' => 150,
                'employee_id' => 200,
                'condition' => 'Sehat',
                'description' => 'Tidak Pernah',
                'description_2' => 'Tidak Ada',
                'created_at' => '2024-04-02 17:40:12',
                'updated_at' => '2024-04-02 17:40:12',
            ),
            150 => 
            array (
                'id' => 151,
                'employee_id' => 201,
                'condition' => 'SEHAT',
                'description' => 'TIDAK PERNAH',
                'description_2' => 'TIDAK ADA',
                'created_at' => '2024-04-02 18:16:35',
                'updated_at' => '2024-04-02 18:16:35',
            ),
        ));
        
        
    }
}