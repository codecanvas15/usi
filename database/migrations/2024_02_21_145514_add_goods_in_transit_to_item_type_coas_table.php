<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AddGoodsInTransitToItemTypeCoasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transit_to_item_type_coas', function (Blueprint $table) {
            try {
                DB::table('item_type_coas')
                    ->insert([
                        [
                            'item_type_id' => 1,
                            'coa_id' => null,
                            'type' => 'goods_in_transit',
                        ]
                    ]);
            } catch (\Throwable $th) {
                Log::info($th);
            }

            $item_categories = DB::table('item_categories')
                ->where('item_type_id', 1)
                ->get();

            foreach ($item_categories as $key => $item_category) {
                DB::table('item_category_coas')
                    ->insert([
                        'item_category_id' => $item_category->id,
                        'coa_id' => null,
                        'type' => 'goods_in_transit',
                    ]);

                DB::table('item_categories')
                    ->where('id', $item_category->id)
                    ->update(
                        [
                            'is_complete' => 0
                        ]
                    );
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transit_to_item_type_coas', function (Blueprint $table) {
            //
        });
    }
}
