<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountAmountToItemReceivingReportPurchaseTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_report_purchase_transports', function (Blueprint $table) {
            $table->integer('is_tax_full')->after('lost_discount')->default(0);
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
            $table->dropColumn('is_tax_full');
        });
    }
}
