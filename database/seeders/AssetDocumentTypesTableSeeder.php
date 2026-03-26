<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AssetDocumentTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('asset_document_types')->delete();
        
        \DB::table('asset_document_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Tanah',
                'deleted_at' => NULL,
                'created_at' => NULL,
                'updated_at' => '2023-09-28 16:45:10',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Bangunan',
                'deleted_at' => NULL,
                'created_at' => NULL,
                'updated_at' => '2023-09-28 16:45:16',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Kendaraan Komersial',
                'deleted_at' => NULL,
                'created_at' => NULL,
                'updated_at' => '2023-09-28 16:44:41',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Kapal',
                'deleted_at' => NULL,
                'created_at' => NULL,
                'updated_at' => '2023-09-28 16:45:33',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Kendaraan Kantor',
                'deleted_at' => NULL,
                'created_at' => '2023-09-28 16:44:51',
                'updated_at' => '2023-09-28 16:46:46',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Inventaris Kantor',
                'deleted_at' => NULL,
                'created_at' => '2023-09-28 16:46:16',
                'updated_at' => '2023-09-28 16:46:16',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Inventaris Operasional',
                'deleted_at' => NULL,
                'created_at' => '2023-09-28 16:46:32',
                'updated_at' => '2023-09-28 16:46:32',
            ),
        ));
        
        
    }
}