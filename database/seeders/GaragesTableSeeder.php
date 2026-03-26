<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GaragesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('garages')->delete();
        
        
        
    }
}