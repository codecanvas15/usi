<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeBanksTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('employee_banks')->delete();
        
        \DB::table('employee_banks')->insert(array (
            0 => 
            array (
                'id' => 4,
                'employee_id' => 7,
                'bank_name' => 'BCA',
                'behalf_of' => 'Indrawati',
                'account_number' => '777777777',
                'created_at' => '2023-03-23 18:11:13',
                'updated_at' => '2023-03-23 18:11:13',
            ),
            1 => 
            array (
                'id' => 6,
                'employee_id' => 9,
                'bank_name' => 'bca',
                'behalf_of' => 'Euis Mulyaning Asih',
                'account_number' => '123445549',
                'created_at' => '2023-03-23 21:20:30',
                'updated_at' => '2023-03-23 21:20:30',
            ),
            2 => 
            array (
                'id' => 7,
                'employee_id' => 13,
                'bank_name' => 'bca',
                'behalf_of' => 'yuan',
                'account_number' => '123456',
                'created_at' => '2023-05-24 23:45:22',
                'updated_at' => '2023-05-24 23:45:22',
            ),
            3 => 
            array (
                'id' => 8,
                'employee_id' => 14,
                'bank_name' => 'MANDIRI',
                'behalf_of' => 'Affan Maulana Akbar',
                'account_number' => '1400017154502',
                'created_at' => '2023-05-25 22:55:45',
                'updated_at' => '2023-05-25 22:55:45',
            ),
            4 => 
            array (
                'id' => 10,
                'employee_id' => 20,
                'bank_name' => 'BCA',
                'behalf_of' => 'Nurul Aini',
                'account_number' => '0890671284',
                'created_at' => '2023-08-09 17:21:37',
                'updated_at' => '2023-08-09 17:21:37',
            ),
            5 => 
            array (
                'id' => 11,
                'employee_id' => 21,
                'bank_name' => 'BRI',
                'behalf_of' => 'Herru Rusdianda',
                'account_number' => '1234567890',
                'created_at' => '2023-08-15 19:43:13',
                'updated_at' => '2023-08-15 19:43:13',
            ),
            6 => 
            array (
                'id' => 12,
                'employee_id' => 22,
                'bank_name' => 'BRI',
                'behalf_of' => 'Oskar Palaliung',
                'account_number' => '00960012345678',
                'created_at' => '2023-08-15 20:05:53',
                'updated_at' => '2023-08-15 20:05:53',
            ),
            7 => 
            array (
                'id' => 13,
                'employee_id' => 6,
                'bank_name' => 'BCA',
                'behalf_of' => 'Yulianti',
                'account_number' => '123456789',
                'created_at' => '2023-08-31 19:52:59',
                'updated_at' => '2023-08-31 19:52:59',
            ),
            8 => 
            array (
                'id' => 14,
                'employee_id' => 137,
                'bank_name' => 'BRI',
                'behalf_of' => 'Ester Nasanta Lubis',
                'account_number' => '016301110679507',
                'created_at' => '2023-09-25 21:53:04',
                'updated_at' => '2023-09-25 21:53:04',
            ),
            9 => 
            array (
                'id' => 15,
                'employee_id' => 137,
                'bank_name' => 'OCBC',
                'behalf_of' => 'Ester Nasanta Lubis',
                'account_number' => '050811067102',
                'created_at' => '2023-09-25 21:53:04',
                'updated_at' => '2023-09-25 21:53:04',
            ),
            10 => 
            array (
                'id' => 16,
                'employee_id' => 5,
                'bank_name' => 'BCA',
                'behalf_of' => 'EVVY LIANSYAH',
                'account_number' => '1500583799',
                'created_at' => '2023-12-22 18:06:43',
                'updated_at' => '2023-12-22 18:06:43',
            ),
            11 => 
            array (
                'id' => 17,
                'employee_id' => 8,
                'bank_name' => 'BCA',
                'behalf_of' => 'Cindy febriana sutanto',
                'account_number' => '0101857815',
                'created_at' => '2023-12-22 21:53:41',
                'updated_at' => '2023-12-22 21:53:41',
            ),
            12 => 
            array (
                'id' => 18,
                'employee_id' => 17,
                'bank_name' => 'BRI',
                'behalf_of' => 'Tus Fa\'li Ilambatar',
                'account_number' => '551501013494535',
                'created_at' => '2023-12-23 02:05:10',
                'updated_at' => '2023-12-23 02:05:10',
            ),
            13 => 
            array (
                'id' => 19,
                'employee_id' => 32,
                'bank_name' => 'Bank Permata',
                'behalf_of' => 'Marisi Pandapotan Sidabutar',
                'account_number' => '1237656438',
                'created_at' => '2024-01-03 00:28:20',
                'updated_at' => '2024-01-03 00:28:20',
            ),
            14 => 
            array (
                'id' => 20,
                'employee_id' => 32,
                'bank_name' => 'Bank BCA',
                'behalf_of' => 'Marisi Pandapotan Sidabutar',
                'account_number' => '3630015200',
                'created_at' => '2024-01-03 00:28:20',
                'updated_at' => '2024-01-03 00:28:20',
            ),
            15 => 
            array (
                'id' => 21,
                'employee_id' => 23,
                'bank_name' => 'BNI',
                'behalf_of' => 'MUHAMMAD LUTHFI OKTAFIAN ADAM',
                'account_number' => '338513601',
                'created_at' => '2024-01-05 18:21:28',
                'updated_at' => '2024-01-05 18:21:28',
            ),
            16 => 
            array (
                'id' => 22,
                'employee_id' => 15,
                'bank_name' => 'BRI',
                'behalf_of' => 'HENDRA WIDIANA',
                'account_number' => '00960012345678',
                'created_at' => '2024-01-05 18:25:55',
                'updated_at' => '2024-01-05 18:25:55',
            ),
            17 => 
            array (
                'id' => 25,
                'employee_id' => 95,
                'bank_name' => 'BRI',
                'behalf_of' => 'Zasmi umar baik',
                'account_number' => '018301101960501',
                'created_at' => '2024-01-09 05:00:05',
                'updated_at' => '2024-01-09 05:00:05',
            ),
            18 => 
            array (
                'id' => 27,
                'employee_id' => 191,
                'bank_name' => 'BRI',
                'behalf_of' => 'Risaldi',
                'account_number' => '506201014515531',
                'created_at' => '2024-01-09 18:52:30',
                'updated_at' => '2024-01-09 18:52:30',
            ),
            19 => 
            array (
                'id' => 28,
                'employee_id' => 179,
                'bank_name' => 'BRI',
                'behalf_of' => 'MUH KHALIK',
                'account_number' => '795901011219535',
                'created_at' => '2024-01-09 19:06:02',
                'updated_at' => '2024-01-09 19:06:02',
            ),
            20 => 
            array (
                'id' => 31,
                'employee_id' => 140,
                'bank_name' => 'BRI',
                'behalf_of' => 'Fajar Hariadi Pratama',
                'account_number' => '457301028071537',
                'created_at' => '2024-01-10 00:02:19',
                'updated_at' => '2024-01-10 00:02:19',
            ),
            21 => 
            array (
                'id' => 32,
                'employee_id' => 116,
                'bank_name' => 'BRI',
                'behalf_of' => 'M. Mauluddin Miftachur R.',
                'account_number' => '002601030532536',
                'created_at' => '2024-01-10 00:09:22',
                'updated_at' => '2024-01-10 00:09:22',
            ),
            22 => 
            array (
                'id' => 33,
                'employee_id' => 190,
                'bank_name' => 'BRI',
                'behalf_of' => 'Syapriansyah',
                'account_number' => '728701028833537',
                'created_at' => '2024-01-10 00:20:50',
                'updated_at' => '2024-01-10 00:20:50',
            ),
            23 => 
            array (
                'id' => 34,
                'employee_id' => 59,
                'bank_name' => 'BRI',
                'behalf_of' => 'Ariyani Dwi Kartika',
                'account_number' => '0096-01-144506-50-9',
                'created_at' => '2024-01-23 17:42:00',
                'updated_at' => '2024-01-23 17:42:00',
            ),
            24 => 
            array (
                'id' => 35,
                'employee_id' => 49,
                'bank_name' => 'Bank Central Asia',
                'behalf_of' => 'Aryani Widyawati',
                'account_number' => '2161095461',
                'created_at' => '2024-01-26 13:40:00',
                'updated_at' => '2024-01-26 13:40:00',
            ),
        ));
        
        
    }
}