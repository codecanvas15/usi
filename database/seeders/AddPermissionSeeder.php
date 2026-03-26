<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AddPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arr = [
            'stock-adjustment' => [
                'edit stock-adjustment',
                'delete stock-adjustment',
            ],
            'stock-transfer' => [
                'edit stock-transfer',
                'delete stock-transfer',
            ],
            'purchase-order' => [
                'close purchase-order'
            ],
            'stock-adjustment' => [
                'edit-hpp stock-adjustment'
            ],

        ];

        foreach ($arr as $group => $permissions) {
            foreach ($permissions as $perm) {
                Permission::updateOrCreate([
                    'group' => $group,
                    'name' => $perm,
                ], [
                    'group' => $group,
                    'name' => $perm,
                    'guard_name' => 'web',
                ]);
            }
        }
    }
}
