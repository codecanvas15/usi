<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModelHasRolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('model_has_roles')->delete();
        
        \DB::table('model_has_roles')->insert(array (
            0 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 1,
            ),
            1 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 10,
            ),
            2 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 11,
            ),
            3 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 12,
            ),
            4 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 13,
            ),
            5 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 98,
            ),
            6 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 99,
            ),
            7 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 100,
            ),
            8 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 101,
            ),
            9 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 102,
            ),
            10 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 110,
            ),
            11 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 118,
            ),
            12 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 119,
            ),
            13 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 120,
            ),
            14 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 121,
            ),
            15 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 122,
            ),
            16 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 133,
            ),
            17 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 134,
            ),
            18 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 143,
            ),
            19 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 145,
            ),
            20 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 147,
            ),
            21 => 
            array (
                'role_id' => 10,
                'model_type' => 'App\\Models\\User',
                'model_id' => 150,
            ),
            22 => 
            array (
                'role_id' => 35,
                'model_type' => 'App\\Models\\User',
                'model_id' => 149,
            ),
            23 => 
            array (
                'role_id' => 35,
                'model_type' => 'App\\Models\\User',
                'model_id' => 155,
            ),
            24 => 
            array (
                'role_id' => 36,
                'model_type' => 'App\\Models\\User',
                'model_id' => 137,
            ),
            25 => 
            array (
                'role_id' => 38,
                'model_type' => 'App\\Models\\User',
                'model_id' => 141,
            ),
            26 => 
            array (
                'role_id' => 38,
                'model_type' => 'App\\Models\\User',
                'model_id' => 142,
            ),
            27 => 
            array (
                'role_id' => 38,
                'model_type' => 'App\\Models\\User',
                'model_id' => 150,
            ),
            28 => 
            array (
                'role_id' => 40,
                'model_type' => 'App\\Models\\User',
                'model_id' => 138,
            ),
            29 => 
            array (
                'role_id' => 41,
                'model_type' => 'App\\Models\\User',
                'model_id' => 140,
            ),
            30 => 
            array (
                'role_id' => 42,
                'model_type' => 'App\\Models\\User',
                'model_id' => 148,
            ),
            31 => 
            array (
                'role_id' => 42,
                'model_type' => 'App\\Models\\User',
                'model_id' => 151,
            ),
            32 => 
            array (
                'role_id' => 42,
                'model_type' => 'App\\Models\\User',
                'model_id' => 153,
            ),
            33 => 
            array (
                'role_id' => 42,
                'model_type' => 'App\\Models\\User',
                'model_id' => 154,
            ),
            34 => 
            array (
                'role_id' => 43,
                'model_type' => 'App\\Models\\User',
                'model_id' => 139,
            ),
            35 => 
            array (
                'role_id' => 43,
                'model_type' => 'App\\Models\\User',
                'model_id' => 145,
            ),
            36 => 
            array (
                'role_id' => 43,
                'model_type' => 'App\\Models\\User',
                'model_id' => 147,
            ),
            37 => 
            array (
                'role_id' => 44,
                'model_type' => 'App\\Models\\User',
                'model_id' => 136,
            ),
            38 => 
            array (
                'role_id' => 44,
                'model_type' => 'App\\Models\\User',
                'model_id' => 137,
            ),
            39 => 
            array (
                'role_id' => 45,
                'model_type' => 'App\\Models\\User',
                'model_id' => 152,
            ),
            40 => 
            array (
                'role_id' => 46,
                'model_type' => 'App\\Models\\User',
                'model_id' => 135,
            ),
            41 => 
            array (
                'role_id' => 46,
                'model_type' => 'App\\Models\\User',
                'model_id' => 144,
            ),
            42 => 
            array (
                'role_id' => 46,
                'model_type' => 'App\\Models\\User',
                'model_id' => 146,
            ),
        ));
        
        
    }
}