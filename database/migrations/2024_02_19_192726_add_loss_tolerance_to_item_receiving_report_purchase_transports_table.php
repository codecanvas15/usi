<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLossToleranceToItemReceivingReportPurchaseTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_report_purchase_transports', function (Blueprint $table) {
            $table->double('loss_tolerance')->nullable()->after('total');
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
            $table->dropColumn('loss_tolerance');
        });
    }
}
