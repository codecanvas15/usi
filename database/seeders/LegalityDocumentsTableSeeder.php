<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LegalityDocumentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('legality_documents')->delete();
        
        \DB::table('legality_documents')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type' => 'company',
                'name' => 'NPWP USI',
                'transaction_date' => '2023-06-28',
                'effective_date' => '2030-12-31',
                'end_date' => '2030-12-31',
                'due_date' => 30,
                'description' => 'NPWP USI GONDOSULI SURABAYA',
                'file' => 'legality-document/xYbq68iT1nFpsiAY4tRXRFwVm9j9tFLj8z0db2bA.pdf',
                'deleted_at' => NULL,
                'created_at' => '2023-06-28 14:41:53',
                'updated_at' => '2023-06-28 14:41:53',
            ),
            1 => 
            array (
                'id' => 2,
                'type' => 'company',
                'name' => 'DOMISILI GONDOSULI',
                'transaction_date' => '2022-07-07',
                'effective_date' => '2023-07-07',
                'end_date' => '2023-07-07',
                'due_date' => 7,
                'description' => 'DOMISILI GONDOSULI EXP 7 JULI 2023',
                'file' => 'legality-document/s6Smr8qNS6Jrf3MUxLcMvsdNh8W5EJKiul1ubW09.pdf',
                'deleted_at' => NULL,
                'created_at' => '2023-06-28 15:59:54',
                'updated_at' => '2023-06-28 15:59:54',
            ),
        ));
        
        
    }
}