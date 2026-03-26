<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFleetIdToStockUsageDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_usage_details', function (Blueprint $table) {
            $table->foreignId('fleet_id')->nullable()->after('necessity')->constrained('fleets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_usage_details', function (Blueprint $table) {
            $table->dropColumn('fleet_id');
        });
    }
}
