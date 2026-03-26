<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDeliveryOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_order_details', function (Blueprint $table) {
            $table->string('segel_atas')->nullable()->change();
            $table->string('segel_bawah')->nullable()->change();
            $table->string('temperatur')->nullable()->change();
            $table->string('meter_awal')->nullable()->change();
            $table->string('meter_akhir')->nullable()->change();
            $table->string('sg_meter')->nullable()->change();
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
