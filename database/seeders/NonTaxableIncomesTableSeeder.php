<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class NonTaxableIncomesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('non_taxable_incomes')->delete();
        
        \DB::table('non_taxable_incomes')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'TK/0',
                'note' => 'Tidak Kawin',
                'amount' => '54000000.00',
                'deleted_at' => NULL,
                'created_at' => '2023-08-17 21:02:46',
                'updated_at' => '2023-08-17 21:02:46',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'K/0',
                'note' => 'Kawin 0 Anak',
                'amount' => '58500000.00',
                'deleted_at' => NULL,
                'created_at' => '2023-08-17 21:03:37',
                'updated_at' => '2023-08-24 00:30:33',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'K/1',
                'note' => 'Kawin 1 Anak',
                'amount' => '63000000.00',
                'deleted_at' => NULL,
                'created_at' => '2023-08-17 21:04:04',
                'updated_at' => '2023-08-24 00:31:13',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'K/2',
                'note' => 'Kawin 2 Anak',
                'amount' => '67500000.00',
                'deleted_at' => NULL,
                'created_at' => '2023-08-17 21:04:22',
                'updated_at' => '2023-08-24 00:31:25',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'K/3',
                'note' => 'Kawin 3 Anak',
                'amount' => '72000000.00',
                'deleted_at' => NULL,
                'created_at' => '2023-08-17 21:04:39',
                'updated_at' => '2023-08-24 00:31:36',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'TK/1',
                'note' => 'Tidak kawin, 1 tanggungan',
                'amount' => '58500000.00',
                'deleted_at' => NULL,
                'created_at' => '2023-08-24 00:32:16',
                'updated_at' => '2023-08-24 00:32:16',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'TK/2',
                'note' => 'Tidak kawin, 2 tanggungan',
                'amount' => '63000000.00',
                'deleted_at' => NULL,
                'created_at' => '2023-08-24 00:32:44',
                'updated_at' => '2023-08-24 00:32:44',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'TK/3',
                'note' => 'Tidak kawin, 3 tanggungan',
                'amount' => '67500000.00',
                'deleted_at' => NULL,
                'created_at' => '2023-08-24 00:33:08',
                'updated_at' => '2023-08-24 00:33:08',
            ),
        ));
        
        
    }
}