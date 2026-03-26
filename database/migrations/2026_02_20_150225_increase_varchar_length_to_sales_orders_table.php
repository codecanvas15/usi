<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncreaseVarcharLengthToSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->string('nomor_po_external', 255)->change();
        });
        Schema::table('invoice_tradings', function (Blueprint $table) {
            $table->string('nomor_po_external', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            //
        });
    }
}
