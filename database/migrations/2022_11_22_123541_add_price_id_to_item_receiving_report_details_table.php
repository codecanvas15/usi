<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceIdToItemReceivingReportDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_report_details', function (Blueprint $table) {
            $table->foreignId('price_id')->after('item_receiving_report_id')->nullable()->constrained('prices');
            $table->foreignId('item_id')->after('price_id')->nullable()->constrained('items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_receiving_report_details', function (Blueprint $table) {
            //
        });
    }
}
