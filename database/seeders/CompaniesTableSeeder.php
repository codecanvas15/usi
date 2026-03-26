<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('companies')->delete();
        
        \DB::table('companies')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'PT UNITED SHIPPING INDONESIA',
                'short_name' => 'USI',
                'address' => 'Jl. Gondosuli no. 8 Surabaya.',
                'phone' => '08155110111',
                'fax' => '08155110111',
                'email' => 'info@ptusi.co.id',
                'website' => 'www.ptusi.co.id',
                'logo' => 'company/company.jpg',
                'secondary_logo' => 'company/AJoNK1JBgJtoR4hC0FXoFBMJtaVAC56IPPLaJ0ah.png',
                'npwp' => NULL,
                'created_at' => '2023-12-08 02:37:05',
                'updated_at' => '2024-01-10 18:06:54',
            ),
        ));
        
        
    }
}