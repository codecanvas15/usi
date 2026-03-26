<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProfitLossSubcategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('profit_loss_subcategories')->delete();
        
        \DB::table('profit_loss_subcategories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'profit_loss_category_id' => 1,
                'name' => 'pendapatan',
                'type' => 'plus',
                'deleted_at' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'profit_loss_category_id' => 1,
                'name' => 'harga-pokok-penjualan',
                'type' => 'minus',
                'deleted_at' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'profit_loss_category_id' => 2,
                'name' => 'biaya-operasional',
                'type' => 'minus',
                'deleted_at' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'profit_loss_category_id' => 3,
                'name' => 'biaya-diluar-usaha',
                'type' => 'minus',
                'deleted_at' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'profit_loss_category_id' => 3,
                'name' => 'pendapatan-diluar-usaha',
                'type' => 'plus',
                'deleted_at' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}