<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExchangeRateToCurrencies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->integer('exchange_rate')->nullable()->after('negara');
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->dropColumn('exchange_rate');
            //
        });
    }
}
