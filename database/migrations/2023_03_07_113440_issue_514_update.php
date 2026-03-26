<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Issue514Update extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_usage_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('item_receiving_report_id');
            $table->dropConstrainedForeignId('po_trading_id');
            $table->dropConstrainedForeignId('purchase_order_general_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
