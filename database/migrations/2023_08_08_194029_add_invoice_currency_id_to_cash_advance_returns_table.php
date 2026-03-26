<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceCurrencyIdToCashAdvanceReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_advanced_returns', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_currency_id')->nullable()->after('currency_id');
            $table->foreign('invoice_currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });

        Schema::table('cash_advanced_return_invoices', function (Blueprint $table) {
            $table->decimal('amount_to_paid_or_return_convert', 18, 2)->nullable()->after('amount_to_paid_or_return');
        });

        Schema::table('cash_advance_return_invoice_details', function (Blueprint $table) {
            $table->decimal('amount_convert', 18, 2)->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_advanced_returns', function (Blueprint $table) {
            $table->dropForeign('cash_advanced_returns_invoice_currency_id_foreign');
            $table->dropColumn('invoice_currency_id');
        });

        Schema::table('cash_advanced_return_invoices', function (Blueprint $table) {
            $table->dropColumn('amount_to_paid_or_return_convert');
        });

        Schema::table('cash_advance_return_invoice_details', function (Blueprint $table) {
            $table->dropColumn('amount_convert');
        });
    }
}
