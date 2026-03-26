<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpaceAvailableToSoTradingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('so_trading_details', function (Blueprint $table) {
            $table->integer('sudah_dialokasikan')->after('jumlah')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('so_trading_details', function (Blueprint $table) {
            $table->dropColumn('sudah_dialokasikan');
        });
    }
}
