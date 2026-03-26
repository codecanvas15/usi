<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropWarehouseInDeliveryOrderGeneralsDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_order_general_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ware_house_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_order_general_details', function (Blueprint $table) {
            //
        });
    }
}
