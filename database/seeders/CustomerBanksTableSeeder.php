<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CustomerBanksTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('customer_banks')->delete();
        
        
        
    }
}