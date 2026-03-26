<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateInvoiceTradingTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_trading_taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_trading_id');
            $table->foreign('invoice_trading_id')->references('id')->on('invoice_tradings');
            $table->unsignedBigInteger('tax_id');
            $table->foreign('tax_id')->references('id')->on('taxes');
            $table->decimal('value', 18, 4);
            $table->decimal('amount', 18, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_trading_taxes');
    }
}
