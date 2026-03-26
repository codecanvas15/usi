<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropTanggalAtDeliveryOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropColumn('tanggal');
            $table->string('nomor_do', 24)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->date('tanggal');
        });
    }
}
