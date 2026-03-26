<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UnitsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('units')->delete();
        
        \DB::table('units')->insert(array (
            0 => 
            array (
                'id' => 201,
                'name' => 'PCS',
                'created_at' => '2023-09-15 00:32:01',
                'updated_at' => '2023-09-15 00:32:01',
                'sort' => 'PCS',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 370,
                'name' => 'Liter',
                'created_at' => '2023-10-18 19:44:20',
                'updated_at' => '2023-10-18 19:44:20',
                'sort' => 'Ltr',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 2745,
                'name' => 'Unit',
                'created_at' => '2023-10-20 18:31:39',
                'updated_at' => '2023-10-20 18:31:41',
                'sort' => 'Unit',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 2746,
                'name' => 'Set',
                'created_at' => '2023-10-20 18:31:39',
                'updated_at' => '2023-10-20 18:31:39',
                'sort' => 'Set',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 2747,
                'name' => 'Pak',
                'created_at' => '2023-10-20 18:31:39',
                'updated_at' => '2023-10-20 18:31:39',
                'sort' => 'Pak',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 2748,
                'name' => 'Dus',
                'created_at' => '2023-10-20 18:31:39',
                'updated_at' => '2023-10-20 18:31:39',
                'sort' => 'Dus',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 2749,
                'name' => 'Lsn',
                'created_at' => '2023-10-20 18:31:39',
                'updated_at' => '2023-10-20 18:31:39',
                'sort' => 'Lsn',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 2750,
                'name' => 'Buku',
                'created_at' => '2023-10-20 18:31:39',
                'updated_at' => '2023-10-20 18:31:39',
                'sort' => 'Buku',
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 2751,
                'name' => 'Roll',
                'created_at' => '2023-10-20 18:31:39',
                'updated_at' => '2023-10-20 18:31:39',
                'sort' => 'Roll',
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'id' => 2752,
                'name' => 'Rim',
                'created_at' => '2023-10-20 18:31:39',
                'updated_at' => '2023-10-20 18:31:39',
                'sort' => 'Rim',
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'id' => 2753,
                'name' => 'Meter',
                'created_at' => '2023-10-20 18:31:40',
                'updated_at' => '2023-10-20 18:31:40',
                'sort' => 'Meter',
                'deleted_at' => NULL,
            ),
            11 => 
            array (
                'id' => 2754,
                'name' => 'Botol',
                'created_at' => '2023-10-20 18:31:40',
                'updated_at' => '2023-10-20 18:31:40',
                'sort' => 'Botol',
                'deleted_at' => NULL,
            ),
            12 => 
            array (
                'id' => 2755,
                'name' => 'Lembar',
                'created_at' => '2023-10-20 18:31:40',
                'updated_at' => '2023-10-20 18:31:40',
                'sort' => 'Lembar',
                'deleted_at' => NULL,
            ),
            13 => 
            array (
                'id' => 2756,
                'name' => 'Mtr',
                'created_at' => '2023-10-20 18:31:40',
                'updated_at' => '2023-10-20 18:31:40',
                'sort' => 'Mtr',
                'deleted_at' => NULL,
            ),
            14 => 
            array (
                'id' => 2757,
                'name' => 'Kg',
                'created_at' => '2023-10-20 18:31:40',
                'updated_at' => '2023-10-20 18:31:40',
                'sort' => 'Kg',
                'deleted_at' => NULL,
            ),
            15 => 
            array (
                'id' => 2758,
                'name' => 'Batang',
                'created_at' => '2023-10-20 18:31:40',
                'updated_at' => '2023-10-20 18:31:40',
                'sort' => 'Batang',
                'deleted_at' => NULL,
            ),
            16 => 
            array (
                'id' => 2759,
                'name' => 'Kaleng',
                'created_at' => '2023-10-20 18:31:40',
                'updated_at' => '2023-10-20 18:31:40',
                'sort' => 'Kaleng',
                'deleted_at' => NULL,
            ),
            17 => 
            array (
                'id' => 2760,
                'name' => 'ml',
                'created_at' => '2023-10-20 18:31:40',
                'updated_at' => '2023-10-20 18:31:40',
                'sort' => 'ml',
                'deleted_at' => NULL,
            ),
            18 => 
            array (
                'id' => 2761,
                'name' => 'Tube',
                'created_at' => '2023-10-20 18:31:40',
                'updated_at' => '2023-10-20 18:31:40',
                'sort' => 'Tube',
                'deleted_at' => NULL,
            ),
            19 => 
            array (
                'id' => 2762,
                'name' => 'LS',
                'created_at' => '2023-10-20 18:31:40',
                'updated_at' => '2023-10-20 18:31:40',
                'sort' => 'LS',
                'deleted_at' => NULL,
            ),
            20 => 
            array (
                'id' => 2763,
                'name' => 'Strp',
                'created_at' => '2023-10-20 18:31:40',
                'updated_at' => '2023-10-20 18:31:40',
                'sort' => 'Strp',
                'deleted_at' => NULL,
            ),
            21 => 
            array (
                'id' => 2764,
                'name' => 'Psg',
                'created_at' => '2023-10-20 18:31:40',
                'updated_at' => '2023-10-20 18:31:40',
                'sort' => 'Psg',
                'deleted_at' => NULL,
            ),
            22 => 
            array (
                'id' => 2765,
                'name' => 'Bks',
                'created_at' => '2023-10-20 18:31:40',
                'updated_at' => '2023-10-20 18:31:40',
                'sort' => 'Bks',
                'deleted_at' => NULL,
            ),
            23 => 
            array (
                'id' => 2766,
                'name' => 'Jrg',
                'created_at' => '2023-10-20 18:31:41',
                'updated_at' => '2023-10-20 18:31:41',
                'sort' => 'Jrg',
                'deleted_at' => NULL,
            ),
            24 => 
            array (
                'id' => 2767,
                'name' => 'Galon',
                'created_at' => '2023-10-20 18:31:41',
                'updated_at' => '2023-10-20 18:31:41',
                'sort' => 'Galon',
                'deleted_at' => NULL,
            ),
            25 => 
            array (
                'id' => 2768,
                'name' => 'Ton',
                'created_at' => '2023-10-20 18:31:41',
                'updated_at' => '2023-10-20 18:31:41',
                'sort' => 'Ton',
                'deleted_at' => NULL,
            ),
            26 => 
            array (
                'id' => 2769,
                'name' => 'Tablet',
                'created_at' => '2024-01-12 20:27:03',
                'updated_at' => '2024-01-12 20:27:03',
                'sort' => 'Tab',
                'deleted_at' => NULL,
            ),
            27 => 
            array (
                'id' => 2770,
                'name' => 'Certificate',
                'created_at' => '2024-01-12 20:51:57',
                'updated_at' => '2024-01-12 20:51:57',
                'sort' => 'Cert',
                'deleted_at' => NULL,
            ),
            28 => 
            array (
                'id' => 2771,
                'name' => 'Cyl',
                'created_at' => '2024-01-12 20:53:04',
                'updated_at' => '2024-01-12 20:53:04',
                'sort' => 'Cyl',
                'deleted_at' => NULL,
            ),
            29 => 
            array (
                'id' => 2772,
                'name' => 'Tabung',
                'created_at' => '2024-01-12 21:45:03',
                'updated_at' => '2024-01-12 21:45:03',
                'sort' => 'Tabung',
                'deleted_at' => NULL,
            ),
            30 => 
            array (
                'id' => 2773,
                'name' => 'Jam',
                'created_at' => '2024-01-16 22:41:59',
                'updated_at' => '2024-01-16 22:41:59',
                'sort' => 'Jam',
                'deleted_at' => NULL,
            ),
            31 => 
            array (
                'id' => 2774,
                'name' => 'Hari',
                'created_at' => '2024-01-16 22:42:09',
                'updated_at' => '2024-01-16 22:42:09',
                'sort' => 'Hari',
                'deleted_at' => NULL,
            ),
            32 => 
            array (
                'id' => 2775,
                'name' => 'Transaksi',
                'created_at' => '2024-01-17 23:36:18',
                'updated_at' => '2024-01-17 23:36:18',
                'sort' => 'Transaksi',
                'deleted_at' => NULL,
            ),
            33 => 
            array (
                'id' => 2776,
                'name' => 'EA',
                'created_at' => '2024-02-01 00:29:12',
                'updated_at' => '2024-02-01 00:29:12',
                'sort' => 'EA',
                'deleted_at' => NULL,
            ),
            34 => 
            array (
                'id' => 2777,
                'name' => 'Bulan',
                'created_at' => '2024-02-01 21:32:29',
                'updated_at' => '2024-02-01 21:32:29',
                'sort' => 'Bulan',
                'deleted_at' => NULL,
            ),
            35 => 
            array (
                'id' => 2778,
                'name' => 'PAX',
                'created_at' => '2024-02-28 17:57:33',
                'updated_at' => '2024-02-28 17:58:03',
                'sort' => 'PAX',
                'deleted_at' => NULL,
            ),
            36 => 
            array (
                'id' => 2779,
                'name' => 'NIGHT',
                'created_at' => '2024-02-28 17:58:15',
                'updated_at' => '2024-02-28 17:58:15',
                'sort' => 'NIGHT',
                'deleted_at' => NULL,
            ),
            37 => 
            array (
                'id' => 2780,
                'name' => 'Kotak',
                'created_at' => '2024-03-13 21:32:14',
                'updated_at' => '2024-03-13 21:32:14',
                'sort' => 'Kotak',
                'deleted_at' => NULL,
            ),
            38 => 
            array (
                'id' => 2781,
                'name' => 'cm',
                'created_at' => '2024-03-18 17:06:43',
                'updated_at' => '2024-03-18 17:07:10',
                'sort' => 'cm',
                'deleted_at' => NULL,
            ),
            39 => 
            array (
                'id' => 2782,
                'name' => 'Lonjor',
                'created_at' => '2024-04-23 17:38:07',
                'updated_at' => '2024-04-23 17:38:07',
                'sort' => 'Lonjor',
                'deleted_at' => NULL,
            ),
            40 => 
            array (
                'id' => 2783,
                'name' => 'Lot',
                'created_at' => '2024-04-23 17:38:55',
                'updated_at' => '2024-04-23 17:38:55',
                'sort' => 'Lot',
                'deleted_at' => NULL,
            ),
            41 => 
            array (
                'id' => 2784,
                'name' => 'Titik',
                'created_at' => '2024-04-23 17:41:50',
                'updated_at' => '2024-04-23 17:41:50',
                'sort' => 'Titik',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}