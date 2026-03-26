<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CurrenciesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('currencies')->delete();
        
        \DB::table('currencies')->insert(array (
            0 => 
            array (
                'id' => 105,
                'kode' => 'IDR',
                'simbol' => 'Rp',
                'nama' => 'Rupiah',
                'remark' => 'Indonesia',
                'negara' => 'Indonesia',
                'exchange_rate' => NULL,
                'active' => '1',
                'is_local' => 1,
                'deleted_at' => NULL,
                'created_at' => '2022-12-16 09:05:35',
                'updated_at' => '2022-12-16 09:05:35',
            ),
            1 => 
            array (
                'id' => 239,
                'kode' => 'USD',
                'simbol' => '$',
                'nama' => 'United States dollar',
                'remark' => 'United States Of America',
                'negara' => 'United States of America',
                'exchange_rate' => NULL,
                'active' => '1',
                'is_local' => 0,
                'deleted_at' => NULL,
                'created_at' => '2022-12-16 09:05:36',
                'updated_at' => '2022-12-16 09:05:36',
            ),
        ));
        
        
    }
}