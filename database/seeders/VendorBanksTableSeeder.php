<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class VendorBanksTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('vendor_banks')->delete();
        
        \DB::table('vendor_banks')->insert(array (
            0 => 
            array (
                'id' => 1,
                'vendor_id' => 18,
                'name' => 'Bank Mandiri',
                'account_number' => '1090533555777',
                'behalf_of' => 'PT. Mitra Utama Energi',
                'deleted_at' => NULL,
                'created_at' => '2023-10-26 20:59:57',
                'updated_at' => '2023-10-26 20:59:57',
            ),
            1 => 
            array (
                'id' => 2,
                'vendor_id' => 19,
                'name' => 'Bank Mandiri',
                'account_number' => '122-00-0888850-8',
                'behalf_of' => 'PT. INTIM PUTRA PERKASA',
                'deleted_at' => NULL,
                'created_at' => '2023-10-26 21:01:39',
                'updated_at' => '2023-10-26 21:01:39',
            ),
            2 => 
            array (
                'id' => 3,
                'vendor_id' => 20,
                'name' => 'Bank Mandiri',
                'account_number' => '1090500006663',
                'behalf_of' => 'PT. GLOBAL PETRO PASIFIC',
                'deleted_at' => NULL,
                'created_at' => '2023-10-26 21:03:14',
                'updated_at' => '2023-10-26 21:03:14',
            ),
            3 => 
            array (
                'id' => 4,
                'vendor_id' => 21,
                'name' => 'Bank Mandiri',
                'account_number' => '1140011302158',
                'behalf_of' => 'PT. DHARMA MITRA PETROLINDO',
                'deleted_at' => NULL,
                'created_at' => '2023-10-26 21:04:48',
                'updated_at' => '2023-10-26 21:04:48',
            ),
            4 => 
            array (
                'id' => 5,
                'vendor_id' => 22,
                'name' => 'Bank Mandiri',
                'account_number' => '125.000.5454764',
                'behalf_of' => 'PT. LINTAS SAMUDRA LINE',
                'deleted_at' => NULL,
                'created_at' => '2023-10-26 21:05:43',
                'updated_at' => '2023-10-26 21:05:43',
            ),
            5 => 
            array (
                'id' => 6,
                'vendor_id' => 23,
                'name' => 'Bank Mandiri',
                'account_number' => '1500010608246',
                'behalf_of' => 'PT. Yoezhadassah',
                'deleted_at' => NULL,
                'created_at' => '2023-10-26 22:32:51',
                'updated_at' => '2023-10-26 22:32:51',
            ),
            6 => 
            array (
                'id' => 7,
                'vendor_id' => 24,
                'name' => 'Bank Mandiri',
                'account_number' => '1090533777777',
                'behalf_of' => 'PT. Mitra Maritim Mandiri',
                'deleted_at' => NULL,
                'created_at' => '2023-10-26 22:34:29',
                'updated_at' => '2023-10-26 22:34:29',
            ),
            7 => 
            array (
                'id' => 8,
                'vendor_id' => 25,
                'name' => 'Bank Mandiri',
                'account_number' => '0310016997572',
                'behalf_of' => 'PT. INTERNUSA REBECCA ENERGY',
                'deleted_at' => NULL,
                'created_at' => '2023-10-26 22:35:40',
                'updated_at' => '2023-10-26 22:35:40',
            ),
            8 => 
            array (
                'id' => 9,
                'vendor_id' => 26,
                'name' => 'Bank Mandiri',
                'account_number' => '1520018000048',
                'behalf_of' => 'PT. Sumber Karya Anugerah',
                'deleted_at' => NULL,
                'created_at' => '2023-10-26 22:37:14',
                'updated_at' => '2023-10-26 22:37:14',
            ),
            9 => 
            array (
                'id' => 10,
                'vendor_id' => 27,
                'name' => 'Bank Mandiri',
                'account_number' => '1500005672009',
                'behalf_of' => 'PT Jobroindo Makmur',
                'deleted_at' => NULL,
                'created_at' => '2023-11-16 21:31:58',
                'updated_at' => '2023-11-16 21:31:58',
            ),
            10 => 
            array (
                'id' => 11,
                'vendor_id' => 28,
                'name' => 'Bank Mandiri',
                'account_number' => '1200007950863',
                'behalf_of' => 'PT. DIMAS PUTRA PERTAMA',
                'deleted_at' => NULL,
                'created_at' => '2023-11-16 21:34:04',
                'updated_at' => '2023-11-16 21:34:04',
            ),
            11 => 
            array (
                'id' => 12,
                'vendor_id' => 29,
                'name' => 'Bank Mandiri',
                'account_number' => '1200010763543',
                'behalf_of' => 'PT. Umrindo Mandiri Sejahtera',
                'deleted_at' => NULL,
                'created_at' => '2023-11-21 21:54:36',
                'updated_at' => '2023-11-21 21:54:36',
            ),
            12 => 
            array (
                'id' => 13,
                'vendor_id' => 30,
                'name' => 'Bank Mandiri',
                'account_number' => '1200011068660',
                'behalf_of' => 'PT DIMAS PUTRA MANDIRI',
                'deleted_at' => NULL,
                'created_at' => '2023-12-01 23:40:42',
                'updated_at' => '2023-12-01 23:40:42',
            ),
            13 => 
            array (
                'id' => 14,
                'vendor_id' => 31,
                'name' => 'Bank Mandiri',
                'account_number' => '1550006612744',
                'behalf_of' => 'PT. Makmur Jaya Energi',
                'deleted_at' => NULL,
                'created_at' => '2023-12-14 00:47:54',
                'updated_at' => '2023-12-14 00:47:54',
            ),
            14 => 
            array (
                'id' => 15,
                'vendor_id' => 33,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '814731002641',
                'behalf_of' => 'PT. UNITED SHIPPING INDONESIA',
                'deleted_at' => NULL,
                'created_at' => '2024-01-11 21:29:23',
                'updated_at' => '2024-01-11 21:29:23',
            ),
            15 => 
            array (
                'id' => 16,
                'vendor_id' => 34,
                'name' => 'BANK NEGARA INDONESIA',
                'account_number' => '6117888999',
                'behalf_of' => 'PT. JAMATEJA SERVICE INDONESIA',
                'deleted_at' => NULL,
                'created_at' => '2024-01-11 21:50:27',
                'updated_at' => '2024-01-11 21:50:27',
            ),
            16 => 
            array (
                'id' => 17,
                'vendor_id' => 35,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '4649798998',
                'behalf_of' => 'CV PRIMA SOLUSI PARTS',
                'deleted_at' => NULL,
                'created_at' => '2024-01-11 21:56:02',
                'updated_at' => '2024-01-11 21:56:02',
            ),
            17 => 
            array (
                'id' => 18,
                'vendor_id' => 36,
                'name' => 'BANK RAKYAT INDONESIA',
                'account_number' => '036001000844305',
                'behalf_of' => 'PT. Bintang Alam Sentosa',
                'deleted_at' => NULL,
                'created_at' => '2024-01-17 17:52:39',
                'updated_at' => '2024-02-06 21:16:38',
            ),
            18 => 
            array (
                'id' => 19,
                'vendor_id' => 37,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '2580815963',
                'behalf_of' => 'Cecilia Erni Septibagio',
                'deleted_at' => NULL,
                'created_at' => '2024-01-17 22:33:31',
                'updated_at' => '2024-01-17 22:33:31',
            ),
            19 => 
            array (
                'id' => 20,
                'vendor_id' => 38,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '0188320000',
                'behalf_of' => 'PT. SENTRALSARI PRIMASENTOSA',
                'deleted_at' => NULL,
                'created_at' => '2024-01-18 21:48:11',
                'updated_at' => '2024-01-18 21:48:11',
            ),
            20 => 
            array (
                'id' => 21,
                'vendor_id' => 55,
                'name' => 'BANK RAKYAT INDONESIA',
                'account_number' => '002301098185509',
                'behalf_of' => 'ZAINAL ABIDIN',
                'deleted_at' => NULL,
                'created_at' => '2024-01-22 18:30:58',
                'updated_at' => '2024-01-22 18:30:58',
            ),
            21 => 
            array (
                'id' => 22,
                'vendor_id' => 56,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '0885106094',
                'behalf_of' => 'PT TITIPAN KILAT SURABAYA',
                'deleted_at' => NULL,
                'created_at' => '2024-01-23 20:57:25',
                'updated_at' => '2024-01-23 20:57:25',
            ),
            22 => 
            array (
                'id' => 23,
                'vendor_id' => 11,
                'name' => 'BCA',
                'account_number' => '8291877788',
                'behalf_of' => 'PT. USI PETROTRANS ENERGI',
                'deleted_at' => NULL,
                'created_at' => '2024-01-24 21:21:06',
                'updated_at' => '2024-01-24 21:21:06',
            ),
            23 => 
            array (
                'id' => 24,
                'vendor_id' => 49,
                'name' => 'BCA',
                'account_number' => '0883999409',
                'behalf_of' => 'pedoman dirgantara travel',
                'deleted_at' => NULL,
                'created_at' => '2024-01-24 22:38:38',
                'updated_at' => '2024-01-24 22:38:38',
            ),
            24 => 
            array (
                'id' => 25,
                'vendor_id' => 4,
                'name' => 'BCA',
                'account_number' => '4645029888',
                'behalf_of' => 'CV SENTRAL JAYA TEHNIK',
                'deleted_at' => NULL,
                'created_at' => '2024-01-25 00:59:23',
                'updated_at' => '2024-01-25 00:59:23',
            ),
            25 => 
            array (
                'id' => 26,
                'vendor_id' => 47,
                'name' => 'SINARMAS',
                'account_number' => '805220029502',
                'behalf_of' => 'KBRU QQ UNITED SHIPPING INDONESIA',
                'deleted_at' => NULL,
                'created_at' => '2024-01-25 17:02:44',
                'updated_at' => '2024-01-25 17:02:44',
            ),
            26 => 
            array (
                'id' => 27,
                'vendor_id' => 47,
                'name' => 'PERMATA',
                'account_number' => '702567726',
                'behalf_of' => 'PT KALIBESAR RAYA UTAMA',
                'deleted_at' => NULL,
                'created_at' => '2024-01-25 17:04:53',
                'updated_at' => '2024-01-25 17:04:53',
            ),
            27 => 
            array (
                'id' => 28,
                'vendor_id' => 42,
                'name' => 'BCA',
                'account_number' => '4647478998',
                'behalf_of' => 'CV SOLUSITEK UTAMA',
                'deleted_at' => NULL,
                'created_at' => '2024-01-25 18:49:14',
                'updated_at' => '2024-01-25 18:49:14',
            ),
            28 => 
            array (
                'id' => 29,
                'vendor_id' => 10,
                'name' => 'BCA',
                'account_number' => '4640244777',
                'behalf_of' => 'CV. PRIMA SOLUSI TEKNIK',
                'deleted_at' => NULL,
                'created_at' => '2024-01-25 18:50:36',
                'updated_at' => '2024-01-25 18:50:36',
            ),
            29 => 
            array (
                'id' => 30,
                'vendor_id' => 57,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '7900678368',
                'behalf_of' => 'HERRY ISWANTO',
                'deleted_at' => NULL,
                'created_at' => '2024-01-26 21:19:31',
                'updated_at' => '2024-01-26 21:19:31',
            ),
            30 => 
            array (
                'id' => 31,
                'vendor_id' => 58,
                'name' => 'KODE BILLING',
                'account_number' => 'KODE BILLING',
                'behalf_of' => 'KODE BILLING',
                'deleted_at' => NULL,
                'created_at' => '2024-01-31 21:03:53',
                'updated_at' => '2024-01-31 21:03:53',
            ),
            31 => 
            array (
                'id' => 32,
                'vendor_id' => 59,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '4291761717',
                'behalf_of' => 'NUGROHO SUSANTO',
                'deleted_at' => NULL,
                'created_at' => '2024-02-01 23:36:32',
                'updated_at' => '2024-02-01 23:36:32',
            ),
            32 => 
            array (
                'id' => 33,
                'vendor_id' => 59,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '3641171717',
                'behalf_of' => 'BISMA ANORAGA PT',
                'deleted_at' => NULL,
                'created_at' => '2024-02-07 23:40:16',
                'updated_at' => '2024-02-07 23:40:16',
            ),
            33 => 
            array (
                'id' => 34,
                'vendor_id' => 53,
                'name' => 'MANDIRI',
                'account_number' => '1200010408487',
                'behalf_of' => 'PT. Raesa Permata Jaya Line',
                'deleted_at' => NULL,
                'created_at' => '2024-02-19 18:30:02',
                'updated_at' => '2024-02-19 18:30:02',
            ),
            34 => 
            array (
                'id' => 35,
                'vendor_id' => 60,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '6170987444',
                'behalf_of' => 'CV. ROBELIN KARYA INDONESIA',
                'deleted_at' => NULL,
                'created_at' => '2024-02-22 00:05:16',
                'updated_at' => '2024-02-22 00:05:16',
            ),
            35 => 
            array (
                'id' => 36,
                'vendor_id' => 61,
                'name' => 'VIRTUAL ACCOUNT',
                'account_number' => 'VIRTUAL ACCOUNT',
                'behalf_of' => 'VIRTUAL ACCOUNT',
                'deleted_at' => NULL,
                'created_at' => '2024-02-22 21:20:37',
                'updated_at' => '2024-02-22 21:20:37',
            ),
            36 => 
            array (
                'id' => 37,
                'vendor_id' => 62,
                'name' => 'CASH',
                'account_number' => 'CASH',
                'behalf_of' => 'CASH',
                'deleted_at' => NULL,
                'created_at' => '2024-02-22 21:51:19',
                'updated_at' => '2024-02-22 21:51:19',
            ),
            37 => 
            array (
                'id' => 38,
                'vendor_id' => 3,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '4644277200',
                'behalf_of' => 'CV. MULTI JASINDO',
                'deleted_at' => NULL,
                'created_at' => '2024-02-27 17:04:28',
                'updated_at' => '2024-02-27 17:04:28',
            ),
            38 => 
            array (
                'id' => 39,
                'vendor_id' => 12,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => 'PT. USI PETROTRANS SAMUDRA',
                'behalf_of' => '8291177778',
                'deleted_at' => NULL,
                'created_at' => '2024-02-28 01:02:33',
                'updated_at' => '2024-02-28 01:02:33',
            ),
            39 => 
            array (
                'id' => 40,
                'vendor_id' => 63,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '0886616168',
                'behalf_of' => 'PT. BERKAT LANGGENG SUKSES SEJATI',
                'deleted_at' => NULL,
                'created_at' => '2024-02-29 22:11:06',
                'updated_at' => '2024-02-29 22:11:06',
            ),
            40 => 
            array (
                'id' => 41,
                'vendor_id' => 32,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '2582667668',
                'behalf_of' => 'WIDARTA CHANDRA',
                'deleted_at' => NULL,
                'created_at' => '2024-03-02 00:18:18',
                'updated_at' => '2024-03-02 00:18:18',
            ),
            41 => 
            array (
                'id' => 42,
                'vendor_id' => 64,
                'name' => 'BANK BCA',
                'account_number' => '7160110001',
                'behalf_of' => 'PT. INDOMOBIL PRIMA NIAGA',
                'deleted_at' => NULL,
                'created_at' => '2024-03-05 16:35:41',
                'updated_at' => '2024-03-05 16:35:41',
            ),
            42 => 
            array (
                'id' => 43,
                'vendor_id' => 65,
                'name' => 'BANK MANDIRI',
                'account_number' => '1240002148592',
                'behalf_of' => 'PT. Synergy Risk Management Consultans',
                'deleted_at' => NULL,
                'created_at' => '2024-03-05 22:02:06',
                'updated_at' => '2024-03-05 22:02:06',
            ),
            43 => 
            array (
                'id' => 44,
                'vendor_id' => 66,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '4700103345',
                'behalf_of' => 'Yenny Limina',
                'deleted_at' => NULL,
                'created_at' => '2024-03-06 16:35:25',
                'updated_at' => '2024-03-06 16:35:25',
            ),
            44 => 
            array (
                'id' => 45,
                'vendor_id' => 67,
                'name' => 'VIRTUAL ACCOUNT',
                'account_number' => 'VIRTUAL ACCOUNT',
                'behalf_of' => 'VIRTUAL ACCOUNT',
                'deleted_at' => NULL,
                'created_at' => '2024-03-08 18:44:25',
                'updated_at' => '2024-03-08 18:44:25',
            ),
            45 => 
            array (
                'id' => 46,
                'vendor_id' => 68,
                'name' => 'VIRTUAL ACCOUNT',
                'account_number' => 'VIRTUAL ACCOUNT',
                'behalf_of' => 'VIRTUAL ACCOUNT',
                'deleted_at' => NULL,
                'created_at' => '2024-03-08 18:46:22',
                'updated_at' => '2024-03-08 18:46:22',
            ),
            46 => 
            array (
                'id' => 47,
                'vendor_id' => 69,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '0884873581',
                'behalf_of' => 'Tjio Lily Indrawati',
                'deleted_at' => NULL,
                'created_at' => '2024-03-13 18:56:37',
                'updated_at' => '2024-03-13 18:56:37',
            ),
            47 => 
            array (
                'id' => 48,
                'vendor_id' => 70,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '4683818545',
                'behalf_of' => 'Sitaresmi Puspadewi S',
                'deleted_at' => NULL,
                'created_at' => '2024-03-13 18:59:15',
                'updated_at' => '2024-03-13 18:59:15',
            ),
            48 => 
            array (
                'id' => 49,
                'vendor_id' => 71,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '1010849809',
                'behalf_of' => 'The Mei Fung',
                'deleted_at' => NULL,
                'created_at' => '2024-03-13 23:29:24',
                'updated_at' => '2024-03-13 23:29:24',
            ),
            49 => 
            array (
                'id' => 50,
                'vendor_id' => 72,
                'name' => 'BANK NEGARA INDONESIA',
                'account_number' => 'VIRTUAL ACCOUNT',
                'behalf_of' => 'VIRTUAL ACCOUNT',
                'deleted_at' => NULL,
                'created_at' => '2024-03-14 17:55:30',
                'updated_at' => '2024-03-14 17:55:30',
            ),
            50 => 
            array (
                'id' => 51,
                'vendor_id' => 73,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '4638566778',
                'behalf_of' => 'CV. Omgs Indonesia',
                'deleted_at' => NULL,
                'created_at' => '2024-03-18 21:14:06',
                'updated_at' => '2024-03-18 21:14:06',
            ),
            51 => 
            array (
                'id' => 52,
                'vendor_id' => 73,
                'name' => 'BANK PANIN',
                'account_number' => '4022013923',
                'behalf_of' => 'CV. Omgs Indonesia',
                'deleted_at' => NULL,
                'created_at' => '2024-03-18 21:14:06',
                'updated_at' => '2024-03-18 21:14:06',
            ),
            52 => 
            array (
                'id' => 53,
                'vendor_id' => 74,
                'name' => 'BANK NEGARA INDONESIA',
                'account_number' => '7272828273',
            'behalf_of' => 'PT. Pertamina Marine Solutions (PMSOL)',
                'deleted_at' => NULL,
                'created_at' => '2024-03-21 17:41:42',
                'updated_at' => '2024-03-21 17:41:42',
            ),
            53 => 
            array (
                'id' => 54,
                'vendor_id' => 75,
                'name' => 'CIMB Niaga',
                'account_number' => '706282679600',
                'behalf_of' => 'Sudirman',
                'deleted_at' => NULL,
                'created_at' => '2024-03-26 19:18:48',
                'updated_at' => '2024-03-26 19:18:48',
            ),
            54 => 
            array (
                'id' => 55,
                'vendor_id' => 76,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '7240071007',
                'behalf_of' => 'Liong Wei Siong',
                'deleted_at' => NULL,
                'created_at' => '2024-03-28 18:52:33',
                'updated_at' => '2024-03-28 20:43:57',
            ),
            55 => 
            array (
                'id' => 56,
                'vendor_id' => 77,
                'name' => '-',
                'account_number' => '-',
                'behalf_of' => '-',
                'deleted_at' => NULL,
                'created_at' => '2024-03-28 18:57:21',
                'updated_at' => '2024-03-28 18:57:21',
            ),
            56 => 
            array (
                'id' => 57,
                'vendor_id' => 78,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '7961999000',
                'behalf_of' => 'PT RAJAWALI DIESEL INDONESIA',
                'deleted_at' => NULL,
                'created_at' => '2024-04-03 15:57:16',
                'updated_at' => '2024-04-03 15:57:16',
            ),
            57 => 
            array (
                'id' => 58,
                'vendor_id' => 78,
                'name' => 'BANK MANDIRI',
                'account_number' => '1360032776699',
                'behalf_of' => 'PT RAJAWALI DIESEL INDONESIA',
                'deleted_at' => NULL,
                'created_at' => '2024-04-03 15:57:16',
                'updated_at' => '2024-04-03 15:57:16',
            ),
            58 => 
            array (
                'id' => 59,
                'vendor_id' => 79,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '0149167899',
                'behalf_of' => 'PT. DAMAI SEJAHTERA ABADI',
                'deleted_at' => NULL,
                'created_at' => '2024-04-03 16:51:25',
                'updated_at' => '2024-04-03 16:51:25',
            ),
            59 => 
            array (
                'id' => 60,
                'vendor_id' => 80,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '8622903333',
                'behalf_of' => 'PT. ERA GLOBAL NUSINDO',
                'deleted_at' => NULL,
                'created_at' => '2024-04-26 17:50:16',
                'updated_at' => '2024-04-26 17:50:16',
            ),
            60 => 
            array (
                'id' => 61,
                'vendor_id' => 81,
                'name' => 'BANK SYARIAH INDONESIA',
                'account_number' => '7084488148',
                'behalf_of' => 'KJPP SATRIA SETIAWAN DAN REKAN',
                'deleted_at' => NULL,
                'created_at' => '2024-05-07 10:45:39',
                'updated_at' => '2024-05-07 10:45:39',
            ),
            61 => 
            array (
                'id' => 62,
                'vendor_id' => 82,
                'name' => 'BANK MANDIRI',
                'account_number' => '0310005973949',
                'behalf_of' => 'PT. MASADA JAYA LINES',
                'deleted_at' => NULL,
                'created_at' => '2024-05-07 16:32:53',
                'updated_at' => '2024-05-07 16:32:53',
            ),
            62 => 
            array (
                'id' => 63,
                'vendor_id' => 83,
                'name' => 'BANK CENTRAL ASIA',
                'account_number' => '8640675757',
                'behalf_of' => 'CV. PUTRA SURYA PERDANA',
                'deleted_at' => NULL,
                'created_at' => '2024-05-08 13:28:00',
                'updated_at' => '2024-05-08 13:28:00',
            ),
            63 => 
            array (
                'id' => 64,
                'vendor_id' => 83,
                'name' => 'BANK RAKYAT INDONESIA',
                'account_number' => '1101003057300',
                'behalf_of' => 'CV. PUTRA SURYA PERDANA',
                'deleted_at' => NULL,
                'created_at' => '2024-05-08 13:28:00',
                'updated_at' => '2024-05-08 13:28:00',
            ),
        ));
        
        
    }
}