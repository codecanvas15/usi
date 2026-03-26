<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLpbToStockUsageDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_usage_details', function (Blueprint $table) {
            $table->string('po_model')->after('price_id')->nullable();
            $table->string('po_id')->after('po_model')->nullable();
            $table->string('lpb_model')->after('po_id')->nullable();
            $table->string('lpb_id')->after('lpb_model')->nullable();
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
            //
        });
    }
}
