<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateInvoiceTradingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_trading_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_trading_id');
            $table->foreign('invoice_trading_id')->references('id')->on('invoice_tradings');
            $table->unsignedBigInteger('delivery_order_id');
            $table->foreign('delivery_order_id')->references('id')->on('delivery_orders');
            $table->decimal('jumlah_dikirim', 18, 3);
            $table->decimal('jumlah_diterima', 18, 3);
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
        Schema::dropIfExists('invoice_trading_details');
    }
}
