<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Issue157 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_transports', function (Blueprint $table) {
            $table->string('type', 50)->nullable()->after('status');
            $table->string('type_delivery', 50)->nullable()->after('type');
            $table->unsignedBigInteger('delivery_order_ship_id')->nullable()->after('so_trading_id');
            $table->unsignedBigInteger('delivery_order_id')->nullable()->after('delivery_order_ship_id');

            $table->foreign('delivery_order_ship_id')->references('id')->on('delivery_order_ships');
            $table->foreign('delivery_order_id')->references('id')->on('delivery_orders');
        });

        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('delivery_order_ship_id')->nullable()->after('ware_house_id');
            $table->unsignedBigInteger('delivery_order_id')->nullable()->after('delivery_order_ship_id');
            $table->boolean('is_double_handling')->default(false)->after('vehicle_information');

            $table->foreign('delivery_order_ship_id')->references('id')->on('delivery_order_ships');
            $table->foreign('delivery_order_id')->references('id')->on('delivery_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
