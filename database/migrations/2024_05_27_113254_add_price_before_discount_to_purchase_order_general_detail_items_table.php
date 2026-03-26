<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPriceBeforeDiscountToPurchaseOrderGeneralDetailItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_general_detail_items', function (Blueprint $table) {
            $table->decimal('price_before_discount', 18, 2)->after('quantity_received')->default(0);
            $table->decimal('discount', 18, 2)->after('price_before_discount')->default(0);
        });

        $purchase_order_general_detail_items = DB::table('purchase_order_general_detail_items')->get();

        foreach ($purchase_order_general_detail_items as $purchase_order_general_detail_item) {
            DB::table('purchase_order_general_detail_items')->where('id', $purchase_order_general_detail_item->id)->update([
                'price_before_discount' => $purchase_order_general_detail_item->price,
                'discount' => 0
            ]);
        }

        Schema::table('purchase_order_service_detail_items', function (Blueprint $table) {
            $table->decimal('price_before_discount', 18, 2)->after('quantity_received')->default(0);
            $table->decimal('discount', 18, 2)->after('price_before_discount')->default(0);
        });

        $purchase_order_service_detail_items = DB::table('purchase_order_service_detail_items')->get();

        foreach ($purchase_order_service_detail_items as $purchase_order_service_detail_item) {
            DB::table('purchase_order_service_detail_items')->where('id', $purchase_order_service_detail_item->id)->update([
                'price_before_discount' => $purchase_order_service_detail_item->price,
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
        Schema::table('purchase_order_general_detail_items', function (Blueprint $table) {
            $table->dropColumn(['price_before_discount', 'discount']);
        });

        Schema::table('purchase_order_service_detail_items', function (Blueprint $table) {
            $table->dropColumn(['price_before_discount', 'discount']);
        });
    }
}
