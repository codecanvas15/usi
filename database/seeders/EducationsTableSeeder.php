<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EducationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('educations')->delete();
        
        \DB::table('educations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'SMA',
                'deleted_at' => NULL,
                'created_at' => '2023-02-07 16:35:47',
                'updated_at' => '2023-02-07 16:35:47',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'SMK',
                'deleted_at' => NULL,
                'created_at' => '2023-02-07 16:35:47',
                'updated_at' => '2023-02-07 16:35:47',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'D3',
                'deleted_at' => NULL,
                'created_at' => '2023-02-07 16:35:47',
                'updated_at' => '2023-02-07 16:35:47',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'S1',
                'deleted_at' => NULL,
                'created_at' => '2023-02-07 16:35:47',
                'updated_at' => '2023-02-07 16:35:47',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'S2',
                'deleted_at' => NULL,
                'created_at' => '2023-02-07 16:35:47',
                'updated_at' => '2023-02-07 16:35:47',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'S3',
                'deleted_at' => NULL,
                'created_at' => '2023-02-07 16:35:47',
                'updated_at' => '2023-02-07 16:35:47',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'tidak ada',
                'deleted_at' => NULL,
                'created_at' => '2023-08-23 00:05:35',
                'updated_at' => '2023-08-23 00:05:35',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'belum',
                'deleted_at' => NULL,
                'created_at' => '2023-08-23 23:41:19',
                'updated_at' => '2023-08-23 23:41:19',
            ),
        ));
        
        
    }
}