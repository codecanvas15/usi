<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 10,
                'name' => 'super_admin',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            1 => 
            array (
                'id' => 35,
                'name' => 'PEMBELIAN GENERAL - SUPERVISOR',
                'guard_name' => 'web',
                'created_at' => '2023-09-20 22:21:33',
                'updated_at' => '2024-02-26 17:22:46',
            ),
            2 => 
            array (
                'id' => 36,
                'name' => 'PENJUALAN',
                'guard_name' => 'web',
                'created_at' => '2023-09-20 22:22:03',
                'updated_at' => '2023-09-20 22:22:03',
            ),
            3 => 
            array (
                'id' => 37,
                'name' => 'ACCOUNTING - MANAGER',
                'guard_name' => 'web',
                'created_at' => '2023-09-20 22:25:46',
                'updated_at' => '2023-09-20 22:25:46',
            ),
            4 => 
            array (
                'id' => 38,
                'name' => 'ACCOUNTING - STAFF',
                'guard_name' => 'web',
                'created_at' => '2023-09-20 22:35:57',
                'updated_at' => '2023-09-20 22:35:57',
            ),
            5 => 
            array (
                'id' => 39,
                'name' => 'FINANCE - MANAGER',
                'guard_name' => 'web',
                'created_at' => '2023-09-20 23:36:53',
                'updated_at' => '2023-09-25 21:59:07',
            ),
            6 => 
            array (
                'id' => 40,
                'name' => 'FINANCE - SUPERVISOR',
                'guard_name' => 'web',
                'created_at' => '2023-09-20 23:57:48',
                'updated_at' => '2023-09-25 21:58:51',
            ),
            7 => 
            array (
                'id' => 41,
                'name' => 'FINANCE - STAFF',
                'guard_name' => 'web',
                'created_at' => '2023-09-21 00:16:27',
                'updated_at' => '2023-09-25 21:58:36',
            ),
            8 => 
            array (
                'id' => 42,
                'name' => 'PEMBELIAN GENERAL - STAFF',
                'guard_name' => 'web',
                'created_at' => '2023-09-21 00:42:00',
                'updated_at' => '2023-09-25 21:56:28',
            ),
            9 => 
            array (
                'id' => 43,
                'name' => 'TRADING - MANAGER',
                'guard_name' => 'web',
                'created_at' => '2023-09-21 22:25:14',
                'updated_at' => '2023-09-25 21:57:02',
            ),
            10 => 
            array (
                'id' => 44,
                'name' => 'TRADING - STAFF',
                'guard_name' => 'web',
                'created_at' => '2023-09-21 22:48:52',
                'updated_at' => '2023-09-25 21:56:45',
            ),
            11 => 
            array (
                'id' => 45,
                'name' => 'HRD & GA - MANAGER',
                'guard_name' => 'web',
                'created_at' => '2023-09-21 23:03:33',
                'updated_at' => '2023-09-25 21:57:35',
            ),
            12 => 
            array (
                'id' => 46,
                'name' => 'HRD & GA - STAFF',
                'guard_name' => 'web',
                'created_at' => '2023-09-21 23:15:20',
                'updated_at' => '2023-09-25 21:57:17',
            ),
            13 => 
            array (
                'id' => 47,
                'name' => 'Management',
                'guard_name' => 'web',
                'created_at' => '2023-09-27 21:49:04',
                'updated_at' => '2023-09-27 21:49:04',
            ),
            14 => 
            array (
                'id' => 48,
                'name' => 'SUPERVISOR TRADING',
                'guard_name' => 'web',
                'created_at' => '2023-10-19 01:09:43',
                'updated_at' => '2023-10-19 01:09:43',
            ),
        ));
        
        
    }
}