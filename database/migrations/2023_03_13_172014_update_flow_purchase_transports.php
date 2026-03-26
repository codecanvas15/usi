<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFlowPurchaseTransports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('delivery_order_id');
            $table->dropConstrainedForeignId('delivery_order_ship_id');
        });

        Schema::table('purchase_transports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('delivery_order_id');
            $table->dropConstrainedForeignId('delivery_order_ship_id');

            $table->dropColumn(['type_delivery']);
        });

        Schema::dropIfExists('delivery_order_ships');
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
