<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeEmergencyContactsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('employee_emergency_contacts')->delete();
        
        \DB::table('employee_emergency_contacts')->insert(array (
            0 => 
            array (
                'id' => 4,
                'employee_id' => 7,
                'nama' => 'Indri',
                'hubungan' => 'teman',
                'nomor_telepon' => '0817777777',
                'alamat' => 'sby',
                'created_at' => '2023-03-23 18:11:13',
                'updated_at' => '2023-03-23 18:11:13',
            ),
            1 => 
            array (
                'id' => 6,
                'employee_id' => 9,
                'nama' => 'anang',
                'hubungan' => 'ayah',
                'nomor_telepon' => '081234444',
                'alamat' => 'Surabaya',
                'created_at' => '2023-03-23 21:20:30',
                'updated_at' => '2023-03-23 21:20:30',
            ),
            2 => 
            array (
                'id' => 7,
                'employee_id' => 13,
                'nama' => 'joan',
                'hubungan' => 'kakak',
                'nomor_telepon' => '12134',
                'alamat' => 'abcd',
                'created_at' => '2023-05-24 23:45:22',
                'updated_at' => '2023-05-24 23:45:22',
            ),
            3 => 
            array (
                'id' => 8,
                'employee_id' => 14,
                'nama' => 'SODIK',
                'hubungan' => 'AYAH',
                'nomor_telepon' => '081335924508',
                'alamat' => 'KEDIRI',
                'created_at' => '2023-05-25 22:55:45',
                'updated_at' => '2023-05-25 22:55:45',
            ),
            4 => 
            array (
                'id' => 10,
                'employee_id' => 20,
                'nama' => 'Agus',
                'hubungan' => 'Suami',
                'nomor_telepon' => '08123456789',
                'alamat' => 'Jl. Margorejo Masjid 4-F RT.002 RW.004 Surabaya',
                'created_at' => '2023-08-09 17:21:37',
                'updated_at' => '2023-08-09 17:21:37',
            ),
            5 => 
            array (
                'id' => 11,
                'employee_id' => 21,
                'nama' => 'Ingrik',
                'hubungan' => 'istri',
                'nomor_telepon' => '0812345678',
                'alamat' => 'Samarinda',
                'created_at' => '2023-08-15 19:43:13',
                'updated_at' => '2023-08-15 19:43:13',
            ),
            6 => 
            array (
                'id' => 12,
                'employee_id' => 22,
                'nama' => 'Ester',
                'hubungan' => 'istri',
                'nomor_telepon' => '0812345678',
                'alamat' => 'Tana Toraja',
                'created_at' => '2023-08-15 20:05:53',
                'updated_at' => '2023-08-15 20:05:53',
            ),
            7 => 
            array (
                'id' => 15,
                'employee_id' => 137,
                'nama' => 'Irvan Christiawan Wijaya',
                'hubungan' => 'Teman',
                'nomor_telepon' => '089539714028',
                'alamat' => 'Perum. Puri Nirwana Jedong B8 Kec. Wagir, Kab. Malang, Jawa Timur',
                'created_at' => '2023-09-25 21:53:22',
                'updated_at' => '2023-09-25 21:53:22',
            ),
            8 => 
            array (
                'id' => 16,
                'employee_id' => 5,
                'nama' => 'SANCUK HANAFI',
                'hubungan' => 'SUAMI',
                'nomor_telepon' => '085758888898',
                'alamat' => 'Gunung Anyar Tambak Kav. 4 Gang Durian No.29 Surabaya',
                'created_at' => '2023-12-22 18:01:27',
                'updated_at' => '2023-12-22 18:01:27',
            ),
            9 => 
            array (
                'id' => 17,
                'employee_id' => 8,
                'nama' => 'Lindawati',
                'hubungan' => 'orang tua',
                'nomor_telepon' => '081339960005',
                'alamat' => 'ploso timur 7/35, surabaya',
                'created_at' => '2023-12-22 21:53:18',
                'updated_at' => '2023-12-22 21:53:18',
            ),
            10 => 
            array (
                'id' => 18,
                'employee_id' => 28,
                'nama' => 'Emy Astui',
                'hubungan' => 'Istri',
                'nomor_telepon' => '+62 813-4520-3032',
                'alamat' => 'Samarinda',
                'created_at' => '2024-01-05 17:57:03',
                'updated_at' => '2024-01-05 17:57:03',
            ),
            11 => 
            array (
                'id' => 19,
                'employee_id' => 15,
                'nama' => 'Nining Puji Astuti',
                'hubungan' => 'istri',
                'nomor_telepon' => '081234567890',
                'alamat' => 'Tulungagung',
                'created_at' => '2024-01-05 18:25:39',
                'updated_at' => '2024-01-05 18:25:39',
            ),
            12 => 
            array (
                'id' => 22,
                'employee_id' => 140,
                'nama' => 'Dewi Areani',
                'hubungan' => 'Istri',
                'nomor_telepon' => '085232133324',
                'alamat' => 'Teuku Umar',
                'created_at' => '2024-01-10 00:02:07',
                'updated_at' => '2024-01-10 00:02:07',
            ),
            13 => 
            array (
                'id' => 23,
                'employee_id' => 190,
                'nama' => 'HJ.ROHANI',
                'hubungan' => 'Ibu',
                'nomor_telepon' => '082195559854',
                'alamat' => 'Jl.A.Potto Kec.Lilirilau.Kab.Soppeng',
                'created_at' => '2024-01-10 00:20:42',
                'updated_at' => '2024-01-10 00:20:42',
            ),
            14 => 
            array (
                'id' => 24,
                'employee_id' => 58,
                'nama' => 'Lie Vanny Leono',
                'hubungan' => 'Saudara',
                'nomor_telepon' => '085350849968',
                'alamat' => 'Jl Belitung Darat No 1B RT 026 RW 02',
                'created_at' => '2024-01-10 22:42:47',
                'updated_at' => '2024-01-10 22:42:47',
            ),
            15 => 
            array (
                'id' => 25,
                'employee_id' => 59,
                'nama' => 'Fajar Viki Veni',
                'hubungan' => 'Saudara kandung',
                'nomor_telepon' => '087716328075',
                'alamat' => 'Jl.griya tama asri 2, Kec. Murung Pudak, Kab.Tabalong, KAL-SEL',
                'created_at' => '2024-01-23 17:40:20',
                'updated_at' => '2024-01-23 17:40:20',
            ),
            16 => 
            array (
                'id' => 26,
                'employee_id' => 49,
                'nama' => 'Aryanti Widyastuti',
                'hubungan' => 'Kakak KAndung',
                'nomor_telepon' => '082140336067',
                'alamat' => 'Makarya Binangun blok H-20 Waru',
                'created_at' => '2024-01-26 13:39:05',
                'updated_at' => '2024-01-26 13:39:05',
            ),
        ));
        
        
    }
}