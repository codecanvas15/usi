<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePurchaseOrderTaxTaxTradingForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_taxes', function (Blueprint $table) {
            $table->dropForeign('purchase_order_taxes_tax_trading_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_taxes', function (Blueprint $table) {
            //
        });
    }
}
