<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitToSoTradingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('so_trading_details', function (Blueprint $table) {
            $table->enum('unit', ['Liter', 'Kilo Liter'])->after('jumlah');
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
            //
        });
    }
}
