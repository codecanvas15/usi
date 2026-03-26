<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUnitColInPoTradingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('po_trading_details', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        Schema::table('po_trading_details', function (Blueprint $table) {
            $table->enum('type', ['Liter', 'Kilo Liter'])->after('jumlah');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('po_trading_details', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('po_trading_details', function (Blueprint $table) {
            $table->enum('type', ['L', 'KL'])->after('jumlah');
        });
    }
}
