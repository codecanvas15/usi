<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IncomeTaxesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('income_taxes')->delete();
        
        \DB::table('income_taxes')->insert(array (
            0 => 
            array (
                'id' => 1,
                'min' => '0.00',
                'max' => '60000000.00',
                'percentage' => '5.00',
                'deleted_at' => NULL,
                'created_at' => '2023-08-17 02:52:56',
                'updated_at' => '2023-08-19 13:13:49',
            ),
            1 => 
            array (
                'id' => 2,
                'min' => '60000000.00',
                'max' => '250000000.00',
                'percentage' => '15.00',
                'deleted_at' => NULL,
                'created_at' => '2023-08-17 02:57:14',
                'updated_at' => '2023-08-19 13:15:29',
            ),
            2 => 
            array (
                'id' => 3,
                'min' => '250000000.00',
                'max' => '500000000.00',
                'percentage' => '25.00',
                'deleted_at' => NULL,
                'created_at' => '2023-08-17 02:57:35',
                'updated_at' => '2023-08-17 02:57:35',
            ),
            3 => 
            array (
                'id' => 4,
                'min' => '500000000.00',
                'max' => '5000000000.00',
                'percentage' => '30.00',
                'deleted_at' => NULL,
                'created_at' => '2023-08-17 02:58:00',
                'updated_at' => '2023-08-19 13:16:13',
            ),
            4 => 
            array (
                'id' => 5,
                'min' => '5000000000.00',
                'max' => '0.00',
                'percentage' => '35.00',
                'deleted_at' => NULL,
                'created_at' => '2023-08-19 13:16:55',
                'updated_at' => '2023-08-19 13:38:57',
            ),
        ));
        
        
    }
}