<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemReceivingReportToDeliveryOrderShip extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_order_ships', function (Blueprint $table) {
            $table->boolean('is_item_receiving_report_created')->default(false)->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_order_ships', function (Blueprint $table) {
            //
        });
    }
}
