<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (file_exists(storage_path('app/public/company/')) && is_dir(storage_path('app/public/company/'))) {
            array_map('unlink', glob(storage_path('app/public/company/') . '/*.*'));
            rmdir(storage_path('app/public/company/'));
        }

        mkdir(storage_path('app/public/company/'));
        File::copy(public_path('/images/icon.png'), storage_path('app/public/company/company.jpg'));

        Company::create([
            'name' => Str::upper(config('app.name', 'United Shipping Indonesia')),
            'address' => get_primary_branch()->address,
            'phone' => get_primary_branch()->phone,
            'fax' => get_primary_branch()->phone,
            'logo' => 'company/company.jpg',
        ]);
    }
}
