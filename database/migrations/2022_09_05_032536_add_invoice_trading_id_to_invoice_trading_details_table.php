<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceTradingIdToInvoiceTradingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_trading_details', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\InvoiceTrading::class)->constrained('invoice_tradings')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_trading_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('invoice_trading_id');
        });
    }
}
