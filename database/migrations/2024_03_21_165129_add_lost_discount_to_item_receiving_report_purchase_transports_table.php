<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLostDiscountToItemReceivingReportPurchaseTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_report_purchase_transports', function (Blueprint $table) {
            $table->decimal('lost_discount', 18, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_receiving_report_purchase_transports', function (Blueprint $table) {
            $table->dropColumn('lost_discount');
        });
    }
}
