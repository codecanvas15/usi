<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TaxTradingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('tax_tradings')->delete();
        
        \DB::table('tax_tradings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'PPN',
                'value' => '0.1100',
                'coa_sale_id' => NULL,
                'coa_purchase_id' => NULL,
                'type' => 'ppn',
                'deleted_at' => NULL,
                'created_at' => '2023-01-27 22:10:21',
                'updated_at' => '2023-09-17 18:37:46',
            ),
        ));
        
        
    }
}