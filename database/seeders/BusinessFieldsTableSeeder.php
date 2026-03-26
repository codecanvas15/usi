<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BusinessFieldsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('business_fields')->delete();
        
        \DB::table('business_fields')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Vendor Umum',
                'created_at' => '2022-12-24 00:29:04',
                'updated_at' => '2023-09-28 16:32:40',
                'deleted_at' => '2023-09-28 16:32:40',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'VENDOR ATK',
                'created_at' => '2023-01-17 17:20:30',
                'updated_at' => '2023-09-28 16:32:47',
                'deleted_at' => '2023-09-28 16:32:47',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Vendor Material',
                'created_at' => '2023-01-17 18:46:25',
                'updated_at' => '2023-09-28 16:32:53',
                'deleted_at' => '2023-09-28 16:32:53',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Vendor Pipa',
                'created_at' => '2023-01-17 18:47:27',
                'updated_at' => '2023-01-17 18:47:50',
                'deleted_at' => '2023-01-17 18:47:50',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Vendor Listrik',
                'created_at' => '2023-01-17 18:48:29',
                'updated_at' => '2023-09-28 16:33:03',
                'deleted_at' => '2023-09-28 16:33:03',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Vendor Komputer',
                'created_at' => '2023-01-19 21:14:50',
                'updated_at' => '2023-09-28 16:33:24',
                'deleted_at' => '2023-09-28 16:33:24',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Bengkel',
                'created_at' => '2023-01-19 21:16:18',
                'updated_at' => '2023-09-28 16:33:18',
                'deleted_at' => '2023-09-28 16:33:18',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Dealer Mobil',
                'created_at' => '2023-01-31 18:34:19',
                'updated_at' => '2023-09-28 16:33:12',
                'deleted_at' => '2023-09-28 16:33:12',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Financing / Leasing',
                'created_at' => '2023-01-31 20:01:05',
                'updated_at' => '2023-10-17 17:15:28',
                'deleted_at' => '2023-10-17 17:15:28',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Galangan',
                'created_at' => '2023-01-31 22:43:52',
                'updated_at' => '2023-09-28 16:32:31',
                'deleted_at' => '2023-09-28 16:32:31',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Asuransi',
                'created_at' => '2023-01-31 22:44:05',
                'updated_at' => '2023-10-17 17:15:51',
                'deleted_at' => '2023-10-17 17:15:51',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Laboratorium',
                'created_at' => '2023-01-31 22:44:26',
                'updated_at' => '2023-09-28 16:31:50',
                'deleted_at' => '2023-09-28 16:31:50',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'Vendor Tour And Travel',
                'created_at' => '2023-02-22 18:39:45',
                'updated_at' => '2023-09-28 16:32:17',
                'deleted_at' => '2023-09-28 16:32:17',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'Apotik',
                'created_at' => '2023-02-22 18:54:20',
                'updated_at' => '2023-09-28 16:31:36',
                'deleted_at' => '2023-09-28 16:31:36',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'Transportir',
                'created_at' => '2023-03-03 18:27:59',
                'updated_at' => '2023-10-17 17:15:46',
                'deleted_at' => '2023-10-17 17:15:46',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'Cat',
                'created_at' => '2023-03-03 22:47:36',
                'updated_at' => '2023-09-28 16:31:23',
                'deleted_at' => '2023-09-28 16:31:23',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'Fuel Supplier',
                'created_at' => '2023-03-08 18:30:51',
                'updated_at' => '2023-10-17 17:15:39',
                'deleted_at' => '2023-10-17 17:15:39',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'Agen',
                'created_at' => '2023-03-14 21:10:31',
                'updated_at' => '2023-09-28 16:33:51',
                'deleted_at' => '2023-09-28 16:33:51',
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'Elekronik',
                'created_at' => '2023-03-15 21:36:11',
                'updated_at' => '2023-10-17 17:15:13',
                'deleted_at' => '2023-10-17 17:15:13',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'Spare Part',
                'created_at' => '2023-03-15 21:36:31',
                'updated_at' => '2023-09-28 16:31:17',
                'deleted_at' => '2023-09-28 16:31:17',
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'Otomotif',
                'created_at' => '2023-03-15 21:37:01',
                'updated_at' => '2023-10-17 17:15:07',
                'deleted_at' => '2023-10-17 17:15:07',
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'ATK',
                'created_at' => '2023-03-15 21:39:58',
                'updated_at' => '2023-09-28 16:31:03',
                'deleted_at' => '2023-09-28 16:31:03',
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'Fabrikasi',
                'created_at' => '2023-05-23 00:24:40',
                'updated_at' => '2023-10-17 17:15:01',
                'deleted_at' => '2023-10-17 17:15:01',
            ),
            23 => 
            array (
                'id' => 24,
                'name' => 'Kontraktor',
                'created_at' => '2023-05-31 18:08:57',
                'updated_at' => '2023-10-17 17:14:53',
                'deleted_at' => '2023-10-17 17:14:53',
            ),
            24 => 
            array (
                'id' => 25,
                'name' => 'Professional',
                'created_at' => '2023-06-28 22:40:40',
                'updated_at' => '2023-10-17 17:14:46',
                'deleted_at' => '2023-10-17 17:14:46',
            ),
            25 => 
            array (
                'id' => 26,
                'name' => 'Jasa Standarisasi & Kalibrasi',
                'created_at' => '2023-07-07 17:04:03',
                'updated_at' => '2023-10-17 17:14:40',
                'deleted_at' => '2023-10-17 17:14:40',
            ),
            26 => 
            array (
                'id' => 27,
                'name' => 'A. Pertanian, Kehutanan, Perikanan',
                'created_at' => '2023-10-17 17:16:34',
                'updated_at' => '2023-10-17 17:16:34',
                'deleted_at' => NULL,
            ),
            27 => 
            array (
                'id' => 28,
                'name' => 'B. Pertambangan dan Penggalian',
                'created_at' => '2023-10-17 17:16:55',
                'updated_at' => '2023-10-17 17:16:55',
                'deleted_at' => NULL,
            ),
            28 => 
            array (
                'id' => 29,
                'name' => 'C. Industri Pengolahan',
                'created_at' => '2023-10-17 17:17:13',
                'updated_at' => '2023-10-17 17:17:13',
                'deleted_at' => NULL,
            ),
            29 => 
            array (
                'id' => 30,
                'name' => 'D. Pengadaan Listrik, Gas, Uap / Air Panas dan Udara Dingin',
                'created_at' => '2023-10-17 17:17:51',
                'updated_at' => '2023-10-17 17:17:51',
                'deleted_at' => NULL,
            ),
            30 => 
            array (
                'id' => 31,
                'name' => 'E. Pengadaan Air, Pengolahan Sampah, Daur Ulang, Pembuangan, Limbah',
                'created_at' => '2023-10-17 17:18:37',
                'updated_at' => '2023-10-17 17:18:37',
                'deleted_at' => NULL,
            ),
            31 => 
            array (
                'id' => 32,
                'name' => 'F. Konstruksi',
                'created_at' => '2023-10-17 17:18:51',
                'updated_at' => '2023-10-17 17:18:51',
                'deleted_at' => NULL,
            ),
            32 => 
            array (
                'id' => 33,
                'name' => 'G. Perdagangan Besar dan Eceran, Reparasi & Perawatan Monil, Sepeda Motor',
                'created_at' => '2023-10-17 17:19:51',
                'updated_at' => '2023-10-17 17:19:51',
                'deleted_at' => NULL,
            ),
            33 => 
            array (
                'id' => 34,
                'name' => 'H. Transportasi & Pergudangan',
                'created_at' => '2023-10-17 17:20:10',
                'updated_at' => '2023-10-17 17:20:10',
                'deleted_at' => NULL,
            ),
            34 => 
            array (
                'id' => 35,
                'name' => 'I. Penyediaan Akomodasi & Penyediaan Makan Minum',
                'created_at' => '2023-10-17 17:20:36',
                'updated_at' => '2023-10-17 17:20:36',
                'deleted_at' => NULL,
            ),
            35 => 
            array (
                'id' => 36,
                'name' => 'J. Informasi & Komunikasi',
                'created_at' => '2023-10-17 17:20:51',
                'updated_at' => '2023-10-17 17:20:51',
                'deleted_at' => NULL,
            ),
            36 => 
            array (
                'id' => 37,
                'name' => 'K. Jasa Keuangan & Asuransi',
                'created_at' => '2023-10-17 17:21:06',
                'updated_at' => '2023-10-17 17:21:06',
                'deleted_at' => NULL,
            ),
            37 => 
            array (
                'id' => 38,
                'name' => 'L. Real Estate',
                'created_at' => '2023-10-17 17:21:18',
                'updated_at' => '2023-10-17 17:21:18',
                'deleted_at' => NULL,
            ),
            38 => 
            array (
                'id' => 39,
                'name' => 'M. Jasa Profesional, Ilmiah, Tehnis',
                'created_at' => '2023-10-17 17:21:46',
                'updated_at' => '2023-10-17 17:21:46',
                'deleted_at' => NULL,
            ),
            39 => 
            array (
                'id' => 40,
                'name' => 'N. Jasa Persewaan, Ketenagakerjaan, Agen Perjalanan, Penunjang Usaha Lainnya',
                'created_at' => '2023-10-17 17:22:16',
                'updated_at' => '2023-10-17 17:22:16',
                'deleted_at' => NULL,
            ),
            40 => 
            array (
                'id' => 41,
                'name' => 'O. Administrasi Pemerintah & Jaminan Sosial Wajib',
                'created_at' => '2023-10-17 17:22:40',
                'updated_at' => '2023-10-17 17:22:40',
                'deleted_at' => NULL,
            ),
            41 => 
            array (
                'id' => 42,
                'name' => 'P. Jasa Pendidikan',
                'created_at' => '2023-10-17 17:22:53',
                'updated_at' => '2023-10-17 17:22:53',
                'deleted_at' => NULL,
            ),
            42 => 
            array (
                'id' => 43,
                'name' => 'Q. Jasa Kesehatan & Kegiatan Sosial',
                'created_at' => '2023-10-17 17:23:11',
                'updated_at' => '2023-10-17 17:23:11',
                'deleted_at' => NULL,
            ),
            43 => 
            array (
                'id' => 44,
                'name' => 'R. Kebudayaan, Hiburan, Rekreasi',
                'created_at' => '2023-10-17 17:23:29',
                'updated_at' => '2023-10-17 17:23:42',
                'deleted_at' => NULL,
            ),
            44 => 
            array (
                'id' => 45,
                'name' => 'S. Kegiatan Jasa Lainnya',
                'created_at' => '2023-10-17 17:23:56',
                'updated_at' => '2023-10-17 17:23:56',
                'deleted_at' => NULL,
            ),
            45 => 
            array (
                'id' => 46,
                'name' => 'T. Jasa Perorangan Layanan Rmh Tangga, Kegiatan yg Menghasilan Barang & Jasa',
                'created_at' => '2023-10-17 17:24:38',
                'updated_at' => '2023-10-17 17:24:38',
                'deleted_at' => NULL,
            ),
            46 => 
            array (
                'id' => 47,
                'name' => 'U. Kegiatan Badan Internasional & Badan Ekstra Internasional Lainnya',
                'created_at' => '2023-10-17 17:25:06',
                'updated_at' => '2023-10-17 17:25:06',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}