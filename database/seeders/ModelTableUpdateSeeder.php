<?php

namespace Database\Seeders;

use App\Models\LeaveChangeFile;
use App\Models\ModelAuthorization;
use App\Models\ModelTable;
use App\Models\Project;
use App\Models\PurchaseRequest;
use Illuminate\Database\Seeder;

class ModelTableUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ModelTable::updateOrCreate([
            'name' => LeaveChangeFile::class
        ], [
            'name' => LeaveChangeFile::class,
            'alias' => 'perubahan-file-cuti',
            'group' => 'hrd',
            'need_to_check_amount' => 0,
        ]);

        ModelTable::updateOrCreate([
            'name' => PurchaseRequest::class
        ], [
            'name' => PurchaseRequest::class,
            'alias' => 'purchase-request-general',
            'group' => 'pembelian',
            'need_to_check_amount' => 0,
            'type' => 'general',
        ]);

        ModelTable::updateOrCreate([
            'name' => PurchaseRequest::class,
            'type' => 'jasa'
        ], [
            'name' => PurchaseRequest::class,
            'alias' => 'purchase-request-service',
            'group' => 'pembelian',
            'need_to_check_amount' => 0,
            'type' => 'jasa',
        ]);

        ModelTable::updateOrCreate([
            'name' => Project::class,
            'type' => 'project'
        ], [
            'name' => Project::class,
            'alias' => 'project',
            'group' => 'project',
            'need_to_check_amount' => 0,
        ]);
    }
}
