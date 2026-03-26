<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DefaultCoasTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('default_coas')->delete();
        
        \DB::table('default_coas')->insert(array (
            0 => 
            array (
                'id' => 1,
                'coa_id' => NULL,
                'name' => 'Account Receivable Coa',
                'type' => 'customer',
                'created_at' => NULL,
                'updated_at' => '2023-09-17 11:26:01',
            ),
            1 => 
            array (
                'id' => 2,
                'coa_id' => NULL,
                'name' => 'Sale Discounts Coa',
                'type' => 'customer',
                'created_at' => NULL,
                'updated_at' => '2023-09-17 11:26:01',
            ),
            2 => 
            array (
                'id' => 3,
                'coa_id' => NULL,
                'name' => 'Customer Deposite Coa',
                'type' => 'customer',
                'created_at' => NULL,
                'updated_at' => '2023-09-17 11:26:01',
            ),
            3 => 
            array (
                'id' => 4,
                'coa_id' => NULL,
                'name' => 'Account Payable Coa',
                'type' => 'vendor',
                'created_at' => NULL,
                'updated_at' => '2023-09-17 11:26:01',
            ),
            4 => 
            array (
                'id' => 5,
                'coa_id' => NULL,
                'name' => 'Purchase Discounts Coa',
                'type' => 'vendor',
                'created_at' => NULL,
                'updated_at' => '2023-09-17 11:26:01',
            ),
            5 => 
            array (
                'id' => 6,
                'coa_id' => NULL,
                'name' => 'Vendor Deposite Coa',
                'type' => 'vendor',
                'created_at' => NULL,
                'updated_at' => '2023-09-17 11:26:01',
            ),
            6 => 
            array (
                'id' => 7,
                'coa_id' => NULL,
                'name' => 'Exchange Rate Gap',
                'type' => 'finance',
                'created_at' => NULL,
                'updated_at' => '2023-09-17 11:26:01',
            ),
            7 => 
            array (
                'id' => 8,
                'coa_id' => NULL,
                'name' => 'Selisih Bayar',
                'type' => 'finance',
                'created_at' => NULL,
                'updated_at' => '2023-09-17 11:26:01',
            ),
            8 => 
            array (
                'id' => 9,
                'coa_id' => NULL,
                'name' => 'Invoice Return',
                'type' => 'finance',
                'created_at' => NULL,
                'updated_at' => '2023-09-17 11:26:01',
            ),
            9 => 
            array (
                'id' => 10,
                'coa_id' => NULL,
                'name' => 'Losess DO General',
                'type' => 'finance',
                'created_at' => NULL,
                'updated_at' => '2023-09-17 11:26:01',
            ),
        ));
        
        
    }
}