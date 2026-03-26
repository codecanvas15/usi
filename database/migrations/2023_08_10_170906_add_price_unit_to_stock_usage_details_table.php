<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceUnitToStockUsageDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_usage_details', function (Blueprint $table) {
            $table->decimal('price_unit', 18, 2)->after('price_id')->default();
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
            $table->dropColumn('price_unit');
        });
    }
}
