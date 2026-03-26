<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUnitColInSoTradingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('so_trading_details', function (Blueprint $table) {
            $table->dropColumn('unit');
            $table->enum('type', ['Liter', 'Kilo Liter'])->default('Liter')->after('jumlah');
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
            $table->dropColumn('type');
            $table->enum('unit', ['Liter', 'Kilo Liter'])->after('jumlah');
        });
    }
}
