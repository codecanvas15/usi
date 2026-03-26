<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtherCostToPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_generals', function (Blueprint $table) {
            $table->decimal('other_cost', 18, 4)->nullable()->after('total');
        });
        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->decimal('other_cost', 18, 4)->nullable()->after('total');
        });
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('other_cost', 18, 4)->nullable()->after('total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_generals', function (Blueprint $table) {
            $table->dropColumn('other_cost');
        });
        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->dropColumn('other_cost');
        });
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('other_cost');
        });
    }
}
