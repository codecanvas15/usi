<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LeasesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('leases')->delete();
        
        
        
    }
}