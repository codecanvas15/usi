<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeDeliveryIdNullableToItemReceivingReportPurchaseTransportDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_report_purchase_transport_details', function (Blueprint $table) {
            $table->unsignedBigInteger('delivery_order_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_receiving_report_purchase_transport_details', function (Blueprint $table) {
            $table->unsignedBigInteger('delivery_order_id')->nullable(false)->change();
        });
    }
}
