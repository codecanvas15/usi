<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDiscountToSaleOrderGeneralDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_order_general_details', function (Blueprint $table) {
            $table->decimal('price_before_discount', 18, 2)->after('purchase_order_general_id')->default(0);
            $table->decimal('discount', 18, 2)->after('price_before_discount')->default(0);
        });

        $sale_order_general_details = DB::table('sale_order_general_details')->get();

        foreach ($sale_order_general_details as $purchase_order_general_detail_item) {
            DB::table('sale_order_general_details')->where('id', $purchase_order_general_detail_item->id)->update([
                'price_before_discount' => $purchase_order_general_detail_item->price,
                'discount' => 0
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_order_general_details', function (Blueprint $table) {
            $table->dropColumn(['price_before_discount', 'discount']);
        });
    }
}
