<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDeliveryOrders3Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropColumn('tangga_berlaku');

            $table->date('tanggal_mulai')->nullable()->after('nomor_do');
            $table->date('tanggal_berakhir')->nullable()->after('tanggal_mulai');
            $table->boolean('status_cetak')->default(false);
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
            $table->date('tangga_berlaku')->nullable();

            $table->dropColumn('tanggal_mulai');
            $table->dropColumn('tanggal_berakhir');
        });
    }
}
