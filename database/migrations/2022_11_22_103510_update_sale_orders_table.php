<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSaleOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('other_cost');

            $table->decimal('sub_total', 18, 3)->change();
            $table->decimal('total', 18, 3)->change();
        });

        Schema::table('sale_order_details', function (Blueprint $table) {
            $table->decimal('harga', 18, 3)->change();
            $table->decimal('sub_total', 18, 3)->nullable();
            $table->decimal('total', 18, 3)->nullable();
            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
