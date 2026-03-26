<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferenceToInvoiceTradingCoasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_trading_coas', function (Blueprint $table) {
            $table->string('reference_model')->nullable()->after('type');
            $table->unsignedBigInteger('reference_id')->nullable()->after('reference_model');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_trading_coas', function (Blueprint $table) {
            $table->dropColumn('reference_model');
            $table->dropColumn('reference_id');
        });
    }
}
