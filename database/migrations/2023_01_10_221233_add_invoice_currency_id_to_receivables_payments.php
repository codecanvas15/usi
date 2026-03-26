<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceCurrencyIdToReceivablesPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receivables_payments', function (Blueprint $table) {
            $table->foreignId('invoice_currency_id')->after('currency_id')->constrained('currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receivables_payments', function (Blueprint $table) {
            $table->dropColumn('invoice_currency_id');
        });
    }
}
