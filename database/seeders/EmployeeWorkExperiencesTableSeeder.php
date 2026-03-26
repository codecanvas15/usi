<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeWorkExperiencesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('employee_work_experiences')->delete();
        
        \DB::table('employee_work_experiences')->insert(array (
            0 => 
            array (
                'id' => 1,
                'employee_id' => 5,
                'from' => '2011-10-31',
                'to' => '2000-01-03',
                'name' => 'NEWERA GROUP',
                'phone' => '031-3987081',
                'employee_count' => 100,
                'type' => 'PABRIK',
                'position' => 'KEUANGAN',
                'beginning_position' => 'OPERATOR UMUM',
                'end_position' => 'KEUANGAN',
                'supervisor' => 'BU ELSYE',
                'reason_for_leaving' => 'MUTASI KERJA',
                'created_at' => '2023-12-22 18:29:14',
                'updated_at' => '2023-12-22 18:29:14',
            ),
            1 => 
            array (
                'id' => 2,
                'employee_id' => 28,
                'from' => '2022-03-23',
                'to' => '2016-03-01',
                'name' => 'Cindara pratama line',
                'phone' => '0542740462',
                'employee_count' => 1450,
                'type' => 'Pelayaran',
                'position' => 'Masinis 3',
                'beginning_position' => 'Masinis 3',
                'end_position' => 'Masinis 3',
                'supervisor' => 'Chief officer',
                'reason_for_leaving' => 'Bergabung dengan PT USI',
                'created_at' => '2024-01-05 17:55:58',
                'updated_at' => '2024-01-05 17:55:58',
            ),
            2 => 
            array (
                'id' => 6,
                'employee_id' => 191,
                'from' => '2023-06-09',
                'to' => '2021-08-02',
                'name' => 'Pt.Marindo pasific',
                'phone' => '085342595363',
                'employee_count' => 10,
                'type' => 'Pelaut',
                'position' => 'Nahkoda',
                'beginning_position' => 'Mualim 2',
                'end_position' => 'Nahkoda',
                'supervisor' => 'Atasan',
                'reason_for_leaving' => 'Cari pengalaman yg lain',
                'created_at' => '2024-01-09 18:52:17',
                'updated_at' => '2024-01-09 18:52:17',
            ),
            3 => 
            array (
                'id' => 9,
                'employee_id' => 140,
                'from' => '2022-10-20',
                'to' => '2020-06-02',
                'name' => 'MKP',
                'phone' => '08995087800',
                'employee_count' => 0,
                'type' => 'Pelayaran',
                'position' => 'Masinis 3',
                'beginning_position' => 'Masinis 3',
                'end_position' => 'Masinis 3',
                'supervisor' => 'Karena sesuai kemampuan',
                'reason_for_leaving' => 'Menikah',
                'created_at' => '2024-01-10 00:02:00',
                'updated_at' => '2024-01-10 00:02:00',
            ),
            4 => 
            array (
                'id' => 10,
                'employee_id' => 116,
                'from' => '2023-07-03',
                'to' => '2023-02-23',
                'name' => 'Suasana Baru Line',
                'phone' => 'Tidak ada',
                'employee_count' => 0,
                'type' => 'Pelayaran',
                'position' => 'Mualim 1',
                'beginning_position' => 'Tidak ada',
                'end_position' => 'Tidak ada',
                'supervisor' => 'Tidak ada',
                'reason_for_leaving' => 'Finish kontrak',
                'created_at' => '2024-01-10 00:06:10',
                'updated_at' => '2024-01-10 00:06:10',
            ),
            5 => 
            array (
                'id' => 11,
                'employee_id' => 190,
                'from' => '2020-05-05',
                'to' => '2015-11-25',
                'name' => 'PT.Equtor Maritim',
                'phone' => '-',
                'employee_count' => 0,
                'type' => 'Kapal Tanker',
                'position' => 'Mualim 2',
                'beginning_position' => 'Mualim 3',
                'end_position' => 'Mualim 2',
                'supervisor' => '-',
                'reason_for_leaving' => 'Karena sudah selesai Kontrak',
                'created_at' => '2024-01-10 00:20:33',
                'updated_at' => '2024-01-10 00:20:33',
            ),
            6 => 
            array (
                'id' => 12,
                'employee_id' => 49,
                'from' => '2022-05-08',
                'to' => '2013-06-29',
                'name' => 'PT LABAN RAYA SAMODRA',
                'phone' => '031-3720360',
                'employee_count' => 600,
                'type' => 'Trading',
                'position' => 'Manager',
                'beginning_position' => 'Staff',
                'end_position' => 'Manager Purchasing',
                'supervisor' => 'Direktur Teknik',
                'reason_for_leaving' => 'Mengundurkan diri3',
                'created_at' => '2024-01-26 13:35:45',
                'updated_at' => '2024-01-26 13:35:45',
            ),
            7 => 
            array (
                'id' => 13,
                'employee_id' => 49,
                'from' => '2013-06-17',
                'to' => '1998-12-08',
                'name' => 'PT INDOPLAST MAKMUR',
                'phone' => '031-3987081',
                'employee_count' => 5000,
                'type' => 'Perusahaan Sandal',
                'position' => 'Senior Staff Purchasing',
                'beginning_position' => 'Staff',
                'end_position' => 'Senior Staff Purchasing',
                'supervisor' => 'Manager Purchasing',
                'reason_for_leaving' => 'Mengundurkan diri',
                'created_at' => '2024-01-26 13:35:45',
                'updated_at' => '2024-01-26 13:35:45',
            ),
        ));
        
        
    }
}