<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtherCostInSaleOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->decimal('sub_total_after_tax', 18, 3)->after('sub_total');
            $table->decimal('other_cost', 18, 3)->after('sub_total_after_tax');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->dropColumn('other_cost');
        });
    }
}
