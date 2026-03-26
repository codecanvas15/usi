<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MasterUserAssessmentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('master_user_assessments')->delete();
        
        \DB::table('master_user_assessments')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Kompetensi Dasar',
                'weight' => 0.03,
                'type' => 'key skill competencies',
                'deleted_at' => NULL,
                'created_at' => '2023-06-20 23:28:45',
                'updated_at' => '2023-06-20 23:28:45',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Behavioural',
                'weight' => 0.05,
                'type' => 'key behavioral competencies',
                'deleted_at' => NULL,
                'created_at' => '2023-06-20 23:29:36',
                'updated_at' => '2023-06-20 23:29:36',
            ),
        ));
        
        
    }
}