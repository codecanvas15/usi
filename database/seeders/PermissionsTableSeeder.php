<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('permissions')->delete();
        
        \DB::table('permissions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'view project',
                'group' => 'project',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'create project',
                'group' => 'project',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'edit project',
                'group' => 'project',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'delete project',
                'group' => 'project',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'close project',
                'group' => 'project',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'approve project',
                'group' => 'project',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'reject project',
                'group' => 'project',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'activate project',
                'group' => 'project',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'deactivate project',
                'group' => 'project',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'cancel project',
                'group' => 'project',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'view role',
                'group' => 'role',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'create role',
                'group' => 'role',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'edit role',
                'group' => 'role',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'delete role',
                'group' => 'role',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'view user',
                'group' => 'user',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'create user',
                'group' => 'user',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'edit user',
                'group' => 'user',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'delete user',
                'group' => 'user',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'export user',
                'group' => 'user',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'import user',
                'group' => 'user',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'view garage',
                'group' => 'garage',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'create garage',
                'group' => 'garage',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'edit garage',
                'group' => 'garage',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            23 => 
            array (
                'id' => 24,
                'name' => 'delete garage',
                'group' => 'garage',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            24 => 
            array (
                'id' => 25,
                'name' => 'view fleet',
                'group' => 'fleet',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            25 => 
            array (
                'id' => 26,
                'name' => 'create fleet',
                'group' => 'fleet',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            26 => 
            array (
                'id' => 27,
                'name' => 'edit fleet',
                'group' => 'fleet',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            27 => 
            array (
                'id' => 28,
                'name' => 'delete fleet',
                'group' => 'fleet',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            28 => 
            array (
                'id' => 29,
                'name' => 'view branch',
                'group' => 'branch',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            29 => 
            array (
                'id' => 30,
                'name' => 'create branch',
                'group' => 'branch',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            30 => 
            array (
                'id' => 31,
                'name' => 'edit branch',
                'group' => 'branch',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            31 => 
            array (
                'id' => 32,
                'name' => 'delete branch',
                'group' => 'branch',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            32 => 
            array (
                'id' => 33,
                'name' => 'view period',
                'group' => 'period',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            33 => 
            array (
                'id' => 34,
                'name' => 'generate period',
                'group' => 'period',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            34 => 
            array (
                'id' => 35,
                'name' => 'view bank-internal',
                'group' => 'bank-internal',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            35 => 
            array (
                'id' => 36,
                'name' => 'create bank-internal',
                'group' => 'bank-internal',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            36 => 
            array (
                'id' => 37,
                'name' => 'edit bank-internal',
                'group' => 'bank-internal',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            37 => 
            array (
                'id' => 38,
                'name' => 'delete bank-internal',
                'group' => 'bank-internal',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            38 => 
            array (
                'id' => 39,
                'name' => 'view coa',
                'group' => 'coa',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            39 => 
            array (
                'id' => 40,
                'name' => 'create coa',
                'group' => 'coa',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            40 => 
            array (
                'id' => 41,
                'name' => 'edit coa',
                'group' => 'coa',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            41 => 
            array (
                'id' => 42,
                'name' => 'delete coa',
                'group' => 'coa',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            42 => 
            array (
                'id' => 43,
                'name' => 'view division',
                'group' => 'division',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            43 => 
            array (
                'id' => 44,
                'name' => 'create division',
                'group' => 'division',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            44 => 
            array (
                'id' => 45,
                'name' => 'edit division',
                'group' => 'division',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            45 => 
            array (
                'id' => 46,
                'name' => 'delete division',
                'group' => 'division',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            46 => 
            array (
                'id' => 47,
                'name' => 'view item-receiving-report',
                'group' => 'item-receiving-report',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            47 => 
            array (
                'id' => 48,
                'name' => 'create item-receiving-report',
                'group' => 'item-receiving-report',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            48 => 
            array (
                'id' => 49,
                'name' => 'edit item-receiving-report',
                'group' => 'item-receiving-report',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            49 => 
            array (
                'id' => 50,
                'name' => 'delete item-receiving-report',
                'group' => 'item-receiving-report',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            50 => 
            array (
                'id' => 51,
                'name' => 'approve item-receiving-report',
                'group' => 'item-receiving-report',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            51 => 
            array (
                'id' => 52,
                'name' => 'revert item-receiving-report',
                'group' => 'item-receiving-report',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            52 => 
            array (
                'id' => 53,
                'name' => 'void item-receiving-report',
                'group' => 'item-receiving-report',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            53 => 
            array (
                'id' => 54,
                'name' => 'cancel item-receiving-report',
                'group' => 'item-receiving-report',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            54 => 
            array (
                'id' => 55,
                'name' => 'reject item-receiving-report',
                'group' => 'item-receiving-report',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            55 => 
            array (
                'id' => 56,
                'name' => 'close item-receiving-report',
                'group' => 'item-receiving-report',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:29',
                'updated_at' => '2022-12-16 08:35:29',
            ),
            56 => 
            array (
                'id' => 57,
                'name' => 'add-coa item-receiving-report',
                'group' => 'item-receiving-report',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            57 => 
            array (
                'id' => 58,
                'name' => 'view item-receiving-report-general',
                'group' => 'item-receiving-report-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            58 => 
            array (
                'id' => 59,
                'name' => 'create item-receiving-report-general',
                'group' => 'item-receiving-report-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            59 => 
            array (
                'id' => 60,
                'name' => 'edit item-receiving-report-general',
                'group' => 'item-receiving-report-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            60 => 
            array (
                'id' => 61,
                'name' => 'delete item-receiving-report-general',
                'group' => 'item-receiving-report-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            61 => 
            array (
                'id' => 62,
                'name' => 'approve item-receiving-report-general',
                'group' => 'item-receiving-report-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            62 => 
            array (
                'id' => 63,
                'name' => 'revert item-receiving-report-general',
                'group' => 'item-receiving-report-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            63 => 
            array (
                'id' => 64,
                'name' => 'void item-receiving-report-general',
                'group' => 'item-receiving-report-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            64 => 
            array (
                'id' => 65,
                'name' => 'cancel item-receiving-report-general',
                'group' => 'item-receiving-report-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            65 => 
            array (
                'id' => 66,
                'name' => 'reject item-receiving-report-general',
                'group' => 'item-receiving-report-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            66 => 
            array (
                'id' => 67,
                'name' => 'close item-receiving-report-general',
                'group' => 'item-receiving-report-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            67 => 
            array (
                'id' => 68,
                'name' => 'add-coa item-receiving-report-general',
                'group' => 'item-receiving-report-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            68 => 
            array (
                'id' => 69,
                'name' => 'view item-receiving-report-service',
                'group' => 'item-receiving-report-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            69 => 
            array (
                'id' => 70,
                'name' => 'create item-receiving-report-service',
                'group' => 'item-receiving-report-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            70 => 
            array (
                'id' => 71,
                'name' => 'edit item-receiving-report-service',
                'group' => 'item-receiving-report-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            71 => 
            array (
                'id' => 72,
                'name' => 'delete item-receiving-report-service',
                'group' => 'item-receiving-report-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            72 => 
            array (
                'id' => 73,
                'name' => 'approve item-receiving-report-service',
                'group' => 'item-receiving-report-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            73 => 
            array (
                'id' => 74,
                'name' => 'revert item-receiving-report-service',
                'group' => 'item-receiving-report-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            74 => 
            array (
                'id' => 75,
                'name' => 'void item-receiving-report-service',
                'group' => 'item-receiving-report-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            75 => 
            array (
                'id' => 76,
                'name' => 'cancel item-receiving-report-service',
                'group' => 'item-receiving-report-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            76 => 
            array (
                'id' => 77,
                'name' => 'reject item-receiving-report-service',
                'group' => 'item-receiving-report-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            77 => 
            array (
                'id' => 78,
                'name' => 'close item-receiving-report-service',
                'group' => 'item-receiving-report-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            78 => 
            array (
                'id' => 79,
                'name' => 'add-coa item-receiving-report-service',
                'group' => 'item-receiving-report-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            79 => 
            array (
                'id' => 80,
                'name' => 'view item-receiving-report-trading',
                'group' => 'item-receiving-report-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            80 => 
            array (
                'id' => 81,
                'name' => 'create item-receiving-report-trading',
                'group' => 'item-receiving-report-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            81 => 
            array (
                'id' => 82,
                'name' => 'edit item-receiving-report-trading',
                'group' => 'item-receiving-report-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            82 => 
            array (
                'id' => 83,
                'name' => 'delete item-receiving-report-trading',
                'group' => 'item-receiving-report-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            83 => 
            array (
                'id' => 84,
                'name' => 'approve item-receiving-report-trading',
                'group' => 'item-receiving-report-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            84 => 
            array (
                'id' => 85,
                'name' => 'revert item-receiving-report-trading',
                'group' => 'item-receiving-report-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            85 => 
            array (
                'id' => 86,
                'name' => 'void item-receiving-report-trading',
                'group' => 'item-receiving-report-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            86 => 
            array (
                'id' => 87,
                'name' => 'cancel item-receiving-report-trading',
                'group' => 'item-receiving-report-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:30',
                'updated_at' => '2022-12-16 08:35:30',
            ),
            87 => 
            array (
                'id' => 88,
                'name' => 'reject item-receiving-report-trading',
                'group' => 'item-receiving-report-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            88 => 
            array (
                'id' => 89,
                'name' => 'close item-receiving-report-trading',
                'group' => 'item-receiving-report-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            89 => 
            array (
                'id' => 90,
                'name' => 'add-coa item-receiving-report-trading',
                'group' => 'item-receiving-report-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            90 => 
            array (
                'id' => 91,
                'name' => 'view item-receiving-report-transport',
                'group' => 'item-receiving-report-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            91 => 
            array (
                'id' => 92,
                'name' => 'create item-receiving-report-transport',
                'group' => 'item-receiving-report-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            92 => 
            array (
                'id' => 93,
                'name' => 'edit item-receiving-report-transport',
                'group' => 'item-receiving-report-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            93 => 
            array (
                'id' => 94,
                'name' => 'delete item-receiving-report-transport',
                'group' => 'item-receiving-report-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            94 => 
            array (
                'id' => 95,
                'name' => 'approve item-receiving-report-transport',
                'group' => 'item-receiving-report-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            95 => 
            array (
                'id' => 96,
                'name' => 'revert item-receiving-report-transport',
                'group' => 'item-receiving-report-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            96 => 
            array (
                'id' => 97,
                'name' => 'void item-receiving-report-transport',
                'group' => 'item-receiving-report-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            97 => 
            array (
                'id' => 98,
                'name' => 'cancel item-receiving-report-transport',
                'group' => 'item-receiving-report-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            98 => 
            array (
                'id' => 99,
                'name' => 'reject item-receiving-report-transport',
                'group' => 'item-receiving-report-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            99 => 
            array (
                'id' => 100,
                'name' => 'close item-receiving-report-transport',
                'group' => 'item-receiving-report-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            100 => 
            array (
                'id' => 101,
                'name' => 'add-coa item-receiving-report-transport',
                'group' => 'item-receiving-report-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            101 => 
            array (
                'id' => 102,
                'name' => 'view ware-house',
                'group' => 'ware-house',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            102 => 
            array (
                'id' => 103,
                'name' => 'create ware-house',
                'group' => 'ware-house',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            103 => 
            array (
                'id' => 104,
                'name' => 'edit ware-house',
                'group' => 'ware-house',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            104 => 
            array (
                'id' => 105,
                'name' => 'delete ware-house',
                'group' => 'ware-house',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            105 => 
            array (
                'id' => 106,
                'name' => 'view stock-card',
                'group' => 'stock-card',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            106 => 
            array (
                'id' => 107,
                'name' => 'export stock-card',
                'group' => 'stock-card',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            107 => 
            array (
                'id' => 108,
                'name' => 'print stock-card',
                'group' => 'stock-card',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            108 => 
            array (
                'id' => 109,
                'name' => 'view stock-card-value',
                'group' => 'stock-card-value',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            109 => 
            array (
                'id' => 110,
                'name' => 'export stock-card-value',
                'group' => 'stock-card-value',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            110 => 
            array (
                'id' => 111,
                'name' => 'print stock-card-value',
                'group' => 'stock-card-value',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            111 => 
            array (
                'id' => 112,
                'name' => 'view stock-card-general',
                'group' => 'stock-card-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            112 => 
            array (
                'id' => 113,
                'name' => 'export stock-card-general',
                'group' => 'stock-card-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            113 => 
            array (
                'id' => 114,
                'name' => 'print stock-card-general',
                'group' => 'stock-card-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            114 => 
            array (
                'id' => 115,
                'name' => 'view stock-card-trading',
                'group' => 'stock-card-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:31',
                'updated_at' => '2022-12-16 08:35:31',
            ),
            115 => 
            array (
                'id' => 116,
                'name' => 'export stock-card-trading',
                'group' => 'stock-card-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            116 => 
            array (
                'id' => 117,
                'name' => 'print stock-card-trading',
                'group' => 'stock-card-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            117 => 
            array (
                'id' => 118,
                'name' => 'view stock-mutation',
                'group' => 'stock-mutation',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            118 => 
            array (
                'id' => 119,
                'name' => 'export stock-mutation',
                'group' => 'stock-mutation',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            119 => 
            array (
                'id' => 120,
                'name' => 'print stock-mutation',
                'group' => 'stock-mutation',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            120 => 
            array (
                'id' => 121,
                'name' => 'view stock-usage',
                'group' => 'stock-usage',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            121 => 
            array (
                'id' => 122,
                'name' => 'export stock-usage',
                'group' => 'stock-usage',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            122 => 
            array (
                'id' => 123,
                'name' => 'print stock-usage',
                'group' => 'stock-usage',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            123 => 
            array (
                'id' => 124,
                'name' => 'create stock-usage',
                'group' => 'stock-usage',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            124 => 
            array (
                'id' => 125,
                'name' => 'approve stock-usage',
                'group' => 'stock-usage',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            125 => 
            array (
                'id' => 126,
                'name' => 'reject stock-usage',
                'group' => 'stock-usage',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            126 => 
            array (
                'id' => 127,
                'name' => 'view stock-transfer',
                'group' => 'stock-transfer',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            127 => 
            array (
                'id' => 128,
                'name' => 'create stock-transfer',
                'group' => 'stock-transfer',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            128 => 
            array (
                'id' => 129,
                'name' => 'approve stock-transfer',
                'group' => 'stock-transfer',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            129 => 
            array (
                'id' => 130,
                'name' => 'reject stock-transfer',
                'group' => 'stock-transfer',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            130 => 
            array (
                'id' => 131,
                'name' => 'view stock-transfer-receiving',
                'group' => 'stock-transfer-receiving',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            131 => 
            array (
                'id' => 132,
                'name' => 'create stock-transfer-receiving',
                'group' => 'stock-transfer-receiving',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            132 => 
            array (
                'id' => 133,
                'name' => 'edit stock-transfer-receiving',
                'group' => 'stock-transfer-receiving',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            133 => 
            array (
                'id' => 134,
                'name' => 'approve stock-transfer-receiving',
                'group' => 'stock-transfer-receiving',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            134 => 
            array (
                'id' => 135,
                'name' => 'reject stock-transfer-receiving',
                'group' => 'stock-transfer-receiving',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            135 => 
            array (
                'id' => 136,
                'name' => 'view stock-adjustment',
                'group' => 'stock-adjustment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            136 => 
            array (
                'id' => 137,
                'name' => 'create stock-adjustment',
                'group' => 'stock-adjustment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            137 => 
            array (
                'id' => 138,
                'name' => 'approve stock-adjustment',
                'group' => 'stock-adjustment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            138 => 
            array (
                'id' => 139,
                'name' => 'reject stock-adjustment',
                'group' => 'stock-adjustment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            139 => 
            array (
                'id' => 140,
                'name' => 'view employee',
                'group' => 'employee',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            140 => 
            array (
                'id' => 141,
                'name' => 'create employee',
                'group' => 'employee',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            141 => 
            array (
                'id' => 142,
                'name' => 'edit employee',
                'group' => 'employee',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            142 => 
            array (
                'id' => 143,
                'name' => 'delete employee',
                'group' => 'employee',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            143 => 
            array (
                'id' => 144,
                'name' => 'export employee',
                'group' => 'employee',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            144 => 
            array (
                'id' => 145,
                'name' => 'import employee',
                'group' => 'employee',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            145 => 
            array (
                'id' => 146,
                'name' => 'view employment-status',
                'group' => 'employment-status',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            146 => 
            array (
                'id' => 147,
                'name' => 'create employment-status',
                'group' => 'employment-status',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            147 => 
            array (
                'id' => 148,
                'name' => 'edit employment-status',
                'group' => 'employment-status',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            148 => 
            array (
                'id' => 149,
                'name' => 'delete employment-status',
                'group' => 'employment-status',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            149 => 
            array (
                'id' => 150,
                'name' => 'view labor-demand',
                'group' => 'labor-demand',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            150 => 
            array (
                'id' => 151,
                'name' => 'create labor-demand',
                'group' => 'labor-demand',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            151 => 
            array (
                'id' => 152,
                'name' => 'edit labor-demand',
                'group' => 'labor-demand',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            152 => 
            array (
                'id' => 153,
                'name' => 'delete labor-demand',
                'group' => 'labor-demand',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            153 => 
            array (
                'id' => 154,
                'name' => 'view position',
                'group' => 'position',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            154 => 
            array (
                'id' => 155,
                'name' => 'create position',
                'group' => 'position',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            155 => 
            array (
                'id' => 156,
                'name' => 'edit position',
                'group' => 'position',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            156 => 
            array (
                'id' => 157,
                'name' => 'delete position',
                'group' => 'position',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            157 => 
            array (
                'id' => 158,
                'name' => 'view payroll-period',
                'group' => 'payroll-period',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            158 => 
            array (
                'id' => 159,
                'name' => 'create payroll-period',
                'group' => 'payroll-period',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            159 => 
            array (
                'id' => 160,
                'name' => 'edit payroll-period',
                'group' => 'payroll-period',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            160 => 
            array (
                'id' => 161,
                'name' => 'delete payroll-period',
                'group' => 'payroll-period',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            161 => 
            array (
                'id' => 162,
                'name' => 'view payroll',
                'group' => 'payroll',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            162 => 
            array (
                'id' => 163,
                'name' => 'create payroll',
                'group' => 'payroll',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:33',
                'updated_at' => '2022-12-16 08:35:33',
            ),
            163 => 
            array (
                'id' => 164,
                'name' => 'edit payroll',
                'group' => 'payroll',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:34',
                'updated_at' => '2022-12-16 08:35:34',
            ),
            164 => 
            array (
                'id' => 165,
                'name' => 'delete payroll',
                'group' => 'payroll',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:34',
                'updated_at' => '2022-12-16 08:35:34',
            ),
            165 => 
            array (
                'id' => 170,
                'name' => 'view evaluation',
                'group' => 'evaluation',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:34',
                'updated_at' => '2022-12-16 08:35:34',
            ),
            166 => 
            array (
                'id' => 171,
                'name' => 'create evaluation',
                'group' => 'evaluation',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:34',
                'updated_at' => '2022-12-16 08:35:34',
            ),
            167 => 
            array (
                'id' => 172,
                'name' => 'edit evaluation',
                'group' => 'evaluation',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:34',
                'updated_at' => '2022-12-16 08:35:34',
            ),
            168 => 
            array (
                'id' => 173,
                'name' => 'delete evaluation',
                'group' => 'evaluation',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:34',
                'updated_at' => '2022-12-16 08:35:34',
            ),
            169 => 
            array (
                'id' => 174,
                'name' => 'view leave',
                'group' => 'leave',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:34',
                'updated_at' => '2022-12-16 08:35:34',
            ),
            170 => 
            array (
                'id' => 175,
                'name' => 'create leave',
                'group' => 'leave',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:34',
                'updated_at' => '2022-12-16 08:35:34',
            ),
            171 => 
            array (
                'id' => 176,
                'name' => 'edit leave',
                'group' => 'leave',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:34',
                'updated_at' => '2022-12-16 08:35:34',
            ),
            172 => 
            array (
                'id' => 177,
                'name' => 'delete leave',
                'group' => 'leave',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:34',
                'updated_at' => '2022-12-16 08:35:34',
            ),
            173 => 
            array (
                'id' => 178,
                'name' => 'view presensi',
                'group' => 'presensi',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:34',
                'updated_at' => '2022-12-16 08:35:34',
            ),
            174 => 
            array (
                'id' => 179,
                'name' => 'create presensi',
                'group' => 'presensi',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:34',
                'updated_at' => '2022-12-16 08:35:34',
            ),
            175 => 
            array (
                'id' => 180,
                'name' => 'edit presensi',
                'group' => 'presensi',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:34',
                'updated_at' => '2022-12-16 08:35:34',
            ),
            176 => 
            array (
                'id' => 181,
                'name' => 'delete presensi',
                'group' => 'presensi',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:34',
                'updated_at' => '2022-12-16 08:35:34',
            ),
            177 => 
            array (
                'id' => 182,
                'name' => 'view permission-letter-employee',
                'group' => 'permission-letter-employee',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:34',
                'updated_at' => '2022-12-16 08:35:34',
            ),
            178 => 
            array (
                'id' => 183,
                'name' => 'create permission-letter-employee',
                'group' => 'permission-letter-employee',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:34',
                'updated_at' => '2022-12-16 08:35:34',
            ),
            179 => 
            array (
                'id' => 184,
                'name' => 'edit permission-letter-employee',
                'group' => 'permission-letter-employee',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            180 => 
            array (
                'id' => 185,
                'name' => 'delete permission-letter-employee',
                'group' => 'permission-letter-employee',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            181 => 
            array (
                'id' => 186,
                'name' => 'view purchase-request',
                'group' => 'purchase-request',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            182 => 
            array (
                'id' => 187,
                'name' => 'view-all purchase-request',
                'group' => 'purchase-request',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            183 => 
            array (
                'id' => 188,
                'name' => 'create purchase-request',
                'group' => 'purchase-request',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            184 => 
            array (
                'id' => 189,
                'name' => 'edit purchase-request',
                'group' => 'purchase-request',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            185 => 
            array (
                'id' => 190,
                'name' => 'delete purchase-request',
                'group' => 'purchase-request',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            186 => 
            array (
                'id' => 191,
                'name' => 'approve purchase-request',
                'group' => 'purchase-request',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            187 => 
            array (
                'id' => 192,
                'name' => 'revert purchase-request',
                'group' => 'purchase-request',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            188 => 
            array (
                'id' => 193,
                'name' => 'void purchase-request',
                'group' => 'purchase-request',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            189 => 
            array (
                'id' => 194,
                'name' => 'cancel purchase-request',
                'group' => 'purchase-request',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            190 => 
            array (
                'id' => 195,
                'name' => 'reject purchase-request',
                'group' => 'purchase-request',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            191 => 
            array (
                'id' => 196,
                'name' => 'close purchase-request',
                'group' => 'purchase-request',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            192 => 
            array (
                'id' => 197,
                'name' => 'view purchase-request-service',
                'group' => 'purchase-request-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            193 => 
            array (
                'id' => 198,
                'name' => 'view-all purchase-request-service',
                'group' => 'purchase-request-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            194 => 
            array (
                'id' => 199,
                'name' => 'create purchase-request-service',
                'group' => 'purchase-request-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            195 => 
            array (
                'id' => 200,
                'name' => 'edit purchase-request-service',
                'group' => 'purchase-request-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            196 => 
            array (
                'id' => 201,
                'name' => 'delete purchase-request-service',
                'group' => 'purchase-request-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            197 => 
            array (
                'id' => 202,
                'name' => 'approve purchase-request-service',
                'group' => 'purchase-request-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            198 => 
            array (
                'id' => 203,
                'name' => 'revert purchase-request-service',
                'group' => 'purchase-request-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            199 => 
            array (
                'id' => 204,
                'name' => 'void purchase-request-service',
                'group' => 'purchase-request-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:36',
                'updated_at' => '2022-12-16 08:35:36',
            ),
            200 => 
            array (
                'id' => 205,
                'name' => 'cancel purchase-request-service',
                'group' => 'purchase-request-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:36',
                'updated_at' => '2022-12-16 08:35:36',
            ),
            201 => 
            array (
                'id' => 206,
                'name' => 'reject purchase-request-service',
                'group' => 'purchase-request-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:36',
                'updated_at' => '2022-12-16 08:35:36',
            ),
            202 => 
            array (
                'id' => 207,
                'name' => 'close purchase-request-service',
                'group' => 'purchase-request-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:36',
                'updated_at' => '2022-12-16 08:35:36',
            ),
            203 => 
            array (
                'id' => 208,
                'name' => 'view purchase-request-general',
                'group' => 'purchase-request-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:36',
                'updated_at' => '2022-12-16 08:35:36',
            ),
            204 => 
            array (
                'id' => 209,
                'name' => 'view-all purchase-request-general',
                'group' => 'purchase-request-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:36',
                'updated_at' => '2022-12-16 08:35:36',
            ),
            205 => 
            array (
                'id' => 210,
                'name' => 'create purchase-request-general',
                'group' => 'purchase-request-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:36',
                'updated_at' => '2022-12-16 08:35:36',
            ),
            206 => 
            array (
                'id' => 211,
                'name' => 'edit purchase-request-general',
                'group' => 'purchase-request-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:36',
                'updated_at' => '2022-12-16 08:35:36',
            ),
            207 => 
            array (
                'id' => 212,
                'name' => 'delete purchase-request-general',
                'group' => 'purchase-request-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:36',
                'updated_at' => '2022-12-16 08:35:36',
            ),
            208 => 
            array (
                'id' => 213,
                'name' => 'approve purchase-request-general',
                'group' => 'purchase-request-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:36',
                'updated_at' => '2022-12-16 08:35:36',
            ),
            209 => 
            array (
                'id' => 214,
                'name' => 'revert purchase-request-general',
                'group' => 'purchase-request-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:36',
                'updated_at' => '2022-12-16 08:35:36',
            ),
            210 => 
            array (
                'id' => 215,
                'name' => 'void purchase-request-general',
                'group' => 'purchase-request-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:36',
                'updated_at' => '2022-12-16 08:35:36',
            ),
            211 => 
            array (
                'id' => 216,
                'name' => 'cancel purchase-request-general',
                'group' => 'purchase-request-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:36',
                'updated_at' => '2022-12-16 08:35:36',
            ),
            212 => 
            array (
                'id' => 217,
                'name' => 'reject purchase-request-general',
                'group' => 'purchase-request-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:36',
                'updated_at' => '2022-12-16 08:35:36',
            ),
            213 => 
            array (
                'id' => 218,
                'name' => 'close purchase-request-general',
                'group' => 'purchase-request-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:36',
                'updated_at' => '2022-12-16 08:35:36',
            ),
            214 => 
            array (
                'id' => 219,
                'name' => 'view purchase-request-transport',
                'group' => 'purchase-request-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:36',
                'updated_at' => '2022-12-16 08:35:36',
            ),
            215 => 
            array (
                'id' => 220,
                'name' => 'view-all purchase-request-transport',
                'group' => 'purchase-request-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:36',
                'updated_at' => '2022-12-16 08:35:36',
            ),
            216 => 
            array (
                'id' => 221,
                'name' => 'create purchase-request-transport',
                'group' => 'purchase-request-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:36',
                'updated_at' => '2022-12-16 08:35:36',
            ),
            217 => 
            array (
                'id' => 222,
                'name' => 'edit purchase-request-transport',
                'group' => 'purchase-request-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:36',
                'updated_at' => '2022-12-16 08:35:36',
            ),
            218 => 
            array (
                'id' => 223,
                'name' => 'delete purchase-request-transport',
                'group' => 'purchase-request-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:37',
                'updated_at' => '2022-12-16 08:35:37',
            ),
            219 => 
            array (
                'id' => 224,
                'name' => 'approve purchase-request-transport',
                'group' => 'purchase-request-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:37',
                'updated_at' => '2022-12-16 08:35:37',
            ),
            220 => 
            array (
                'id' => 225,
                'name' => 'revert purchase-request-transport',
                'group' => 'purchase-request-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:37',
                'updated_at' => '2022-12-16 08:35:37',
            ),
            221 => 
            array (
                'id' => 226,
                'name' => 'void purchase-request-transport',
                'group' => 'purchase-request-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:37',
                'updated_at' => '2022-12-16 08:35:37',
            ),
            222 => 
            array (
                'id' => 227,
                'name' => 'cancel purchase-request-transport',
                'group' => 'purchase-request-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:37',
                'updated_at' => '2022-12-16 08:35:37',
            ),
            223 => 
            array (
                'id' => 228,
                'name' => 'reject purchase-request-transport',
                'group' => 'purchase-request-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:37',
                'updated_at' => '2022-12-16 08:35:37',
            ),
            224 => 
            array (
                'id' => 229,
                'name' => 'close purchase-request-transport',
                'group' => 'purchase-request-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:37',
                'updated_at' => '2022-12-16 08:35:37',
            ),
            225 => 
            array (
                'id' => 230,
                'name' => 'view purchase-order',
                'group' => 'purchase-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:37',
                'updated_at' => '2022-12-16 08:35:37',
            ),
            226 => 
            array (
                'id' => 231,
                'name' => 'create purchase-order',
                'group' => 'purchase-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:37',
                'updated_at' => '2022-12-16 08:35:37',
            ),
            227 => 
            array (
                'id' => 232,
                'name' => 'edit purchase-order',
                'group' => 'purchase-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:37',
                'updated_at' => '2022-12-16 08:35:37',
            ),
            228 => 
            array (
                'id' => 233,
                'name' => 'delete purchase-order',
                'group' => 'purchase-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:37',
                'updated_at' => '2022-12-16 08:35:37',
            ),
            229 => 
            array (
                'id' => 234,
                'name' => 'approve purchase-order',
                'group' => 'purchase-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:37',
                'updated_at' => '2022-12-16 08:35:37',
            ),
            230 => 
            array (
                'id' => 235,
                'name' => 'revert purchase-order',
                'group' => 'purchase-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:37',
                'updated_at' => '2022-12-16 08:35:37',
            ),
            231 => 
            array (
                'id' => 236,
                'name' => 'void purchase-order',
                'group' => 'purchase-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:37',
                'updated_at' => '2022-12-16 08:35:37',
            ),
            232 => 
            array (
                'id' => 237,
                'name' => 'cancel purchase-order',
                'group' => 'purchase-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:37',
                'updated_at' => '2022-12-16 08:35:37',
            ),
            233 => 
            array (
                'id' => 238,
                'name' => 'reject purchase-order',
                'group' => 'purchase-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:37',
                'updated_at' => '2022-12-16 08:35:37',
            ),
            234 => 
            array (
                'id' => 239,
                'name' => 'close purchase-order',
                'group' => 'purchase-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:37',
                'updated_at' => '2022-12-16 08:35:37',
            ),
            235 => 
            array (
                'id' => 240,
                'name' => 'pairing purchase-order',
                'group' => 'purchase-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:37',
                'updated_at' => '2022-12-16 08:35:37',
            ),
            236 => 
            array (
                'id' => 241,
                'name' => 'view purchase-transport',
                'group' => 'purchase-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:38',
                'updated_at' => '2022-12-16 08:35:38',
            ),
            237 => 
            array (
                'id' => 242,
                'name' => 'create purchase-transport',
                'group' => 'purchase-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:38',
                'updated_at' => '2022-12-16 08:35:38',
            ),
            238 => 
            array (
                'id' => 243,
                'name' => 'edit purchase-transport',
                'group' => 'purchase-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:38',
                'updated_at' => '2022-12-16 08:35:38',
            ),
            239 => 
            array (
                'id' => 244,
                'name' => 'delete purchase-transport',
                'group' => 'purchase-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:38',
                'updated_at' => '2022-12-16 08:35:38',
            ),
            240 => 
            array (
                'id' => 245,
                'name' => 'approve purchase-transport',
                'group' => 'purchase-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:38',
                'updated_at' => '2022-12-16 08:35:38',
            ),
            241 => 
            array (
                'id' => 246,
                'name' => 'revert purchase-transport',
                'group' => 'purchase-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:38',
                'updated_at' => '2022-12-16 08:35:38',
            ),
            242 => 
            array (
                'id' => 247,
                'name' => 'void purchase-transport',
                'group' => 'purchase-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:38',
                'updated_at' => '2022-12-16 08:35:38',
            ),
            243 => 
            array (
                'id' => 248,
                'name' => 'cancel purchase-transport',
                'group' => 'purchase-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:38',
                'updated_at' => '2022-12-16 08:35:38',
            ),
            244 => 
            array (
                'id' => 249,
                'name' => 'reject purchase-transport',
                'group' => 'purchase-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:38',
                'updated_at' => '2022-12-16 08:35:38',
            ),
            245 => 
            array (
                'id' => 250,
                'name' => 'close purchase-transport',
                'group' => 'purchase-transport',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:38',
                'updated_at' => '2022-12-16 08:35:38',
            ),
            246 => 
            array (
                'id' => 251,
                'name' => 'view purchase-service',
                'group' => 'purchase-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:38',
                'updated_at' => '2022-12-16 08:35:38',
            ),
            247 => 
            array (
                'id' => 252,
                'name' => 'create purchase-service',
                'group' => 'purchase-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:38',
                'updated_at' => '2022-12-16 08:35:38',
            ),
            248 => 
            array (
                'id' => 253,
                'name' => 'edit purchase-service',
                'group' => 'purchase-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:38',
                'updated_at' => '2022-12-16 08:35:38',
            ),
            249 => 
            array (
                'id' => 254,
                'name' => 'delete purchase-service',
                'group' => 'purchase-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:38',
                'updated_at' => '2022-12-16 08:35:38',
            ),
            250 => 
            array (
                'id' => 255,
                'name' => 'approve purchase-service',
                'group' => 'purchase-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:38',
                'updated_at' => '2022-12-16 08:35:38',
            ),
            251 => 
            array (
                'id' => 256,
                'name' => 'revert purchase-service',
                'group' => 'purchase-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:38',
                'updated_at' => '2022-12-16 08:35:38',
            ),
            252 => 
            array (
                'id' => 257,
                'name' => 'void purchase-service',
                'group' => 'purchase-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:39',
                'updated_at' => '2022-12-16 08:35:39',
            ),
            253 => 
            array (
                'id' => 258,
                'name' => 'cancel purchase-service',
                'group' => 'purchase-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:39',
                'updated_at' => '2022-12-16 08:35:39',
            ),
            254 => 
            array (
                'id' => 259,
                'name' => 'reject purchase-service',
                'group' => 'purchase-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:39',
                'updated_at' => '2022-12-16 08:35:39',
            ),
            255 => 
            array (
                'id' => 260,
                'name' => 'close purchase-service',
                'group' => 'purchase-service',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:39',
                'updated_at' => '2022-12-16 08:35:39',
            ),
            256 => 
            array (
                'id' => 261,
                'name' => 'view purchase-general',
                'group' => 'purchase-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:39',
                'updated_at' => '2022-12-16 08:35:39',
            ),
            257 => 
            array (
                'id' => 262,
                'name' => 'create purchase-general',
                'group' => 'purchase-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:39',
                'updated_at' => '2022-12-16 08:35:39',
            ),
            258 => 
            array (
                'id' => 263,
                'name' => 'edit purchase-general',
                'group' => 'purchase-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:39',
                'updated_at' => '2022-12-16 08:35:39',
            ),
            259 => 
            array (
                'id' => 264,
                'name' => 'delete purchase-general',
                'group' => 'purchase-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:39',
                'updated_at' => '2022-12-16 08:35:39',
            ),
            260 => 
            array (
                'id' => 265,
                'name' => 'approve purchase-general',
                'group' => 'purchase-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:39',
                'updated_at' => '2022-12-16 08:35:39',
            ),
            261 => 
            array (
                'id' => 266,
                'name' => 'revert purchase-general',
                'group' => 'purchase-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:39',
                'updated_at' => '2022-12-16 08:35:39',
            ),
            262 => 
            array (
                'id' => 267,
                'name' => 'void purchase-general',
                'group' => 'purchase-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:39',
                'updated_at' => '2022-12-16 08:35:39',
            ),
            263 => 
            array (
                'id' => 268,
                'name' => 'cancel purchase-general',
                'group' => 'purchase-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:39',
                'updated_at' => '2022-12-16 08:35:39',
            ),
            264 => 
            array (
                'id' => 269,
                'name' => 'reject purchase-general',
                'group' => 'purchase-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:39',
                'updated_at' => '2022-12-16 08:35:39',
            ),
            265 => 
            array (
                'id' => 270,
                'name' => 'close purchase-general',
                'group' => 'purchase-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:39',
                'updated_at' => '2022-12-16 08:35:39',
            ),
            266 => 
            array (
                'id' => 271,
                'name' => 'view item',
                'group' => 'item',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:39',
                'updated_at' => '2022-12-16 08:35:39',
            ),
            267 => 
            array (
                'id' => 272,
                'name' => 'create item',
                'group' => 'item',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:39',
                'updated_at' => '2022-12-16 08:35:39',
            ),
            268 => 
            array (
                'id' => 273,
                'name' => 'edit item',
                'group' => 'item',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:39',
                'updated_at' => '2022-12-16 08:35:39',
            ),
            269 => 
            array (
                'id' => 274,
                'name' => 'delete item',
                'group' => 'item',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:40',
                'updated_at' => '2022-12-16 08:35:40',
            ),
            270 => 
            array (
                'id' => 275,
                'name' => 'view item-price',
                'group' => 'item-price',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:40',
                'updated_at' => '2022-12-16 08:35:40',
            ),
            271 => 
            array (
                'id' => 276,
                'name' => 'create item-price',
                'group' => 'item-price',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:40',
                'updated_at' => '2022-12-16 08:35:40',
            ),
            272 => 
            array (
                'id' => 277,
                'name' => 'edit item-price',
                'group' => 'item-price',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:40',
                'updated_at' => '2022-12-16 08:35:40',
            ),
            273 => 
            array (
                'id' => 278,
                'name' => 'delete item-price',
                'group' => 'item-price',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:40',
                'updated_at' => '2022-12-16 08:35:40',
            ),
            274 => 
            array (
                'id' => 279,
                'name' => 'view item-subtitute',
                'group' => 'item-subtitute',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:40',
                'updated_at' => '2022-12-16 08:35:40',
            ),
            275 => 
            array (
                'id' => 280,
                'name' => 'create item-subtitute',
                'group' => 'item-subtitute',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:40',
                'updated_at' => '2022-12-16 08:35:40',
            ),
            276 => 
            array (
                'id' => 281,
                'name' => 'edit item-subtitute',
                'group' => 'item-subtitute',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:40',
                'updated_at' => '2022-12-16 08:35:40',
            ),
            277 => 
            array (
                'id' => 282,
                'name' => 'delete item-subtitute',
                'group' => 'item-subtitute',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:40',
                'updated_at' => '2022-12-16 08:35:40',
            ),
            278 => 
            array (
                'id' => 283,
                'name' => 'view item-category',
                'group' => 'item-category',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:40',
                'updated_at' => '2022-12-16 08:35:40',
            ),
            279 => 
            array (
                'id' => 284,
                'name' => 'create item-category',
                'group' => 'item-category',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:40',
                'updated_at' => '2022-12-16 08:35:40',
            ),
            280 => 
            array (
                'id' => 285,
                'name' => 'edit item-category',
                'group' => 'item-category',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:40',
                'updated_at' => '2022-12-16 08:35:40',
            ),
            281 => 
            array (
                'id' => 286,
                'name' => 'delete item-category',
                'group' => 'item-category',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:40',
                'updated_at' => '2022-12-16 08:35:40',
            ),
            282 => 
            array (
                'id' => 291,
                'name' => 'view price',
                'group' => 'price',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:41',
                'updated_at' => '2022-12-16 08:35:41',
            ),
            283 => 
            array (
                'id' => 292,
                'name' => 'create price',
                'group' => 'price',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:41',
                'updated_at' => '2022-12-16 08:35:41',
            ),
            284 => 
            array (
                'id' => 293,
                'name' => 'edit price',
                'group' => 'price',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:41',
                'updated_at' => '2022-12-16 08:35:41',
            ),
            285 => 
            array (
                'id' => 294,
                'name' => 'delete price',
                'group' => 'price',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:41',
                'updated_at' => '2022-12-16 08:35:41',
            ),
            286 => 
            array (
                'id' => 295,
                'name' => 'view journal',
                'group' => 'journal',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:41',
                'updated_at' => '2022-12-16 08:35:41',
            ),
            287 => 
            array (
                'id' => 296,
                'name' => 'create journal',
                'group' => 'journal',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:41',
                'updated_at' => '2022-12-16 08:35:41',
            ),
            288 => 
            array (
                'id' => 297,
                'name' => 'edit journal',
                'group' => 'journal',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:41',
                'updated_at' => '2022-12-16 08:35:41',
            ),
            289 => 
            array (
                'id' => 298,
                'name' => 'delete journal',
                'group' => 'journal',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:41',
                'updated_at' => '2022-12-16 08:35:41',
            ),
            290 => 
            array (
                'id' => 299,
                'name' => 'approve journal',
                'group' => 'journal',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:41',
                'updated_at' => '2022-12-16 08:35:41',
            ),
            291 => 
            array (
                'id' => 300,
                'name' => 'revert journal',
                'group' => 'journal',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:41',
                'updated_at' => '2022-12-16 08:35:41',
            ),
            292 => 
            array (
                'id' => 301,
                'name' => 'void journal',
                'group' => 'journal',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:41',
                'updated_at' => '2022-12-16 08:35:41',
            ),
            293 => 
            array (
                'id' => 302,
                'name' => 'cancel journal',
                'group' => 'journal',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:41',
                'updated_at' => '2022-12-16 08:35:41',
            ),
            294 => 
            array (
                'id' => 303,
                'name' => 'reject journal',
                'group' => 'journal',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:41',
                'updated_at' => '2022-12-16 08:35:41',
            ),
            295 => 
            array (
                'id' => 304,
                'name' => 'close journal',
                'group' => 'journal',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:41',
                'updated_at' => '2022-12-16 08:35:41',
            ),
            296 => 
            array (
                'id' => 306,
                'name' => 'view unit',
                'group' => 'unit',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:42',
                'updated_at' => '2022-12-16 08:35:42',
            ),
            297 => 
            array (
                'id' => 307,
                'name' => 'create unit',
                'group' => 'unit',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:42',
                'updated_at' => '2022-12-16 08:35:42',
            ),
            298 => 
            array (
                'id' => 308,
                'name' => 'edit unit',
                'group' => 'unit',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:42',
                'updated_at' => '2022-12-16 08:35:42',
            ),
            299 => 
            array (
                'id' => 309,
                'name' => 'delete unit',
                'group' => 'unit',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:42',
                'updated_at' => '2022-12-16 08:35:42',
            ),
            300 => 
            array (
                'id' => 310,
                'name' => 'view customer',
                'group' => 'customer',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:42',
                'updated_at' => '2022-12-16 08:35:42',
            ),
            301 => 
            array (
                'id' => 311,
                'name' => 'create customer',
                'group' => 'customer',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:42',
                'updated_at' => '2022-12-16 08:35:42',
            ),
            302 => 
            array (
                'id' => 312,
                'name' => 'edit customer',
                'group' => 'customer',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:42',
                'updated_at' => '2022-12-16 08:35:42',
            ),
            303 => 
            array (
                'id' => 313,
                'name' => 'delete customer',
                'group' => 'customer',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:42',
                'updated_at' => '2022-12-16 08:35:42',
            ),
            304 => 
            array (
                'id' => 314,
                'name' => 'export customer',
                'group' => 'customer',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:42',
                'updated_at' => '2022-12-16 08:35:42',
            ),
            305 => 
            array (
                'id' => 315,
                'name' => 'import customer',
                'group' => 'customer',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:42',
                'updated_at' => '2022-12-16 08:35:42',
            ),
            306 => 
            array (
                'id' => 316,
                'name' => 'view customer-coa',
                'group' => 'customer-coa',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:42',
                'updated_at' => '2022-12-16 08:35:42',
            ),
            307 => 
            array (
                'id' => 317,
                'name' => 'create customer-coa',
                'group' => 'customer-coa',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:42',
                'updated_at' => '2022-12-16 08:35:42',
            ),
            308 => 
            array (
                'id' => 318,
                'name' => 'edit customer-coa',
                'group' => 'customer-coa',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:43',
                'updated_at' => '2022-12-16 08:35:43',
            ),
            309 => 
            array (
                'id' => 319,
                'name' => 'view sh-number',
                'group' => 'sh-number',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:43',
                'updated_at' => '2022-12-16 08:35:43',
            ),
            310 => 
            array (
                'id' => 320,
                'name' => 'create sh-number',
                'group' => 'sh-number',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:43',
                'updated_at' => '2022-12-16 08:35:43',
            ),
            311 => 
            array (
                'id' => 321,
                'name' => 'edit sh-number',
                'group' => 'sh-number',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:43',
                'updated_at' => '2022-12-16 08:35:43',
            ),
            312 => 
            array (
                'id' => 322,
                'name' => 'delete sh-number',
                'group' => 'sh-number',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:43',
                'updated_at' => '2022-12-16 08:35:43',
            ),
            313 => 
            array (
                'id' => 323,
                'name' => 'view vendor',
                'group' => 'vendor',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:43',
                'updated_at' => '2022-12-16 08:35:43',
            ),
            314 => 
            array (
                'id' => 324,
                'name' => 'create vendor',
                'group' => 'vendor',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:43',
                'updated_at' => '2022-12-16 08:35:43',
            ),
            315 => 
            array (
                'id' => 325,
                'name' => 'edit vendor',
                'group' => 'vendor',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:43',
                'updated_at' => '2022-12-16 08:35:43',
            ),
            316 => 
            array (
                'id' => 326,
                'name' => 'delete vendor',
                'group' => 'vendor',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:43',
                'updated_at' => '2022-12-16 08:35:43',
            ),
            317 => 
            array (
                'id' => 327,
                'name' => 'view vendor-coa',
                'group' => 'vendor-coa',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:43',
                'updated_at' => '2022-12-16 08:35:43',
            ),
            318 => 
            array (
                'id' => 328,
                'name' => 'create vendor-coa',
                'group' => 'vendor-coa',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:43',
                'updated_at' => '2022-12-16 08:35:43',
            ),
            319 => 
            array (
                'id' => 329,
                'name' => 'edit vendor-coa',
                'group' => 'vendor-coa',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:43',
                'updated_at' => '2022-12-16 08:35:43',
            ),
            320 => 
            array (
                'id' => 330,
                'name' => 'view currency',
                'group' => 'currency',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:43',
                'updated_at' => '2022-12-16 08:35:43',
            ),
            321 => 
            array (
                'id' => 331,
                'name' => 'create currency',
                'group' => 'currency',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:43',
                'updated_at' => '2022-12-16 08:35:43',
            ),
            322 => 
            array (
                'id' => 332,
                'name' => 'edit currency',
                'group' => 'currency',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:44',
                'updated_at' => '2022-12-16 08:35:44',
            ),
            323 => 
            array (
                'id' => 333,
                'name' => 'delete currency',
                'group' => 'currency',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:44',
                'updated_at' => '2022-12-16 08:35:44',
            ),
            324 => 
            array (
                'id' => 334,
                'name' => 'view tax',
                'group' => 'tax',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:44',
                'updated_at' => '2022-12-16 08:35:44',
            ),
            325 => 
            array (
                'id' => 335,
                'name' => 'create tax',
                'group' => 'tax',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:44',
                'updated_at' => '2022-12-16 08:35:44',
            ),
            326 => 
            array (
                'id' => 336,
                'name' => 'edit tax',
                'group' => 'tax',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:44',
                'updated_at' => '2022-12-16 08:35:44',
            ),
            327 => 
            array (
                'id' => 337,
                'name' => 'delete tax',
                'group' => 'tax',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:44',
                'updated_at' => '2022-12-16 08:35:44',
            ),
            328 => 
            array (
                'id' => 338,
                'name' => 'view sales-order',
                'group' => 'sales-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:44',
                'updated_at' => '2022-12-16 08:35:44',
            ),
            329 => 
            array (
                'id' => 339,
                'name' => 'create sales-order',
                'group' => 'sales-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:44',
                'updated_at' => '2022-12-16 08:35:44',
            ),
            330 => 
            array (
                'id' => 340,
                'name' => 'edit sales-order',
                'group' => 'sales-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:44',
                'updated_at' => '2022-12-16 08:35:44',
            ),
            331 => 
            array (
                'id' => 341,
                'name' => 'delete sales-order',
                'group' => 'sales-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:44',
                'updated_at' => '2022-12-16 08:35:44',
            ),
            332 => 
            array (
                'id' => 342,
                'name' => 'approve sales-order',
                'group' => 'sales-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:44',
                'updated_at' => '2022-12-16 08:35:44',
            ),
            333 => 
            array (
                'id' => 343,
                'name' => 'revert sales-order',
                'group' => 'sales-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:44',
                'updated_at' => '2022-12-16 08:35:44',
            ),
            334 => 
            array (
                'id' => 344,
                'name' => 'void sales-order',
                'group' => 'sales-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:44',
                'updated_at' => '2022-12-16 08:35:44',
            ),
            335 => 
            array (
                'id' => 345,
                'name' => 'cancel sales-order',
                'group' => 'sales-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:44',
                'updated_at' => '2022-12-16 08:35:44',
            ),
            336 => 
            array (
                'id' => 346,
                'name' => 'reject sales-order',
                'group' => 'sales-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:45',
                'updated_at' => '2022-12-16 08:35:45',
            ),
            337 => 
            array (
                'id' => 347,
                'name' => 'close sales-order',
                'group' => 'sales-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:45',
                'updated_at' => '2022-12-16 08:35:45',
            ),
            338 => 
            array (
                'id' => 348,
                'name' => 'pairing sales-order',
                'group' => 'sales-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:45',
                'updated_at' => '2022-12-16 08:35:45',
            ),
            339 => 
            array (
                'id' => 349,
                'name' => 'view sales-order-general',
                'group' => 'sales-order-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:45',
                'updated_at' => '2022-12-16 08:35:45',
            ),
            340 => 
            array (
                'id' => 350,
                'name' => 'create sales-order-general',
                'group' => 'sales-order-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:45',
                'updated_at' => '2022-12-16 08:35:45',
            ),
            341 => 
            array (
                'id' => 351,
                'name' => 'edit sales-order-general',
                'group' => 'sales-order-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:45',
                'updated_at' => '2022-12-16 08:35:45',
            ),
            342 => 
            array (
                'id' => 352,
                'name' => 'delete sales-order-general',
                'group' => 'sales-order-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:45',
                'updated_at' => '2022-12-16 08:35:45',
            ),
            343 => 
            array (
                'id' => 353,
                'name' => 'approve sales-order-general',
                'group' => 'sales-order-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:45',
                'updated_at' => '2022-12-16 08:35:45',
            ),
            344 => 
            array (
                'id' => 354,
                'name' => 'revert sales-order-general',
                'group' => 'sales-order-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:45',
                'updated_at' => '2022-12-16 08:35:45',
            ),
            345 => 
            array (
                'id' => 355,
                'name' => 'void sales-order-general',
                'group' => 'sales-order-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:45',
                'updated_at' => '2022-12-16 08:35:45',
            ),
            346 => 
            array (
                'id' => 356,
                'name' => 'cancel sales-order-general',
                'group' => 'sales-order-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:45',
                'updated_at' => '2022-12-16 08:35:45',
            ),
            347 => 
            array (
                'id' => 357,
                'name' => 'reject sales-order-general',
                'group' => 'sales-order-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:45',
                'updated_at' => '2022-12-16 08:35:45',
            ),
            348 => 
            array (
                'id' => 358,
                'name' => 'close sales-order-general',
                'group' => 'sales-order-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:45',
                'updated_at' => '2022-12-16 08:35:45',
            ),
            349 => 
            array (
                'id' => 359,
                'name' => 'pairing sales-order-general',
                'group' => 'sales-order-general',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:46',
                'updated_at' => '2022-12-16 08:35:46',
            ),
            350 => 
            array (
                'id' => 360,
                'name' => 'view delivery-order',
                'group' => 'delivery-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:46',
                'updated_at' => '2022-12-16 08:35:46',
            ),
            351 => 
            array (
                'id' => 361,
                'name' => 'create delivery-order',
                'group' => 'delivery-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:46',
                'updated_at' => '2022-12-16 08:35:46',
            ),
            352 => 
            array (
                'id' => 362,
                'name' => 'edit delivery-order',
                'group' => 'delivery-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:46',
                'updated_at' => '2022-12-16 08:35:46',
            ),
            353 => 
            array (
                'id' => 363,
                'name' => 'delete delivery-order',
                'group' => 'delivery-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:46',
                'updated_at' => '2022-12-16 08:35:46',
            ),
            354 => 
            array (
                'id' => 364,
                'name' => 'approve delivery-order',
                'group' => 'delivery-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:46',
                'updated_at' => '2022-12-16 08:35:46',
            ),
            355 => 
            array (
                'id' => 365,
                'name' => 'revert delivery-order',
                'group' => 'delivery-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:46',
                'updated_at' => '2022-12-16 08:35:46',
            ),
            356 => 
            array (
                'id' => 366,
                'name' => 'void delivery-order',
                'group' => 'delivery-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:46',
                'updated_at' => '2022-12-16 08:35:46',
            ),
            357 => 
            array (
                'id' => 367,
                'name' => 'cancel delivery-order',
                'group' => 'delivery-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:46',
                'updated_at' => '2022-12-16 08:35:46',
            ),
            358 => 
            array (
                'id' => 368,
                'name' => 'reject delivery-order',
                'group' => 'delivery-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:46',
                'updated_at' => '2022-12-16 08:35:46',
            ),
            359 => 
            array (
                'id' => 369,
                'name' => 'close delivery-order',
                'group' => 'delivery-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:46',
                'updated_at' => '2022-12-16 08:35:46',
            ),
            360 => 
            array (
                'id' => 370,
                'name' => 'view invoice-trading',
                'group' => 'invoice-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:46',
                'updated_at' => '2022-12-16 08:35:46',
            ),
            361 => 
            array (
                'id' => 371,
                'name' => 'create invoice-trading',
                'group' => 'invoice-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:46',
                'updated_at' => '2022-12-16 08:35:46',
            ),
            362 => 
            array (
                'id' => 372,
                'name' => 'edit invoice-trading',
                'group' => 'invoice-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:47',
                'updated_at' => '2022-12-16 08:35:47',
            ),
            363 => 
            array (
                'id' => 373,
                'name' => 'delete invoice-trading',
                'group' => 'invoice-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:47',
                'updated_at' => '2022-12-16 08:35:47',
            ),
            364 => 
            array (
                'id' => 374,
                'name' => 'approve invoice-trading',
                'group' => 'invoice-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:47',
                'updated_at' => '2022-12-16 08:35:47',
            ),
            365 => 
            array (
                'id' => 375,
                'name' => 'revert invoice-trading',
                'group' => 'invoice-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:47',
                'updated_at' => '2022-12-16 08:35:47',
            ),
            366 => 
            array (
                'id' => 376,
                'name' => 'void invoice-trading',
                'group' => 'invoice-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:47',
                'updated_at' => '2022-12-16 08:35:47',
            ),
            367 => 
            array (
                'id' => 377,
                'name' => 'cancel invoice-trading',
                'group' => 'invoice-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:47',
                'updated_at' => '2022-12-16 08:35:47',
            ),
            368 => 
            array (
                'id' => 378,
                'name' => 'reject invoice-trading',
                'group' => 'invoice-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:47',
                'updated_at' => '2022-12-16 08:35:47',
            ),
            369 => 
            array (
                'id' => 379,
                'name' => 'close invoice-trading',
                'group' => 'invoice-trading',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:47',
                'updated_at' => '2022-12-16 08:35:47',
            ),
            370 => 
            array (
                'id' => 380,
                'name' => 'view quotation',
                'group' => 'quotation',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:47',
                'updated_at' => '2022-12-16 08:35:47',
            ),
            371 => 
            array (
                'id' => 381,
                'name' => 'create quotation',
                'group' => 'quotation',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:47',
                'updated_at' => '2022-12-16 08:35:47',
            ),
            372 => 
            array (
                'id' => 382,
                'name' => 'edit quotation',
                'group' => 'quotation',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:47',
                'updated_at' => '2022-12-16 08:35:47',
            ),
            373 => 
            array (
                'id' => 383,
                'name' => 'delete quotation',
                'group' => 'quotation',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:47',
                'updated_at' => '2022-12-16 08:35:47',
            ),
            374 => 
            array (
                'id' => 384,
                'name' => 'view quotation-add-on-type',
                'group' => 'quotation-add-on-type',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:47',
                'updated_at' => '2022-12-16 08:35:47',
            ),
            375 => 
            array (
                'id' => 385,
                'name' => 'create quotation-add-on-type',
                'group' => 'quotation-add-on-type',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:48',
                'updated_at' => '2022-12-16 08:35:48',
            ),
            376 => 
            array (
                'id' => 386,
                'name' => 'edit quotation-add-on-type',
                'group' => 'quotation-add-on-type',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:48',
                'updated_at' => '2022-12-16 08:35:48',
            ),
            377 => 
            array (
                'id' => 387,
                'name' => 'delete quotation-add-on-type',
                'group' => 'quotation-add-on-type',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:48',
                'updated_at' => '2022-12-16 08:35:48',
            ),
            378 => 
            array (
                'id' => 388,
                'name' => 'view transport-delivery-order',
                'group' => 'transport-delivery-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:48',
                'updated_at' => '2022-12-16 08:35:48',
            ),
            379 => 
            array (
                'id' => 389,
                'name' => 'edit transport-delivery-order',
                'group' => 'transport-delivery-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:48',
                'updated_at' => '2022-12-16 08:35:48',
            ),
            380 => 
            array (
                'id' => 390,
                'name' => 'request-print transport-delivery-order',
                'group' => 'transport-delivery-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:48',
                'updated_at' => '2022-12-16 08:35:48',
            ),
            381 => 
            array (
                'id' => 391,
                'name' => 'print transport-delivery-order',
                'group' => 'transport-delivery-order',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:48',
                'updated_at' => '2022-12-16 08:35:48',
            ),
            382 => 
            array (
                'id' => 392,
                'name' => 'hrd dashboard',
                'group' => 'dashboard',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:48',
                'updated_at' => '2022-12-16 08:35:48',
            ),
            383 => 
            array (
                'id' => 393,
                'name' => 'purchase dashboard',
                'group' => 'dashboard',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:48',
                'updated_at' => '2022-12-16 08:35:48',
            ),
            384 => 
            array (
                'id' => 394,
                'name' => 'sales dashboard',
                'group' => 'dashboard',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:48',
                'updated_at' => '2022-12-16 08:35:48',
            ),
            385 => 
            array (
                'id' => 395,
                'name' => 'warehouse dashboard',
                'group' => 'dashboard',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:48',
                'updated_at' => '2022-12-16 08:35:48',
            ),
            386 => 
            array (
                'id' => 396,
                'name' => 'trading dashboard',
                'group' => 'dashboard',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:48',
                'updated_at' => '2022-12-16 08:35:48',
            ),
            387 => 
            array (
                'id' => 397,
                'name' => 'view fund-submission',
                'group' => 'fund-submission',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:48',
                'updated_at' => '2022-12-16 08:35:48',
            ),
            388 => 
            array (
                'id' => 398,
                'name' => 'create fund-submission',
                'group' => 'fund-submission',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:49',
                'updated_at' => '2022-12-16 08:35:49',
            ),
            389 => 
            array (
                'id' => 399,
                'name' => 'edit fund-submission',
                'group' => 'fund-submission',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:49',
                'updated_at' => '2022-12-16 08:35:49',
            ),
            390 => 
            array (
                'id' => 400,
                'name' => 'delete fund-submission',
                'group' => 'fund-submission',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:49',
                'updated_at' => '2022-12-16 08:35:49',
            ),
            391 => 
            array (
                'id' => 401,
                'name' => 'approve fund-submission',
                'group' => 'fund-submission',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:49',
                'updated_at' => '2022-12-16 08:35:49',
            ),
            392 => 
            array (
                'id' => 402,
                'name' => 'revert fund-submission',
                'group' => 'fund-submission',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:49',
                'updated_at' => '2022-12-16 08:35:49',
            ),
            393 => 
            array (
                'id' => 403,
                'name' => 'void fund-submission',
                'group' => 'fund-submission',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:49',
                'updated_at' => '2022-12-16 08:35:49',
            ),
            394 => 
            array (
                'id' => 404,
                'name' => 'cancel fund-submission',
                'group' => 'fund-submission',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:49',
                'updated_at' => '2022-12-16 08:35:49',
            ),
            395 => 
            array (
                'id' => 405,
                'name' => 'reject fund-submission',
                'group' => 'fund-submission',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:49',
                'updated_at' => '2022-12-16 08:35:49',
            ),
            396 => 
            array (
                'id' => 406,
                'name' => 'close fund-submission',
                'group' => 'fund-submission',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:49',
                'updated_at' => '2022-12-16 08:35:49',
            ),
            397 => 
            array (
                'id' => 407,
                'name' => 'view invoice',
                'group' => 'invoice',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:49',
                'updated_at' => '2022-12-16 08:35:49',
            ),
            398 => 
            array (
                'id' => 408,
                'name' => 'create invoice',
                'group' => 'invoice',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:49',
                'updated_at' => '2022-12-16 08:35:49',
            ),
            399 => 
            array (
                'id' => 409,
                'name' => 'edit invoice',
                'group' => 'invoice',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:49',
                'updated_at' => '2022-12-16 08:35:49',
            ),
            400 => 
            array (
                'id' => 410,
                'name' => 'delete invoice',
                'group' => 'invoice',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:50',
                'updated_at' => '2022-12-16 08:35:50',
            ),
            401 => 
            array (
                'id' => 415,
                'name' => 'view customer-receivable',
                'group' => 'customer-receivable',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:50',
                'updated_at' => '2022-12-16 08:35:50',
            ),
            402 => 
            array (
                'id' => 416,
                'name' => 'create customer-receivable',
                'group' => 'customer-receivable',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:50',
                'updated_at' => '2022-12-16 08:35:50',
            ),
            403 => 
            array (
                'id' => 417,
                'name' => 'edit customer-receivable',
                'group' => 'customer-receivable',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:50',
                'updated_at' => '2022-12-16 08:35:50',
            ),
            404 => 
            array (
                'id' => 418,
                'name' => 'delete customer-receivable',
                'group' => 'customer-receivable',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:50',
                'updated_at' => '2022-12-16 08:35:50',
            ),
            405 => 
            array (
                'id' => 419,
                'name' => 'view supplier-invoice',
                'group' => 'supplier-invoice',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:50',
                'updated_at' => '2022-12-16 08:35:50',
            ),
            406 => 
            array (
                'id' => 420,
                'name' => 'create supplier-invoice',
                'group' => 'supplier-invoice',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:51',
                'updated_at' => '2022-12-16 08:35:51',
            ),
            407 => 
            array (
                'id' => 421,
                'name' => 'edit supplier-invoice',
                'group' => 'supplier-invoice',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:51',
                'updated_at' => '2022-12-16 08:35:51',
            ),
            408 => 
            array (
                'id' => 422,
                'name' => 'delete supplier-invoice',
                'group' => 'supplier-invoice',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:51',
                'updated_at' => '2022-12-16 08:35:51',
            ),
            409 => 
            array (
                'id' => 427,
                'name' => 'view cash-advance-payment',
                'group' => 'cash-advance-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:51',
                'updated_at' => '2022-12-16 08:35:51',
            ),
            410 => 
            array (
                'id' => 428,
                'name' => 'create cash-advance-payment',
                'group' => 'cash-advance-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:51',
                'updated_at' => '2022-12-16 08:35:51',
            ),
            411 => 
            array (
                'id' => 429,
                'name' => 'edit cash-advance-payment',
                'group' => 'cash-advance-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:51',
                'updated_at' => '2022-12-16 08:35:51',
            ),
            412 => 
            array (
                'id' => 430,
                'name' => 'delete cash-advance-payment',
                'group' => 'cash-advance-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:51',
                'updated_at' => '2022-12-16 08:35:51',
            ),
            413 => 
            array (
                'id' => 431,
                'name' => 'view cash-advance-return',
                'group' => 'cash-advance-return',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:51',
                'updated_at' => '2022-12-16 08:35:51',
            ),
            414 => 
            array (
                'id' => 432,
                'name' => 'create cash-advance-return',
                'group' => 'cash-advance-return',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:52',
                'updated_at' => '2022-12-16 08:35:52',
            ),
            415 => 
            array (
                'id' => 433,
                'name' => 'edit cash-advance-return',
                'group' => 'cash-advance-return',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:52',
                'updated_at' => '2022-12-16 08:35:52',
            ),
            416 => 
            array (
                'id' => 434,
                'name' => 'delete cash-advance-return',
                'group' => 'cash-advance-return',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:52',
                'updated_at' => '2022-12-16 08:35:52',
            ),
            417 => 
            array (
                'id' => 435,
                'name' => 'view master-asset',
                'group' => 'master-asset',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:52',
                'updated_at' => '2022-12-16 08:35:52',
            ),
            418 => 
            array (
                'id' => 436,
                'name' => 'create master-asset',
                'group' => 'master-asset',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:52',
                'updated_at' => '2022-12-16 08:35:52',
            ),
            419 => 
            array (
                'id' => 437,
                'name' => 'edit master-asset',
                'group' => 'master-asset',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:52',
                'updated_at' => '2022-12-16 08:35:52',
            ),
            420 => 
            array (
                'id' => 438,
                'name' => 'delete master-asset',
                'group' => 'master-asset',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:52',
                'updated_at' => '2022-12-16 08:35:52',
            ),
            421 => 
            array (
                'id' => 439,
                'name' => 'view depreciation',
                'group' => 'depreciation',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:52',
                'updated_at' => '2022-12-16 08:35:52',
            ),
            422 => 
            array (
                'id' => 440,
                'name' => 'create depreciation',
                'group' => 'depreciation',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:52',
                'updated_at' => '2022-12-16 08:35:52',
            ),
            423 => 
            array (
                'id' => 441,
                'name' => 'edit depreciation',
                'group' => 'depreciation',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:52',
                'updated_at' => '2022-12-16 08:35:52',
            ),
            424 => 
            array (
                'id' => 442,
                'name' => 'delete depreciation',
                'group' => 'depreciation',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:53',
                'updated_at' => '2022-12-16 08:35:53',
            ),
            425 => 
            array (
                'id' => 443,
                'name' => 'view asset-disposition',
                'group' => 'asset-disposition',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:53',
                'updated_at' => '2022-12-16 08:35:53',
            ),
            426 => 
            array (
                'id' => 444,
                'name' => 'create asset-disposition',
                'group' => 'asset-disposition',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:53',
                'updated_at' => '2022-12-16 08:35:53',
            ),
            427 => 
            array (
                'id' => 445,
                'name' => 'edit asset-disposition',
                'group' => 'asset-disposition',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:53',
                'updated_at' => '2022-12-16 08:35:53',
            ),
            428 => 
            array (
                'id' => 446,
                'name' => 'delete asset-disposition',
                'group' => 'asset-disposition',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:53',
                'updated_at' => '2022-12-16 08:35:53',
            ),
            429 => 
            array (
                'id' => 491,
                'name' => 'view closing-period',
                'group' => 'closing-period',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:58',
                'updated_at' => '2022-12-16 08:35:58',
            ),
            430 => 
            array (
                'id' => 492,
                'name' => 'create closing-period',
                'group' => 'closing-period',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:58',
                'updated_at' => '2022-12-16 08:35:58',
            ),
            431 => 
            array (
                'id' => 493,
                'name' => 'edit closing-period',
                'group' => 'closing-period',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:58',
                'updated_at' => '2022-12-16 08:35:58',
            ),
            432 => 
            array (
                'id' => 494,
                'name' => 'delete closing-period',
                'group' => 'closing-period',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:58',
                'updated_at' => '2022-12-16 08:35:58',
            ),
            433 => 
            array (
                'id' => 495,
                'name' => 'view incoming-payment',
                'group' => 'incoming-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:58',
                'updated_at' => '2022-12-16 08:35:58',
            ),
            434 => 
            array (
                'id' => 496,
                'name' => 'create incoming-payment',
                'group' => 'incoming-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:58',
                'updated_at' => '2022-12-16 08:35:58',
            ),
            435 => 
            array (
                'id' => 497,
                'name' => 'edit incoming-payment',
                'group' => 'incoming-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:58',
                'updated_at' => '2022-12-16 08:35:58',
            ),
            436 => 
            array (
                'id' => 498,
                'name' => 'delete incoming-payment',
                'group' => 'incoming-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:59',
                'updated_at' => '2022-12-16 08:35:59',
            ),
            437 => 
            array (
                'id' => 499,
                'name' => 'approve incoming-payment',
                'group' => 'incoming-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:59',
                'updated_at' => '2022-12-16 08:35:59',
            ),
            438 => 
            array (
                'id' => 500,
                'name' => 'revert incoming-payment',
                'group' => 'incoming-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:59',
                'updated_at' => '2022-12-16 08:35:59',
            ),
            439 => 
            array (
                'id' => 501,
                'name' => 'reject incoming-payment',
                'group' => 'incoming-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:59',
                'updated_at' => '2022-12-16 08:35:59',
            ),
            440 => 
            array (
                'id' => 502,
                'name' => 'view outgoing-payment',
                'group' => 'outgoing-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:59',
                'updated_at' => '2022-12-16 08:35:59',
            ),
            441 => 
            array (
                'id' => 503,
                'name' => 'create outgoing-payment',
                'group' => 'outgoing-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:59',
                'updated_at' => '2022-12-16 08:35:59',
            ),
            442 => 
            array (
                'id' => 504,
                'name' => 'edit outgoing-payment',
                'group' => 'outgoing-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:59',
                'updated_at' => '2022-12-16 08:35:59',
            ),
            443 => 
            array (
                'id' => 505,
                'name' => 'delete outgoing-payment',
                'group' => 'outgoing-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:59',
                'updated_at' => '2022-12-16 08:35:59',
            ),
            444 => 
            array (
                'id' => 506,
                'name' => 'approve outgoing-payment',
                'group' => 'outgoing-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:59',
                'updated_at' => '2022-12-16 08:35:59',
            ),
            445 => 
            array (
                'id' => 507,
                'name' => 'revert outgoing-payment',
                'group' => 'outgoing-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:59',
                'updated_at' => '2022-12-16 08:35:59',
            ),
            446 => 
            array (
                'id' => 508,
                'name' => 'reject outgoing-payment',
                'group' => 'outgoing-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:36:00',
                'updated_at' => '2022-12-16 08:36:00',
            ),
            447 => 
            array (
                'id' => 509,
                'name' => 'view supplier-invoice-general',
                'group' => 'supplier-invoice-general',
                'guard_name' => 'web',
                'created_at' => '2023-01-02 04:00:45',
                'updated_at' => '2023-01-02 04:00:45',
            ),
            448 => 
            array (
                'id' => 510,
                'name' => 'create supplier-invoice-general',
                'group' => 'supplier-invoice-general',
                'guard_name' => 'web',
                'created_at' => '2023-01-02 04:00:45',
                'updated_at' => '2023-01-02 04:00:45',
            ),
            449 => 
            array (
                'id' => 511,
                'name' => 'edit supplier-invoice-general',
                'group' => 'supplier-invoice-general',
                'guard_name' => 'web',
                'created_at' => '2023-01-02 04:00:45',
                'updated_at' => '2023-01-02 04:00:45',
            ),
            450 => 
            array (
                'id' => 512,
                'name' => 'delete supplier-invoice-general',
                'group' => 'supplier-invoice-general',
                'guard_name' => 'web',
                'created_at' => '2023-01-02 04:00:45',
                'updated_at' => '2023-01-02 04:00:45',
            ),
            451 => 
            array (
                'id' => 513,
                'name' => 'approve supplier-invoice-general',
                'group' => 'supplier-invoice-general',
                'guard_name' => 'web',
                'created_at' => '2023-01-02 04:00:45',
                'updated_at' => '2023-01-02 04:00:45',
            ),
            452 => 
            array (
                'id' => 514,
                'name' => 'reject supplier-invoice-general',
                'group' => 'supplier-invoice-general',
                'guard_name' => 'web',
                'created_at' => '2023-01-02 04:00:45',
                'updated_at' => '2023-01-02 04:00:45',
            ),
            453 => 
            array (
                'id' => 515,
                'name' => 'approve supplier-invoice',
                'group' => 'supplier-invoice',
                'guard_name' => 'web',
                'created_at' => '2023-01-02 04:01:22',
                'updated_at' => '2023-01-02 04:01:22',
            ),
            454 => 
            array (
                'id' => 516,
                'name' => 'reject supplier-invoice',
                'group' => 'supplier-invoice',
                'guard_name' => 'web',
                'created_at' => '2023-01-02 04:01:51',
                'updated_at' => '2023-01-02 04:01:51',
            ),
            455 => 
            array (
                'id' => 527,
                'name' => 'view default-coa',
                'group' => 'default-coa',
                'guard_name' => 'web',
                'created_at' => '2023-01-17 00:53:21',
                'updated_at' => '2023-01-17 00:53:21',
            ),
            456 => 
            array (
                'id' => 528,
                'name' => 'edit default-coa',
                'group' => 'default-coa',
                'guard_name' => 'web',
                'created_at' => '2023-01-17 00:53:35',
                'updated_at' => '2023-01-17 00:53:35',
            ),
            457 => 
            array (
                'id' => 529,
                'name' => 'approve cash-advance-return',
                'group' => 'cash-advance-return',
                'guard_name' => 'web',
                'created_at' => '2023-01-17 01:12:45',
                'updated_at' => '2023-01-17 01:12:45',
            ),
            458 => 
            array (
                'id' => 530,
                'name' => 'reject cash-advance-return',
                'group' => 'cash-advance-return',
                'guard_name' => 'web',
                'created_at' => '2023-01-17 01:13:03',
                'updated_at' => '2023-01-17 01:13:03',
            ),
            459 => 
            array (
                'id' => 531,
                'name' => 'void cash-advance-return',
                'group' => 'cash-advance-return',
                'guard_name' => 'web',
                'created_at' => '2023-01-17 01:13:18',
                'updated_at' => '2023-01-17 01:13:18',
            ),
            460 => 
            array (
                'id' => 532,
                'name' => 'close cash-advance-return',
                'group' => 'cash-advance-return',
                'guard_name' => 'web',
                'created_at' => '2023-01-17 01:13:37',
                'updated_at' => '2023-01-17 01:13:37',
            ),
            461 => 
            array (
                'id' => 533,
                'name' => 'revert cash-advance-return',
                'group' => 'cash-advance-return',
                'guard_name' => 'web',
                'created_at' => '2023-01-17 01:13:57',
                'updated_at' => '2023-01-17 01:13:57',
            ),
            462 => 
            array (
                'id' => 534,
                'name' => 'view user-activity',
                'group' => 'user',
                'guard_name' => 'web',
                'created_at' => '2023-01-17 04:35:01',
                'updated_at' => '2023-01-17 04:35:01',
            ),
            463 => 
            array (
                'id' => 535,
                'name' => 'view cash-bond',
                'group' => 'cash-bond',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:12:59',
                'updated_at' => '2023-01-24 07:12:59',
            ),
            464 => 
            array (
                'id' => 536,
                'name' => 'create cash-bond',
                'group' => 'cash-bond',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:12:59',
                'updated_at' => '2023-01-24 07:12:59',
            ),
            465 => 
            array (
                'id' => 537,
                'name' => 'edit cash-bond',
                'group' => 'cash-bond',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:12:59',
                'updated_at' => '2023-01-24 07:12:59',
            ),
            466 => 
            array (
                'id' => 538,
                'name' => 'delete cash-bond',
                'group' => 'cash-bond',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:12:59',
                'updated_at' => '2023-01-24 07:12:59',
            ),
            467 => 
            array (
                'id' => 539,
                'name' => 'approve cash-bond',
                'group' => 'cash-bond',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:12:59',
                'updated_at' => '2023-01-24 07:12:59',
            ),
            468 => 
            array (
                'id' => 540,
                'name' => 'reject cash-bond',
                'group' => 'cash-bond',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:12:59',
                'updated_at' => '2023-01-24 07:12:59',
            ),
            469 => 
            array (
                'id' => 541,
                'name' => 'view cash-bond-return',
                'group' => 'cash-bond-return',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:13:34',
                'updated_at' => '2023-01-24 07:13:34',
            ),
            470 => 
            array (
                'id' => 542,
                'name' => 'create cash-bond-return',
                'group' => 'cash-bond-return',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:13:34',
                'updated_at' => '2023-01-24 07:13:34',
            ),
            471 => 
            array (
                'id' => 543,
                'name' => 'edit cash-bond-return',
                'group' => 'cash-bond-return',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:13:35',
                'updated_at' => '2023-01-24 07:13:35',
            ),
            472 => 
            array (
                'id' => 544,
                'name' => 'delete cash-bond-return',
                'group' => 'cash-bond-return',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:13:35',
                'updated_at' => '2023-01-24 07:13:35',
            ),
            473 => 
            array (
                'id' => 545,
                'name' => 'approve cash-bond-return',
                'group' => 'cash-bond-return',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:13:35',
                'updated_at' => '2023-01-24 07:13:35',
            ),
            474 => 
            array (
                'id' => 546,
                'name' => 'reject cash-bond-return',
                'group' => 'cash-bond-return',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:13:35',
                'updated_at' => '2023-01-24 07:13:35',
            ),
            475 => 
            array (
                'id' => 547,
                'name' => 'view account-payable',
                'group' => 'account-payable',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:14:55',
                'updated_at' => '2023-01-24 07:14:55',
            ),
            476 => 
            array (
                'id' => 548,
                'name' => 'create account-payable',
                'group' => 'account-payable',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:14:55',
                'updated_at' => '2023-01-24 07:14:55',
            ),
            477 => 
            array (
                'id' => 549,
                'name' => 'edit account-payable',
                'group' => 'account-payable',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:14:55',
                'updated_at' => '2023-01-24 07:14:55',
            ),
            478 => 
            array (
                'id' => 550,
                'name' => 'delete account-payable',
                'group' => 'account-payable',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:14:55',
                'updated_at' => '2023-01-24 07:14:55',
            ),
            479 => 
            array (
                'id' => 551,
                'name' => 'approve account-payable',
                'group' => 'account-payable',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:14:55',
                'updated_at' => '2023-01-24 07:14:55',
            ),
            480 => 
            array (
                'id' => 552,
                'name' => 'reject account-payable',
                'group' => 'account-payable',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:14:55',
                'updated_at' => '2023-01-24 07:14:55',
            ),
            481 => 
            array (
                'id' => 553,
                'name' => 'void account-payable',
                'group' => 'account-payable',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:14:55',
                'updated_at' => '2023-01-24 07:14:55',
            ),
            482 => 
            array (
                'id' => 554,
                'name' => 'revert account-payable',
                'group' => 'account-payable',
                'guard_name' => 'web',
                'created_at' => '2023-01-24 07:14:56',
                'updated_at' => '2023-01-24 07:14:56',
            ),
            483 => 
            array (
                'id' => 560,
                'name' => 'approve labor-demand',
                'group' => 'labor-demand',
                'guard_name' => 'web',
                'created_at' => '2023-01-27 03:39:28',
                'updated_at' => '2023-01-27 03:39:28',
            ),
            484 => 
            array (
                'id' => 561,
                'name' => 'reject labor-demand',
                'group' => 'labor-demand',
                'guard_name' => 'web',
                'created_at' => '2023-01-27 03:39:28',
                'updated_at' => '2023-01-27 03:39:28',
            ),
            485 => 
            array (
                'id' => 562,
                'name' => 'revert labor-demand',
                'group' => 'labor-demand',
                'guard_name' => 'web',
                'created_at' => '2023-01-27 03:39:28',
                'updated_at' => '2023-01-27 03:39:28',
            ),
            486 => 
            array (
                'id' => 563,
                'name' => 'close labor-demand',
                'group' => 'labor-demand',
                'guard_name' => 'web',
                'created_at' => '2023-01-27 03:39:28',
                'updated_at' => '2023-01-27 03:39:28',
            ),
            487 => 
            array (
                'id' => 564,
                'name' => 'cancel labor-demand',
                'group' => 'labor-demand',
                'guard_name' => 'web',
                'created_at' => '2023-01-27 03:39:28',
                'updated_at' => '2023-01-27 03:39:28',
            ),
            488 => 
            array (
                'id' => 565,
                'name' => 'void labor-demand',
                'group' => 'labor-demand',
                'guard_name' => 'web',
                'created_at' => '2023-01-27 03:39:29',
                'updated_at' => '2023-01-27 03:39:29',
            ),
            489 => 
            array (
                'id' => 566,
                'name' => 'view tax-reconciliation',
                'group' => 'tax-reconciliation',
                'guard_name' => 'web',
                'created_at' => '2023-01-31 16:33:43',
                'updated_at' => '2023-01-31 16:33:43',
            ),
            490 => 
            array (
                'id' => 567,
                'name' => 'create tax-reconciliation',
                'group' => 'tax-reconciliation',
                'guard_name' => 'web',
                'created_at' => '2023-01-31 16:33:43',
                'updated_at' => '2023-01-31 16:33:43',
            ),
            491 => 
            array (
                'id' => 568,
                'name' => 'edit tax-reconciliation',
                'group' => 'tax-reconciliation',
                'guard_name' => 'web',
                'created_at' => '2023-01-31 16:33:43',
                'updated_at' => '2023-01-31 16:33:43',
            ),
            492 => 
            array (
                'id' => 569,
                'name' => 'delete tax-reconciliation',
                'group' => 'tax-reconciliation',
                'guard_name' => 'web',
                'created_at' => '2023-01-31 16:33:44',
                'updated_at' => '2023-01-31 16:33:44',
            ),
            493 => 
            array (
                'id' => 570,
                'name' => 'approve tax-reconciliation',
                'group' => 'tax-reconciliation',
                'guard_name' => 'web',
                'created_at' => '2023-01-31 16:33:44',
                'updated_at' => '2023-01-31 16:33:44',
            ),
            494 => 
            array (
                'id' => 571,
                'name' => 'reject tax-reconciliation',
                'group' => 'tax-reconciliation',
                'guard_name' => 'web',
                'created_at' => '2023-01-31 16:33:44',
                'updated_at' => '2023-01-31 16:33:44',
            ),
            495 => 
            array (
                'id' => 572,
                'name' => 'revert tax-reconciliation',
                'group' => 'tax-reconciliation',
                'guard_name' => 'web',
                'created_at' => '2023-01-31 16:33:44',
                'updated_at' => '2023-01-31 16:33:44',
            ),
            496 => 
            array (
                'id' => 573,
                'name' => 'void tax-reconciliation',
                'group' => 'tax-reconciliation',
                'guard_name' => 'web',
                'created_at' => '2023-01-31 16:33:44',
                'updated_at' => '2023-01-31 16:33:44',
            ),
            497 => 
            array (
                'id' => 574,
                'name' => 'view tax-trading',
                'group' => 'tax-trading',
                'guard_name' => 'web',
                'created_at' => '2023-01-31 16:48:25',
                'updated_at' => '2023-01-31 16:48:25',
            ),
            498 => 
            array (
                'id' => 575,
                'name' => 'create tax-trading',
                'group' => 'tax-trading',
                'guard_name' => 'web',
                'created_at' => '2023-01-31 16:48:25',
                'updated_at' => '2023-01-31 16:48:25',
            ),
            499 => 
            array (
                'id' => 576,
                'name' => 'view degree',
                'group' => 'degree',
                'guard_name' => 'web',
                'created_at' => '2023-02-07 16:36:30',
                'updated_at' => '2023-02-07 16:36:30',
            ),
        ));
        \DB::table('permissions')->insert(array (
            0 => 
            array (
                'id' => 577,
                'name' => 'create degree',
                'group' => 'degree',
                'guard_name' => 'web',
                'created_at' => '2023-02-07 16:36:31',
                'updated_at' => '2023-02-07 16:36:31',
            ),
            1 => 
            array (
                'id' => 578,
                'name' => 'edit degree',
                'group' => 'degree',
                'guard_name' => 'web',
                'created_at' => '2023-02-07 16:36:31',
                'updated_at' => '2023-02-07 16:36:31',
            ),
            2 => 
            array (
                'id' => 579,
                'name' => 'delete degree',
                'group' => 'degree',
                'guard_name' => 'web',
                'created_at' => '2023-02-07 16:36:31',
                'updated_at' => '2023-02-07 16:36:31',
            ),
            3 => 
            array (
                'id' => 580,
                'name' => 'view education',
                'group' => 'education',
                'guard_name' => 'web',
                'created_at' => '2023-02-07 16:36:44',
                'updated_at' => '2023-02-07 16:36:44',
            ),
            4 => 
            array (
                'id' => 581,
                'name' => 'create education',
                'group' => 'education',
                'guard_name' => 'web',
                'created_at' => '2023-02-07 16:36:44',
                'updated_at' => '2023-02-07 16:36:44',
            ),
            5 => 
            array (
                'id' => 582,
                'name' => 'edit education',
                'group' => 'education',
                'guard_name' => 'web',
                'created_at' => '2023-02-07 16:36:45',
                'updated_at' => '2023-02-07 16:36:45',
            ),
            6 => 
            array (
                'id' => 583,
                'name' => 'delete education',
                'group' => 'education',
                'guard_name' => 'web',
                'created_at' => '2023-02-07 16:36:45',
                'updated_at' => '2023-02-07 16:36:45',
            ),
            7 => 
            array (
                'id' => 584,
                'name' => 'view labor-application',
                'group' => 'labor-application',
                'guard_name' => 'web',
                'created_at' => '2023-02-07 16:37:09',
                'updated_at' => '2023-02-07 16:37:09',
            ),
            8 => 
            array (
                'id' => 585,
                'name' => 'create labor-application',
                'group' => 'labor-application',
                'guard_name' => 'web',
                'created_at' => '2023-02-07 16:37:09',
                'updated_at' => '2023-02-07 16:37:09',
            ),
            9 => 
            array (
                'id' => 586,
                'name' => 'edit labor-application',
                'group' => 'labor-application',
                'guard_name' => 'web',
                'created_at' => '2023-02-07 16:37:09',
                'updated_at' => '2023-02-07 16:37:09',
            ),
            10 => 
            array (
                'id' => 587,
                'name' => 'delete labor-application',
                'group' => 'labor-application',
                'guard_name' => 'web',
                'created_at' => '2023-02-07 16:37:10',
                'updated_at' => '2023-02-07 16:37:10',
            ),
            11 => 
            array (
                'id' => 588,
                'name' => 'approve labor-application',
                'group' => 'labor-application',
                'guard_name' => 'web',
                'created_at' => '2023-02-07 16:37:10',
                'updated_at' => '2023-02-07 16:37:10',
            ),
            12 => 
            array (
                'id' => 589,
                'name' => 'reject labor-application',
                'group' => 'labor-application',
                'guard_name' => 'web',
                'created_at' => '2023-02-07 16:37:10',
                'updated_at' => '2023-02-07 16:37:10',
            ),
            13 => 
            array (
                'id' => 590,
                'name' => 'view delivery-order-general',
                'group' => 'delivery-order-general',
                'guard_name' => 'web',
                'created_at' => '2023-02-09 19:46:15',
                'updated_at' => '2023-02-09 19:46:15',
            ),
            14 => 
            array (
                'id' => 591,
                'name' => 'create delivery-order-general',
                'group' => 'delivery-order-general',
                'guard_name' => 'web',
                'created_at' => '2023-02-09 19:46:15',
                'updated_at' => '2023-02-09 19:46:15',
            ),
            15 => 
            array (
                'id' => 592,
                'name' => 'edit delivery-order-general',
                'group' => 'delivery-order-general',
                'guard_name' => 'web',
                'created_at' => '2023-02-09 19:46:15',
                'updated_at' => '2023-02-09 19:46:15',
            ),
            16 => 
            array (
                'id' => 593,
                'name' => 'delete delivery-order-general',
                'group' => 'delivery-order-general',
                'guard_name' => 'web',
                'created_at' => '2023-02-09 19:46:16',
                'updated_at' => '2023-02-09 19:46:16',
            ),
            17 => 
            array (
                'id' => 594,
                'name' => 'approve delivery-order-general',
                'group' => 'delivery-order-general',
                'guard_name' => 'web',
                'created_at' => '2023-02-09 19:46:16',
                'updated_at' => '2023-02-09 19:46:16',
            ),
            18 => 
            array (
                'id' => 595,
                'name' => 'reject delivery-order-general',
                'group' => 'delivery-order-general',
                'guard_name' => 'web',
                'created_at' => '2023-02-09 19:46:16',
                'updated_at' => '2023-02-09 19:46:16',
            ),
            19 => 
            array (
                'id' => 596,
                'name' => 'close delivery-order-general',
                'group' => 'delivery-order-general',
                'guard_name' => 'web',
                'created_at' => '2023-02-09 19:46:16',
                'updated_at' => '2023-02-09 19:46:16',
            ),
            20 => 
            array (
                'id' => 597,
                'name' => 'void delivery-order-general',
                'group' => 'delivery-order-general',
                'guard_name' => 'web',
                'created_at' => '2023-02-09 19:46:16',
                'updated_at' => '2023-02-09 19:46:16',
            ),
            21 => 
            array (
                'id' => 598,
                'name' => 'view invoice-general',
                'group' => 'invoice-general',
                'guard_name' => 'web',
                'created_at' => '2023-02-09 19:47:29',
                'updated_at' => '2023-02-09 19:47:29',
            ),
            22 => 
            array (
                'id' => 599,
                'name' => 'create invoice-general',
                'group' => 'invoice-general',
                'guard_name' => 'web',
                'created_at' => '2023-02-09 19:47:29',
                'updated_at' => '2023-02-09 19:47:29',
            ),
            23 => 
            array (
                'id' => 600,
                'name' => 'edit invoice-general',
                'group' => 'invoice-general',
                'guard_name' => 'web',
                'created_at' => '2023-02-09 19:47:29',
                'updated_at' => '2023-02-09 19:47:29',
            ),
            24 => 
            array (
                'id' => 601,
                'name' => 'delete invoice-general',
                'group' => 'invoice-general',
                'guard_name' => 'web',
                'created_at' => '2023-02-09 19:47:29',
                'updated_at' => '2023-02-09 19:47:29',
            ),
            25 => 
            array (
                'id' => 602,
                'name' => 'approve invoice-general',
                'group' => 'invoice-general',
                'guard_name' => 'web',
                'created_at' => '2023-02-09 19:47:29',
                'updated_at' => '2023-02-09 19:47:29',
            ),
            26 => 
            array (
                'id' => 603,
                'name' => 'reject invoice-general',
                'group' => 'invoice-general',
                'guard_name' => 'web',
                'created_at' => '2023-02-09 19:47:30',
                'updated_at' => '2023-02-09 19:47:30',
            ),
            27 => 
            array (
                'id' => 604,
                'name' => 'close invoice-general',
                'group' => 'invoice-general',
                'guard_name' => 'web',
                'created_at' => '2023-02-09 19:47:30',
                'updated_at' => '2023-02-09 19:47:30',
            ),
            28 => 
            array (
                'id' => 605,
                'name' => 'void invoice-general',
                'group' => 'invoice-general',
                'guard_name' => 'web',
                'created_at' => '2023-02-09 19:47:30',
                'updated_at' => '2023-02-09 19:47:30',
            ),
            29 => 
            array (
                'id' => 606,
                'name' => 'revert invoice-general',
                'group' => 'invoice-general',
                'guard_name' => 'web',
                'created_at' => '2023-02-09 19:55:16',
                'updated_at' => '2023-02-09 19:55:16',
            ),
            30 => 
            array (
                'id' => 607,
                'name' => 'view contract-extension',
                'group' => 'contract-extension',
                'guard_name' => 'web',
                'created_at' => '2023-02-14 21:07:56',
                'updated_at' => '2023-02-14 21:07:56',
            ),
            31 => 
            array (
                'id' => 608,
                'name' => 'create contract-extension',
                'group' => 'contract-extension',
                'guard_name' => 'web',
                'created_at' => '2023-02-14 21:07:56',
                'updated_at' => '2023-02-14 21:07:56',
            ),
            32 => 
            array (
                'id' => 609,
                'name' => 'edit contract-extension',
                'group' => 'contract-extension',
                'guard_name' => 'web',
                'created_at' => '2023-02-14 21:07:57',
                'updated_at' => '2023-02-14 21:07:57',
            ),
            33 => 
            array (
                'id' => 610,
                'name' => 'delete contract-extension',
                'group' => 'contract-extension',
                'guard_name' => 'web',
                'created_at' => '2023-02-14 21:07:57',
                'updated_at' => '2023-02-14 21:07:57',
            ),
            34 => 
            array (
                'id' => 611,
                'name' => 'import contract-extension',
                'group' => 'contract-extension',
                'guard_name' => 'web',
                'created_at' => '2023-02-14 21:07:57',
                'updated_at' => '2023-02-14 21:07:57',
            ),
            35 => 
            array (
                'id' => 612,
                'name' => 'export contract-extension',
                'group' => 'contract-extension',
                'guard_name' => 'web',
                'created_at' => '2023-02-14 21:07:57',
                'updated_at' => '2023-02-14 21:07:57',
            ),
            36 => 
            array (
                'id' => 613,
                'name' => 'approve contract-extension',
                'group' => 'contract-extension',
                'guard_name' => 'web',
                'created_at' => '2023-02-14 21:07:57',
                'updated_at' => '2023-02-14 21:07:57',
            ),
            37 => 
            array (
                'id' => 614,
                'name' => 'reject contract-extension',
                'group' => 'contract-extension',
                'guard_name' => 'web',
                'created_at' => '2023-02-14 21:07:58',
                'updated_at' => '2023-02-14 21:07:58',
            ),
            38 => 
            array (
                'id' => 615,
                'name' => 'view hrd-assessment',
                'group' => 'hrd-assessment',
                'guard_name' => 'web',
                'created_at' => '2023-02-15 22:17:48',
                'updated_at' => '2023-02-15 22:17:48',
            ),
            39 => 
            array (
                'id' => 616,
                'name' => 'create hrd-assessment',
                'group' => 'hrd-assessment',
                'guard_name' => 'web',
                'created_at' => '2023-02-15 22:17:49',
                'updated_at' => '2023-02-15 22:17:49',
            ),
            40 => 
            array (
                'id' => 617,
                'name' => 'edit hrd-assessment',
                'group' => 'hrd-assessment',
                'guard_name' => 'web',
                'created_at' => '2023-02-15 22:17:49',
                'updated_at' => '2023-02-15 22:17:49',
            ),
            41 => 
            array (
                'id' => 618,
                'name' => 'delete hrd-assessment',
                'group' => 'hrd-assessment',
                'guard_name' => 'web',
                'created_at' => '2023-02-15 22:17:49',
                'updated_at' => '2023-02-15 22:17:49',
            ),
            42 => 
            array (
                'id' => 619,
                'name' => 'view master-hrd-assessment',
                'group' => 'master-hrd-assessment',
                'guard_name' => 'web',
                'created_at' => '2023-02-15 22:18:08',
                'updated_at' => '2023-02-15 22:18:08',
            ),
            43 => 
            array (
                'id' => 620,
                'name' => 'create master-hrd-assessment',
                'group' => 'master-hrd-assessment',
                'guard_name' => 'web',
                'created_at' => '2023-02-15 22:18:08',
                'updated_at' => '2023-02-15 22:18:08',
            ),
            44 => 
            array (
                'id' => 621,
                'name' => 'edit master-hrd-assessment',
                'group' => 'master-hrd-assessment',
                'guard_name' => 'web',
                'created_at' => '2023-02-15 22:18:09',
                'updated_at' => '2023-02-15 22:18:09',
            ),
            45 => 
            array (
                'id' => 622,
                'name' => 'delete master-hrd-assessment',
                'group' => 'master-hrd-assessment',
                'guard_name' => 'web',
                'created_at' => '2023-02-15 22:18:09',
                'updated_at' => '2023-02-15 22:18:09',
            ),
            46 => 
            array (
                'id' => 623,
                'name' => 'view master-user-assessment',
                'group' => 'master-user-assessment',
                'guard_name' => 'web',
                'created_at' => '2023-02-15 22:18:25',
                'updated_at' => '2023-02-15 22:18:25',
            ),
            47 => 
            array (
                'id' => 624,
                'name' => 'create master-user-assessment',
                'group' => 'master-user-assessment',
                'guard_name' => 'web',
                'created_at' => '2023-02-15 22:18:25',
                'updated_at' => '2023-02-15 22:18:25',
            ),
            48 => 
            array (
                'id' => 625,
                'name' => 'edit master-user-assessment',
                'group' => 'master-user-assessment',
                'guard_name' => 'web',
                'created_at' => '2023-02-15 22:18:26',
                'updated_at' => '2023-02-15 22:18:26',
            ),
            49 => 
            array (
                'id' => 626,
                'name' => 'delete master-user-assessment',
                'group' => 'master-user-assessment',
                'guard_name' => 'web',
                'created_at' => '2023-02-15 22:18:26',
                'updated_at' => '2023-02-15 22:18:26',
            ),
            50 => 
            array (
                'id' => 627,
                'name' => 'view user-assessment',
                'group' => 'user-assessment',
                'guard_name' => 'web',
                'created_at' => '2023-02-15 22:21:16',
                'updated_at' => '2023-02-15 22:21:16',
            ),
            51 => 
            array (
                'id' => 628,
                'name' => 'create user-assessment',
                'group' => 'user-assessment',
                'guard_name' => 'web',
                'created_at' => '2023-02-15 22:21:16',
                'updated_at' => '2023-02-15 22:21:16',
            ),
            52 => 
            array (
                'id' => 629,
                'name' => 'edit user-assessment',
                'group' => 'user-assessment',
                'guard_name' => 'web',
                'created_at' => '2023-02-15 22:21:16',
                'updated_at' => '2023-02-15 22:21:16',
            ),
            53 => 
            array (
                'id' => 630,
                'name' => 'delete user-assessment',
                'group' => 'user-assessment',
                'guard_name' => 'web',
                'created_at' => '2023-02-15 22:21:17',
                'updated_at' => '2023-02-15 22:21:17',
            ),
            54 => 
            array (
                'id' => 631,
                'name' => 'view specific-time-work-agreement',
                'group' => 'specific-time-work-agreement',
                'guard_name' => 'web',
                'created_at' => '2023-02-17 02:30:12',
                'updated_at' => '2023-02-17 02:30:12',
            ),
            55 => 
            array (
                'id' => 632,
                'name' => 'create specific-time-work-agreement',
                'group' => 'specific-time-work-agreement',
                'guard_name' => 'web',
                'created_at' => '2023-02-17 02:30:13',
                'updated_at' => '2023-02-17 02:30:13',
            ),
            56 => 
            array (
                'id' => 633,
                'name' => 'edit specific-time-work-agreement',
                'group' => 'specific-time-work-agreement',
                'guard_name' => 'web',
                'created_at' => '2023-02-17 02:30:13',
                'updated_at' => '2023-02-17 02:30:13',
            ),
            57 => 
            array (
                'id' => 634,
                'name' => 'delete specific-time-work-agreement',
                'group' => 'specific-time-work-agreement',
                'guard_name' => 'web',
                'created_at' => '2023-02-17 02:30:13',
                'updated_at' => '2023-02-17 02:30:13',
            ),
            58 => 
            array (
                'id' => 635,
                'name' => 'approve specific-time-work-agreement',
                'group' => 'specific-time-work-agreement',
                'guard_name' => 'web',
                'created_at' => '2023-02-17 02:30:13',
                'updated_at' => '2023-02-17 02:30:13',
            ),
            59 => 
            array (
                'id' => 636,
                'name' => 'reject specific-time-work-agreement',
                'group' => 'specific-time-work-agreement',
                'guard_name' => 'web',
                'created_at' => '2023-02-17 02:30:14',
                'updated_at' => '2023-02-17 02:30:14',
            ),
            60 => 
            array (
                'id' => 637,
                'name' => 'close specific-time-work-agreement',
                'group' => 'specific-time-work-agreement',
                'guard_name' => 'web',
                'created_at' => '2023-02-17 02:30:14',
                'updated_at' => '2023-02-17 02:30:14',
            ),
            61 => 
            array (
                'id' => 638,
                'name' => 'void specific-time-work-agreement',
                'group' => 'specific-time-work-agreement',
                'guard_name' => 'web',
                'created_at' => '2023-02-17 02:30:14',
                'updated_at' => '2023-02-17 02:30:14',
            ),
            62 => 
            array (
                'id' => 639,
                'name' => 'revert specific-time-work-agreement',
                'group' => 'specific-time-work-agreement',
                'guard_name' => 'web',
                'created_at' => '2023-02-17 02:30:14',
                'updated_at' => '2023-02-17 02:30:14',
            ),
            63 => 
            array (
                'id' => 640,
                'name' => 'view labor-transfer-form',
                'group' => 'labor-transfer-form',
                'guard_name' => 'web',
                'created_at' => '2023-02-20 22:22:47',
                'updated_at' => '2023-02-20 22:22:47',
            ),
            64 => 
            array (
                'id' => 641,
                'name' => 'create labor-transfer-form',
                'group' => 'labor-transfer-form',
                'guard_name' => 'web',
                'created_at' => '2023-02-20 22:22:47',
                'updated_at' => '2023-02-20 22:22:47',
            ),
            65 => 
            array (
                'id' => 642,
                'name' => 'edit labor-transfer-form',
                'group' => 'labor-transfer-form',
                'guard_name' => 'web',
                'created_at' => '2023-02-20 22:22:48',
                'updated_at' => '2023-02-20 22:22:48',
            ),
            66 => 
            array (
                'id' => 643,
                'name' => 'delete labor-transfer-form',
                'group' => 'labor-transfer-form',
                'guard_name' => 'web',
                'created_at' => '2023-02-20 22:22:48',
                'updated_at' => '2023-02-20 22:22:48',
            ),
            67 => 
            array (
                'id' => 644,
                'name' => 'approve labor-transfer-form',
                'group' => 'labor-transfer-form',
                'guard_name' => 'web',
                'created_at' => '2023-02-20 22:22:48',
                'updated_at' => '2023-02-20 22:22:48',
            ),
            68 => 
            array (
                'id' => 645,
                'name' => 'reject labor-transfer-form',
                'group' => 'labor-transfer-form',
                'guard_name' => 'web',
                'created_at' => '2023-02-20 22:22:48',
                'updated_at' => '2023-02-20 22:22:48',
            ),
            69 => 
            array (
                'id' => 646,
                'name' => 'view invoice-return',
                'group' => 'invoice-return',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:24:23',
                'updated_at' => '2023-03-01 20:24:23',
            ),
            70 => 
            array (
                'id' => 647,
                'name' => 'create invoice-return',
                'group' => 'invoice-return',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:24:23',
                'updated_at' => '2023-03-01 20:24:23',
            ),
            71 => 
            array (
                'id' => 648,
                'name' => 'edit invoice-return',
                'group' => 'invoice-return',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:24:23',
                'updated_at' => '2023-03-01 20:24:23',
            ),
            72 => 
            array (
                'id' => 649,
                'name' => 'delete invoice-return',
                'group' => 'invoice-return',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:24:23',
                'updated_at' => '2023-03-01 20:24:23',
            ),
            73 => 
            array (
                'id' => 650,
                'name' => 'approve invoice-return',
                'group' => 'invoice-return',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:24:24',
                'updated_at' => '2023-03-01 20:24:24',
            ),
            74 => 
            array (
                'id' => 651,
                'name' => 'reject invoice-return',
                'group' => 'invoice-return',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:24:24',
                'updated_at' => '2023-03-01 20:24:24',
            ),
            75 => 
            array (
                'id' => 652,
                'name' => 'revert invoice-return',
                'group' => 'invoice-return',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:24:24',
                'updated_at' => '2023-03-01 20:24:24',
            ),
            76 => 
            array (
                'id' => 653,
                'name' => 'void invoice-return',
                'group' => 'invoice-return',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:24:24',
                'updated_at' => '2023-03-01 20:24:24',
            ),
            77 => 
            array (
                'id' => 654,
                'name' => 'view cash-advance-receive',
                'group' => 'cash-advance-receive',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:24:59',
                'updated_at' => '2023-03-01 20:24:59',
            ),
            78 => 
            array (
                'id' => 655,
                'name' => 'create cash-advance-receive',
                'group' => 'cash-advance-receive',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:25:00',
                'updated_at' => '2023-03-01 20:25:00',
            ),
            79 => 
            array (
                'id' => 656,
                'name' => 'edit cash-advance-receive',
                'group' => 'cash-advance-receive',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:25:00',
                'updated_at' => '2023-03-01 20:25:00',
            ),
            80 => 
            array (
                'id' => 657,
                'name' => 'delete cash-advance-receive',
                'group' => 'cash-advance-receive',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:25:00',
                'updated_at' => '2023-03-01 20:25:00',
            ),
            81 => 
            array (
                'id' => 658,
                'name' => 'approve cash-advance-receive',
                'group' => 'cash-advance-receive',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:25:00',
                'updated_at' => '2023-03-01 20:25:00',
            ),
            82 => 
            array (
                'id' => 659,
                'name' => 'reject cash-advance-receive',
                'group' => 'cash-advance-receive',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:25:00',
                'updated_at' => '2023-03-01 20:25:00',
            ),
            83 => 
            array (
                'id' => 660,
                'name' => 'void cash-advance-receive',
                'group' => 'cash-advance-receive',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:25:01',
                'updated_at' => '2023-03-01 20:25:01',
            ),
            84 => 
            array (
                'id' => 661,
                'name' => 'revert cash-advance-receive',
                'group' => 'cash-advance-receive',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:25:01',
                'updated_at' => '2023-03-01 20:25:01',
            ),
            85 => 
            array (
                'id' => 662,
                'name' => 'view receivables-payment',
                'group' => 'receivables-payment',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:25:35',
                'updated_at' => '2023-03-01 20:25:35',
            ),
            86 => 
            array (
                'id' => 663,
                'name' => 'create receivables-payment',
                'group' => 'receivables-payment',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:25:35',
                'updated_at' => '2023-03-01 20:25:35',
            ),
            87 => 
            array (
                'id' => 664,
                'name' => 'edit receivables-payment',
                'group' => 'receivables-payment',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:25:35',
                'updated_at' => '2023-03-01 20:25:35',
            ),
            88 => 
            array (
                'id' => 665,
                'name' => 'delete receivables-payment',
                'group' => 'receivables-payment',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:25:35',
                'updated_at' => '2023-03-01 20:25:35',
            ),
            89 => 
            array (
                'id' => 666,
                'name' => 'approve receivables-payment',
                'group' => 'receivables-payment',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:25:36',
                'updated_at' => '2023-03-01 20:25:36',
            ),
            90 => 
            array (
                'id' => 667,
                'name' => 'reject receivables-payment',
                'group' => 'receivables-payment',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:25:36',
                'updated_at' => '2023-03-01 20:25:36',
            ),
            91 => 
            array (
                'id' => 668,
                'name' => 'revert receivables-payment',
                'group' => 'receivables-payment',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:25:36',
                'updated_at' => '2023-03-01 20:25:36',
            ),
            92 => 
            array (
                'id' => 669,
                'name' => 'void receivables-payment',
                'group' => 'receivables-payment',
                'guard_name' => 'web',
                'created_at' => '2023-03-01 20:25:36',
                'updated_at' => '2023-03-01 20:25:36',
            ),
            93 => 
            array (
                'id' => 670,
                'name' => 'view receive-payment',
                'group' => 'receive-payment',
                'guard_name' => 'web',
                'created_at' => '2023-03-02 17:50:09',
                'updated_at' => '2023-03-02 17:50:09',
            ),
            94 => 
            array (
                'id' => 671,
                'name' => 'create receive-payment',
                'group' => 'receive-payment',
                'guard_name' => 'web',
                'created_at' => '2023-03-02 17:50:09',
                'updated_at' => '2023-03-02 17:50:09',
            ),
            95 => 
            array (
                'id' => 672,
                'name' => 'edit receive-payment',
                'group' => 'receive-payment',
                'guard_name' => 'web',
                'created_at' => '2023-03-02 17:50:10',
                'updated_at' => '2023-03-02 17:50:10',
            ),
            96 => 
            array (
                'id' => 673,
                'name' => 'delete receive-payment',
                'group' => 'receive-payment',
                'guard_name' => 'web',
                'created_at' => '2023-03-02 17:50:10',
                'updated_at' => '2023-03-02 17:50:10',
            ),
            97 => 
            array (
                'id' => 674,
                'name' => 'approve receive-payment',
                'group' => 'receive-payment',
                'guard_name' => 'web',
                'created_at' => '2023-03-02 17:50:10',
                'updated_at' => '2023-03-02 17:50:10',
            ),
            98 => 
            array (
                'id' => 675,
                'name' => 'reject receive-payment',
                'group' => 'receive-payment',
                'guard_name' => 'web',
                'created_at' => '2023-03-02 17:50:10',
                'updated_at' => '2023-03-02 17:50:10',
            ),
            99 => 
            array (
                'id' => 676,
                'name' => 'revert receive-payment',
                'group' => 'receive-payment',
                'guard_name' => 'web',
                'created_at' => '2023-03-02 17:50:11',
                'updated_at' => '2023-03-02 17:50:11',
            ),
            100 => 
            array (
                'id' => 677,
                'name' => 'void receive-payment',
                'group' => 'receive-payment',
                'guard_name' => 'web',
                'created_at' => '2023-03-02 17:50:11',
                'updated_at' => '2023-03-02 17:50:11',
            ),
            101 => 
            array (
                'id' => 678,
                'name' => 'cancel receive-payment',
                'group' => 'receive-payment',
                'guard_name' => 'web',
                'created_at' => '2023-03-02 17:50:11',
                'updated_at' => '2023-03-02 17:50:11',
            ),
            102 => 
            array (
                'id' => 681,
                'name' => 'view purchase-return',
                'group' => 'purchase-return',
                'guard_name' => 'web',
                'created_at' => '2023-03-15 00:23:43',
                'updated_at' => '2023-03-15 00:23:43',
            ),
            103 => 
            array (
                'id' => 682,
                'name' => 'create purchase-return',
                'group' => 'purchase-return',
                'guard_name' => 'web',
                'created_at' => '2023-03-15 00:23:43',
                'updated_at' => '2023-03-15 00:23:43',
            ),
            104 => 
            array (
                'id' => 683,
                'name' => 'edit purchase-return',
                'group' => 'purchase-return',
                'guard_name' => 'web',
                'created_at' => '2023-03-15 00:23:43',
                'updated_at' => '2023-03-15 00:23:43',
            ),
            105 => 
            array (
                'id' => 684,
                'name' => 'delete purchase-return',
                'group' => 'purchase-return',
                'guard_name' => 'web',
                'created_at' => '2023-03-15 00:23:43',
                'updated_at' => '2023-03-15 00:23:43',
            ),
            106 => 
            array (
                'id' => 685,
                'name' => 'approve purchase-return',
                'group' => 'purchase-return',
                'guard_name' => 'web',
                'created_at' => '2023-03-15 00:23:44',
                'updated_at' => '2023-03-15 00:23:44',
            ),
            107 => 
            array (
                'id' => 686,
                'name' => 'reject purchase-return',
                'group' => 'purchase-return',
                'guard_name' => 'web',
                'created_at' => '2023-03-15 00:23:44',
                'updated_at' => '2023-03-15 00:23:44',
            ),
            108 => 
            array (
                'id' => 687,
                'name' => 'revert purchase-return',
                'group' => 'purchase-return',
                'guard_name' => 'web',
                'created_at' => '2023-03-15 00:23:44',
                'updated_at' => '2023-03-15 00:23:44',
            ),
            109 => 
            array (
                'id' => 688,
                'name' => 'void purchase-return',
                'group' => 'purchase-return',
                'guard_name' => 'web',
                'created_at' => '2023-03-15 00:23:45',
                'updated_at' => '2023-03-15 00:23:45',
            ),
            110 => 
            array (
                'id' => 689,
                'name' => 'view lease',
                'group' => 'lease',
                'guard_name' => 'web',
                'created_at' => '2023-03-21 21:32:46',
                'updated_at' => '2023-03-21 21:32:46',
            ),
            111 => 
            array (
                'id' => 690,
                'name' => 'create lease',
                'group' => 'lease',
                'guard_name' => 'web',
                'created_at' => '2023-03-21 21:32:47',
                'updated_at' => '2023-03-21 21:32:47',
            ),
            112 => 
            array (
                'id' => 691,
                'name' => 'edit lease',
                'group' => 'lease',
                'guard_name' => 'web',
                'created_at' => '2023-03-21 21:32:47',
                'updated_at' => '2023-03-21 21:32:47',
            ),
            113 => 
            array (
                'id' => 692,
                'name' => 'delete lease',
                'group' => 'lease',
                'guard_name' => 'web',
                'created_at' => '2023-03-21 21:32:47',
                'updated_at' => '2023-03-21 21:32:47',
            ),
            114 => 
            array (
                'id' => 693,
                'name' => 'view amortization',
                'group' => 'amortization',
                'guard_name' => 'web',
                'created_at' => '2023-03-21 21:33:08',
                'updated_at' => '2023-03-21 21:33:08',
            ),
            115 => 
            array (
                'id' => 694,
                'name' => 'create amortization',
                'group' => 'amortization',
                'guard_name' => 'web',
                'created_at' => '2023-03-21 21:33:09',
                'updated_at' => '2023-03-21 21:33:09',
            ),
            116 => 
            array (
                'id' => 695,
                'name' => 'view master-evaluation',
                'group' => 'master-evaluation',
                'guard_name' => 'web',
                'created_at' => '2023-03-23 17:01:21',
                'updated_at' => '2023-03-23 17:01:21',
            ),
            117 => 
            array (
                'id' => 696,
                'name' => 'create master-evaluation',
                'group' => 'master-evaluation',
                'guard_name' => 'web',
                'created_at' => '2023-03-23 17:01:21',
                'updated_at' => '2023-03-23 17:01:21',
            ),
            118 => 
            array (
                'id' => 697,
                'name' => 'edit master-evaluation',
                'group' => 'master-evaluation',
                'guard_name' => 'web',
                'created_at' => '2023-03-23 17:01:21',
                'updated_at' => '2023-03-23 17:01:21',
            ),
            119 => 
            array (
                'id' => 698,
                'name' => 'delete master-evaluation',
                'group' => 'master-evaluation',
                'guard_name' => 'web',
                'created_at' => '2023-03-23 17:01:22',
                'updated_at' => '2023-03-23 17:01:22',
            ),
            120 => 
            array (
                'id' => 699,
                'name' => 'view asset-category',
                'group' => 'asset-category',
                'guard_name' => 'web',
                'created_at' => '2023-05-19 16:40:04',
                'updated_at' => '2023-05-19 16:40:04',
            ),
            121 => 
            array (
                'id' => 700,
                'name' => 'create asset-category',
                'group' => 'asset-category',
                'guard_name' => 'web',
                'created_at' => '2023-05-19 16:40:05',
                'updated_at' => '2023-05-19 16:40:05',
            ),
            122 => 
            array (
                'id' => 701,
                'name' => 'edit asset-category',
                'group' => 'asset-category',
                'guard_name' => 'web',
                'created_at' => '2023-05-19 16:40:05',
                'updated_at' => '2023-05-19 16:40:05',
            ),
            123 => 
            array (
                'id' => 702,
                'name' => 'delete asset-category',
                'group' => 'asset-category',
                'guard_name' => 'web',
                'created_at' => '2023-05-19 16:40:05',
                'updated_at' => '2023-05-19 16:40:05',
            ),
            124 => 
            array (
                'id' => 703,
                'name' => 'view asset-document-type',
                'group' => 'asset-document-type',
                'guard_name' => 'web',
                'created_at' => '2023-05-19 16:40:22',
                'updated_at' => '2023-05-19 16:40:22',
            ),
            125 => 
            array (
                'id' => 704,
                'name' => 'create asset-document-type',
                'group' => 'asset-document-type',
                'guard_name' => 'web',
                'created_at' => '2023-05-19 16:40:23',
                'updated_at' => '2023-05-19 16:40:23',
            ),
            126 => 
            array (
                'id' => 705,
                'name' => 'edit asset-document-type',
                'group' => 'asset-document-type',
                'guard_name' => 'web',
                'created_at' => '2023-05-19 16:40:23',
                'updated_at' => '2023-05-19 16:40:23',
            ),
            127 => 
            array (
                'id' => 706,
                'name' => 'delete asset-document-type',
                'group' => 'asset-document-type',
                'guard_name' => 'web',
                'created_at' => '2023-05-19 16:40:23',
                'updated_at' => '2023-05-19 16:40:23',
            ),
            128 => 
            array (
                'id' => 707,
                'name' => 'change quantity-invoice-trading',
                'group' => 'invoice-trading',
                'guard_name' => 'web',
                'created_at' => '2023-05-30 21:21:28',
                'updated_at' => '2023-05-30 21:21:28',
            ),
            129 => 
            array (
                'id' => 708,
                'name' => 'finance dashboard',
                'group' => 'dashboard',
                'guard_name' => 'web',
                'created_at' => '2023-05-30 21:23:49',
                'updated_at' => '2023-05-30 21:23:49',
            ),
            130 => 
            array (
                'id' => 709,
                'name' => 'accounting dashboard',
                'group' => 'dashboard',
                'guard_name' => 'web',
                'created_at' => '2023-05-30 21:23:49',
                'updated_at' => '2023-05-30 21:23:49',
            ),
            131 => 
            array (
                'id' => 710,
                'name' => 'create asset-document',
                'group' => 'legality-document',
                'guard_name' => 'web',
                'created_at' => '2023-06-17 06:46:22',
                'updated_at' => '2023-06-17 06:46:22',
            ),
            132 => 
            array (
                'id' => 711,
                'name' => 'edit asset-document',
                'group' => 'legality-document',
                'guard_name' => 'web',
                'created_at' => '2023-06-17 06:47:16',
                'updated_at' => '2023-06-17 06:47:16',
            ),
            133 => 
            array (
                'id' => 712,
                'name' => 'delete asset-document',
                'group' => 'legality-document',
                'guard_name' => 'web',
                'created_at' => '2023-06-17 06:47:34',
                'updated_at' => '2023-06-17 06:47:34',
            ),
            134 => 
            array (
                'id' => 714,
                'name' => 'view asset-document',
                'group' => 'legality-document',
                'guard_name' => 'web',
                'created_at' => '2023-06-18 03:06:05',
                'updated_at' => '2023-06-18 03:06:05',
            ),
            135 => 
            array (
                'id' => 715,
                'name' => 'view lease-document',
                'group' => 'legality-document',
                'guard_name' => 'web',
                'created_at' => '2023-06-18 03:24:34',
                'updated_at' => '2023-06-18 03:24:34',
            ),
            136 => 
            array (
                'id' => 716,
                'name' => 'create lease-document',
                'group' => 'legality-document',
                'guard_name' => 'web',
                'created_at' => '2023-06-18 03:24:54',
                'updated_at' => '2023-06-18 03:24:54',
            ),
            137 => 
            array (
                'id' => 717,
                'name' => 'edit lease-document',
                'group' => 'legality-document',
                'guard_name' => 'web',
                'created_at' => '2023-06-18 03:25:06',
                'updated_at' => '2023-06-18 03:25:06',
            ),
            138 => 
            array (
                'id' => 718,
                'name' => 'delete lease-document',
                'group' => 'legality-document',
                'guard_name' => 'web',
                'created_at' => '2023-06-18 03:25:37',
                'updated_at' => '2023-06-18 03:25:37',
            ),
            139 => 
            array (
                'id' => 719,
                'name' => 'view offering-letter',
                'group' => 'offering-letter',
                'guard_name' => 'web',
                'created_at' => '2023-06-18 03:24:34',
                'updated_at' => '2023-06-18 03:24:34',
            ),
            140 => 
            array (
                'id' => 720,
                'name' => 'create offering-letter',
                'group' => 'offering-letter',
                'guard_name' => 'web',
                'created_at' => '2023-06-18 03:24:54',
                'updated_at' => '2023-06-18 03:24:54',
            ),
            141 => 
            array (
                'id' => 721,
                'name' => 'edit offering-letter',
                'group' => 'offering-letter',
                'guard_name' => 'web',
                'created_at' => '2023-06-18 03:25:06',
                'updated_at' => '2023-06-18 03:25:06',
            ),
            142 => 
            array (
                'id' => 722,
                'name' => 'delete offering-letter',
                'group' => 'offering-letter',
                'guard_name' => 'web',
                'created_at' => '2023-06-18 03:25:37',
                'updated_at' => '2023-06-18 03:25:37',
            ),
            143 => 
            array (
                'id' => 723,
                'name' => 'view legality-document',
                'group' => 'legality-document',
                'guard_name' => 'web',
                'created_at' => '2023-06-18 03:24:34',
                'updated_at' => '2023-06-18 03:24:34',
            ),
            144 => 
            array (
                'id' => 724,
                'name' => 'create legality-document',
                'group' => 'legality-document',
                'guard_name' => 'web',
                'created_at' => '2023-06-18 03:24:54',
                'updated_at' => '2023-06-18 03:24:54',
            ),
            145 => 
            array (
                'id' => 725,
                'name' => 'edit legality-document',
                'group' => 'legality-document',
                'guard_name' => 'web',
                'created_at' => '2023-06-18 03:25:06',
                'updated_at' => '2023-06-18 03:25:06',
            ),
            146 => 
            array (
                'id' => 726,
                'name' => 'delete legality-document',
                'group' => 'legality-document',
                'guard_name' => 'web',
                'created_at' => '2023-06-18 03:25:37',
                'updated_at' => '2023-06-18 03:25:37',
            ),
            147 => 
            array (
                'id' => 727,
                'name' => 'approve permission-letter-employee',
                'group' => 'permission-letter-employee',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            148 => 
            array (
                'id' => 728,
                'name' => 'reject permission-letter-employee',
                'group' => 'permission-letter-employee',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:35',
                'updated_at' => '2022-12-16 08:35:35',
            ),
            149 => 
            array (
                'id' => 729,
                'name' => 'approve leave',
                'group' => 'leave',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:34',
                'updated_at' => '2022-12-16 08:35:34',
            ),
            150 => 
            array (
                'id' => 730,
                'name' => 'reject leave',
                'group' => 'leave',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:34',
                'updated_at' => '2022-12-16 08:35:34',
            ),
            151 => 
            array (
                'id' => 731,
                'name' => 'edit model',
                'group' => 'model',
                'guard_name' => 'web',
                'created_at' => '2023-08-03 21:13:11',
                'updated_at' => '2023-08-03 21:13:11',
            ),
            152 => 
            array (
                'id' => 732,
                'name' => 'view model',
                'group' => 'model',
                'guard_name' => 'web',
                'created_at' => '2023-08-03 21:13:11',
                'updated_at' => '2023-08-03 21:13:11',
            ),
            153 => 
            array (
                'id' => 733,
                'name' => 'view closing-delivery-order-ship',
                'group' => 'closing-delivery-order-ship',
                'guard_name' => 'web',
                'created_at' => '2023-06-18 03:24:34',
                'updated_at' => '2023-06-18 03:24:34',
            ),
            154 => 
            array (
                'id' => 734,
                'name' => 'create closing-delivery-order-ship',
                'group' => 'closing-delivery-order-ship',
                'guard_name' => 'web',
                'created_at' => '2023-06-18 03:24:54',
                'updated_at' => '2023-06-18 03:24:54',
            ),
            155 => 
            array (
                'id' => 735,
                'name' => 'edit closing-delivery-order-ship',
                'group' => 'closing-delivery-order-ship',
                'guard_name' => 'web',
                'created_at' => '2023-06-18 03:25:06',
                'updated_at' => '2023-06-18 03:25:06',
            ),
            156 => 
            array (
                'id' => 736,
                'name' => 'delete closing-delivery-order-ship',
                'group' => 'closing-delivery-order-ship',
                'guard_name' => 'web',
                'created_at' => '2023-06-18 03:25:37',
                'updated_at' => '2023-06-18 03:25:37',
            ),
            157 => 
            array (
                'id' => 737,
                'name' => 'view salary-item',
                'group' => 'salary-item',
                'guard_name' => 'web',
                'created_at' => '2023-09-19 23:49:06',
                'updated_at' => '2023-09-19 23:49:06',
            ),
            158 => 
            array (
                'id' => 738,
                'name' => 'create salary-item',
                'group' => 'salary-item',
                'guard_name' => 'web',
                'created_at' => '2023-09-19 23:49:06',
                'updated_at' => '2023-09-19 23:49:06',
            ),
            159 => 
            array (
                'id' => 739,
                'name' => 'edit salary-item',
                'group' => 'salary-item',
                'guard_name' => 'web',
                'created_at' => '2023-09-19 23:49:06',
                'updated_at' => '2023-09-19 23:49:06',
            ),
            160 => 
            array (
                'id' => 740,
                'name' => 'delete salary-item',
                'group' => 'salary-item',
                'guard_name' => 'web',
                'created_at' => '2023-09-19 23:49:06',
                'updated_at' => '2023-09-19 23:49:06',
            ),
            161 => 
            array (
                'id' => 741,
                'name' => 'view income-tax',
                'group' => 'income-tax',
                'guard_name' => 'web',
                'created_at' => '2023-09-19 23:51:02',
                'updated_at' => '2023-09-19 23:51:02',
            ),
            162 => 
            array (
                'id' => 742,
                'name' => 'create income-tax',
                'group' => 'income-tax',
                'guard_name' => 'web',
                'created_at' => '2023-09-19 23:51:02',
                'updated_at' => '2023-09-19 23:51:02',
            ),
            163 => 
            array (
                'id' => 743,
                'name' => 'edit income-tax',
                'group' => 'income-tax',
                'guard_name' => 'web',
                'created_at' => '2023-09-19 23:51:02',
                'updated_at' => '2023-09-19 23:51:02',
            ),
            164 => 
            array (
                'id' => 744,
                'name' => 'delete income-tax',
                'group' => 'income-tax',
                'guard_name' => 'web',
                'created_at' => '2023-09-19 23:51:02',
                'updated_at' => '2023-09-19 23:51:02',
            ),
            165 => 
            array (
                'id' => 745,
                'name' => 'view non-taxable-income',
                'group' => 'non-taxable-income',
                'guard_name' => 'web',
                'created_at' => '2023-09-19 23:51:20',
                'updated_at' => '2023-09-19 23:51:20',
            ),
            166 => 
            array (
                'id' => 746,
                'name' => 'create non-taxable-income',
                'group' => 'non-taxable-income',
                'guard_name' => 'web',
                'created_at' => '2023-09-19 23:51:20',
                'updated_at' => '2023-09-19 23:51:20',
            ),
            167 => 
            array (
                'id' => 747,
                'name' => 'edit non-taxable-income',
                'group' => 'non-taxable-income',
                'guard_name' => 'web',
                'created_at' => '2023-09-19 23:51:20',
                'updated_at' => '2023-09-19 23:51:20',
            ),
            168 => 
            array (
                'id' => 748,
                'name' => 'delete non-taxable-income',
                'group' => 'non-taxable-income',
                'guard_name' => 'web',
                'created_at' => '2023-09-19 23:51:20',
                'updated_at' => '2023-09-19 23:51:20',
            ),
            169 => 
            array (
                'id' => 749,
                'name' => 'view business-field',
                'group' => 'business-field',
                'guard_name' => 'web',
                'created_at' => '2023-09-19 23:54:29',
                'updated_at' => '2023-09-19 23:54:29',
            ),
            170 => 
            array (
                'id' => 750,
                'name' => 'create business-field',
                'group' => 'business-field',
                'guard_name' => 'web',
                'created_at' => '2023-09-19 23:54:29',
                'updated_at' => '2023-09-19 23:54:29',
            ),
            171 => 
            array (
                'id' => 751,
                'name' => 'edit business-field',
                'group' => 'business-field',
                'guard_name' => 'web',
                'created_at' => '2023-09-19 23:54:29',
                'updated_at' => '2023-09-19 23:54:29',
            ),
            172 => 
            array (
                'id' => 752,
                'name' => 'delete business-field',
                'group' => 'business-field',
                'guard_name' => 'web',
                'created_at' => '2023-09-19 23:54:29',
                'updated_at' => '2023-09-19 23:54:29',
            ),
            173 => 
            array (
                'id' => 753,
                'name' => 'penjualan general report',
                'group' => 'report',
                'guard_name' => 'web',
                'created_at' => '2023-09-20 00:00:20',
                'updated_at' => '2023-09-20 00:00:20',
            ),
            174 => 
            array (
                'id' => 754,
                'name' => 'penjualan trading report',
                'group' => 'report',
                'guard_name' => 'web',
                'created_at' => '2023-09-20 00:00:20',
                'updated_at' => '2023-09-20 00:00:20',
            ),
            175 => 
            array (
                'id' => 755,
                'name' => 'purchase request report',
                'group' => 'report',
                'guard_name' => 'web',
                'created_at' => '2023-09-20 00:24:04',
                'updated_at' => '2023-09-20 00:24:04',
            ),
            176 => 
            array (
                'id' => 756,
                'name' => 'purchase order trading report',
                'group' => 'report',
                'guard_name' => 'web',
                'created_at' => '2023-09-20 00:24:04',
                'updated_at' => '2023-09-20 00:24:04',
            ),
            177 => 
            array (
                'id' => 757,
                'name' => 'purchase order general report',
                'group' => 'report',
                'guard_name' => 'web',
                'created_at' => '2023-09-20 00:24:04',
                'updated_at' => '2023-09-20 00:24:04',
            ),
            178 => 
            array (
                'id' => 758,
                'name' => 'purchase order service report',
                'group' => 'report',
                'guard_name' => 'web',
                'created_at' => '2023-09-20 00:24:04',
                'updated_at' => '2023-09-20 00:24:04',
            ),
            179 => 
            array (
                'id' => 759,
                'name' => 'purchase order transport report',
                'group' => 'report',
                'guard_name' => 'web',
                'created_at' => '2023-09-20 00:24:04',
                'updated_at' => '2023-09-20 00:24:04',
            ),
            180 => 
            array (
                'id' => 760,
                'name' => 'purchase order report',
                'group' => 'report',
                'guard_name' => 'web',
                'created_at' => '2023-09-20 00:24:04',
                'updated_at' => '2023-09-20 00:24:04',
            ),
            181 => 
            array (
                'id' => 761,
                'name' => 'warehouse report',
                'group' => 'report',
                'guard_name' => 'web',
                'created_at' => '2023-09-20 00:39:00',
                'updated_at' => '2023-09-20 00:39:00',
            ),
            182 => 
            array (
                'id' => 762,
                'name' => 'hrd report',
                'group' => 'report',
                'guard_name' => 'web',
                'created_at' => '2023-09-20 00:56:34',
                'updated_at' => '2023-09-20 00:56:34',
            ),
            183 => 
            array (
                'id' => 763,
                'name' => 'finance report',
                'group' => 'report',
                'guard_name' => 'web',
                'created_at' => '2023-09-20 00:56:34',
                'updated_at' => '2023-09-20 00:56:34',
            ),
            184 => 
            array (
                'id' => 764,
                'name' => 'bypass pairing sales-order',
                'group' => 'sales-order',
                'guard_name' => 'web',
                'created_at' => '2023-10-18 17:19:39',
                'updated_at' => '2023-10-18 17:19:39',
            ),
            185 => 
            array (
                'id' => 765,
                'name' => 'refresh stock-mutation',
                'group' => 'stock-mutation',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            186 => 
            array (
                'id' => 766,
                'name' => 'view purchase-request-trading',
                'group' => 'purchase-request-trading',
                'guard_name' => 'web',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            187 => 
            array (
                'id' => 767,
                'name' => 'create purchase-request-trading',
                'group' => 'purchase-request-trading',
                'guard_name' => 'web',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            188 => 
            array (
                'id' => 768,
                'name' => 'edit purchase-request-trading',
                'group' => 'purchase-request-trading',
                'guard_name' => 'web',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            189 => 
            array (
                'id' => 769,
                'name' => 'delete purchase-request-trading',
                'group' => 'purchase-request-trading',
                'guard_name' => 'web',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            190 => 
            array (
                'id' => 770,
                'name' => 'edit faktur pajak invoice-trading',
                'group' => 'invoice-trading',
                'guard_name' => 'web',
                'created_at' => '2024-01-17 23:44:44',
                'updated_at' => '2024-01-17 23:44:44',
            ),
            191 => 
            array (
                'id' => 771,
                'name' => 'view master-letter',
                'group' => 'master-letter',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            192 => 
            array (
                'id' => 772,
                'name' => 'create master-letter',
                'group' => 'master-letter',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            193 => 
            array (
                'id' => 773,
                'name' => 'edit master-letter',
                'group' => 'master-letter',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            194 => 
            array (
                'id' => 774,
                'name' => 'delete master-letter',
                'group' => 'master-letter',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:28',
                'updated_at' => '2022-12-16 08:35:28',
            ),
            195 => 
            array (
                'id' => 775,
                'name' => 'edit faktur pajak invoice-general',
                'group' => 'invoice-general',
                'guard_name' => 'web',
                'created_at' => '2023-02-09 19:55:16',
                'updated_at' => '2023-02-09 19:55:16',
            ),
            196 => 
            array (
                'id' => 777,
                'name' => 'void cash-advance-payment',
                'group' => 'cash-advance-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:51',
                'updated_at' => '2022-12-16 08:35:51',
            ),
            197 => 
            array (
                'id' => 778,
                'name' => 'void outgoing-payment',
                'group' => 'outgoing-payment',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:36:00',
                'updated_at' => '2022-12-16 08:36:00',
            ),
            198 => 
            array (
                'id' => 779,
                'name' => 'delete stock-usage',
                'group' => 'stock-usage',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            199 => 
            array (
                'id' => 780,
                'name' => 'edit stock-usage',
                'group' => 'stock-usage',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 08:35:32',
                'updated_at' => '2022-12-16 08:35:32',
            ),
            200 => 
            array (
                'id' => 781,
                'name' => 'delete mass-leave',
                'group' => 'mass-leave',
                'guard_name' => 'web',
                'created_at' => '2024-03-19 16:56:01',
                'updated_at' => '2024-03-19 16:56:01',
            ),
            201 => 
            array (
                'id' => 782,
                'name' => 'edit mass-leave',
                'group' => 'mass-leave',
                'guard_name' => 'web',
                'created_at' => '2024-03-19 16:56:01',
                'updated_at' => '2024-03-19 16:56:01',
            ),
            202 => 
            array (
                'id' => 783,
                'name' => 'create mass-leave',
                'group' => 'mass-leave',
                'guard_name' => 'web',
                'created_at' => '2024-03-19 16:56:01',
                'updated_at' => '2024-03-19 16:56:01',
            ),
            203 => 
            array (
                'id' => 784,
                'name' => 'view mass-leave',
                'group' => 'mass-leave',
                'guard_name' => 'web',
                'created_at' => '2024-03-19 16:56:01',
                'updated_at' => '2024-03-19 16:56:01',
            ),
            204 => 
            array (
                'id' => 785,
                'name' => 'view reset-leave',
                'group' => 'reset-leave',
                'guard_name' => 'web',
                'created_at' => '2024-03-18 21:24:38',
                'updated_at' => '2024-03-18 21:24:38',
            ),
            205 => 
            array (
                'id' => 786,
                'name' => 'delete reset-leave',
                'group' => 'reset-leave',
                'guard_name' => 'web',
                'created_at' => '2024-03-18 21:24:38',
                'updated_at' => '2024-03-18 21:24:38',
            ),
            206 => 
            array (
                'id' => 787,
                'name' => 'edit reset-leave',
                'group' => 'reset-leave',
                'guard_name' => 'web',
                'created_at' => '2024-03-18 21:24:38',
                'updated_at' => '2024-03-18 21:24:38',
            ),
            207 => 
            array (
                'id' => 788,
                'name' => 'create reset-leave',
                'group' => 'reset-leave',
                'guard_name' => 'web',
                'created_at' => '2024-03-18 21:24:38',
                'updated_at' => '2024-03-18 21:24:38',
            ),
        ));
        
        
    }
}