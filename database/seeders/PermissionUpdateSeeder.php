<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->whereIn('id', [764])->delete();

        $permissions = [
            0 => [
                'id' => 764,
                'name' => 'edit stock-transfer',
                'group' => 'stock-transfer',
                'guard_name' => 'web',
                'created_at' => '2022-12-16 01:35:32',
                'updated_at' => '2022-12-16 01:35:32',
            ],
        ];

        DB::table('permissions')->insert($permissions);
    }
}
