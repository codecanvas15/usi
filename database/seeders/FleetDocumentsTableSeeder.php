<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FleetDocumentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('fleet_documents')->delete();
        
        
        
    }
}