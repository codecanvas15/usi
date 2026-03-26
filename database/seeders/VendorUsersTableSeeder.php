<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class VendorUsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('vendor_users')->delete();
        
        
        
    }
}