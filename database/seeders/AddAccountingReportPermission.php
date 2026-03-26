<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AddAccountingReportPermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::updateOrCreate([
            'name' => "accounting report",
        ], [
            'name' => "accounting report",
            'group' => "report",
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'name' => "cashier report",
        ], [
            'name' => "cashier report",
            'group' => "report",
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'name' => "cant-see-other purchase-request",
        ], [
            'name' => "cant-see-other purchase-request",
            'group' => "purchase-request",
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'name' => "cant-see-other purchase-request-service",
        ], [
            'name' => "cant-see-other purchase-request-service",
            'group' => "purchase-request-service",
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'name' => "upload-document stock-usage",
        ], [
            'name' => "upload-document stock-usage",
            'group' => "stock-usage",
            'guard_name' => 'web',
        ]);

        Permission::updateOrCreate([
            'name' => "close stock-usage",
        ], [
            'name' => "close stock-usage",
            'group' => "stock-usage",
            'guard_name' => 'web',
        ]);
    }
}
