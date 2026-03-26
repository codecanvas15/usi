<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropPpnCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_generals', function (Blueprint $table) {
            $table->dropColumn('ppn');
        });
        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->dropColumn('ppn');
        });
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('ppn');
        });
        Schema::table('purchase_transports', function (Blueprint $table) {
            $table->dropColumn('ppn');
        });
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->dropColumn('ppn');
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
            $table->decimal('ppn', 8, 4);
        });
        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->decimal('ppn', 8, 4);
        });
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('ppn', 8, 4);
        });
        Schema::table('purchase_transports', function (Blueprint $table) {
            $table->decimal('ppn', 8, 4);
        });
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->decimal('ppn', 8, 4);
        });
    }
}
