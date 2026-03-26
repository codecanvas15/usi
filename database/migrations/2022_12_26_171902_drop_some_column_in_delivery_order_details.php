<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropSomeColumnInDeliveryOrderDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_order_general_details', function (Blueprint $table) {
            $table->dropForeign('item_receiving_report_foreign');
            $table->dropForeign('item_receiving_report_detail_foreign');

            $table->dropColumn([
                'load_date',
                'unload_date',
                'quantity_remaining',
                'quantity_rejected',
                'quantity_accepted',
                'item_receiving_report_id',
                'item_receiving_report_detail_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_order_general_details', function (Blueprint $table) {
            //
        });
    }
}
