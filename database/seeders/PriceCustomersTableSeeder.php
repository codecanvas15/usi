<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PriceCustomersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('price_customers')->delete();
        
        
        
    }
}