<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItemCategoryCoasTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('item_category_coas')->delete();
    }
}
