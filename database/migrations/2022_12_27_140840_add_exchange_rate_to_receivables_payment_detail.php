<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExchangeRateToReceivablesPaymentDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receivables_payment_details', function (Blueprint $table) {
            $table->decimal('exchange_rate', 18, 3)->after('invoice_trading_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receivables_payment_details', function (Blueprint $table) {
            $table->dropColumn('exchange_rate');
        });
    }
}
