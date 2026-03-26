<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProfitLossCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('profit_loss_categories')->delete();
        
        \DB::table('profit_loss_categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'laba-kotor',
                'deleted_at' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'laba-usaha',
                'deleted_at' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'laba-bersih',
                'deleted_at' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}