<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtherCostToSaleOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->decimal('other_cost', 18, 2)->nullable()->after('sub_total');
            $table->decimal('ppn')->nullable()->after('other_cost');
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
            //
        });
    }
}
