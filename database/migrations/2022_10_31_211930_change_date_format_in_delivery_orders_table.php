<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDateFormatInDeliveryOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropColumn('tanggal_mulai');
            $table->dropColumn('tanggal_berakhir');

            $table->date('tanggal_muat')->nullable()->after('nomor_do');
            $table->date('tanggal_bongkar')->nullable()->after('tanggal_muat');
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
            $table->date('tanggal_mulai')->nullable()->after('nomor_do');
            $table->date('tanggal_berakhir')->nullable()->after('tanggal_mulai');

            $table->dropColumn('tanggal_muat');
            $table->dropColumn('tanggal_bongkar');
        });
    }
}
