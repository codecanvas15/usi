<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnNoPoExternalOnSaleOrderGeneralsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_order_generals', function (Blueprint $table) {
            $table->string('no_po_external')->nullable()->after('quotation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_order_generals', function (Blueprint $table) {
            $table->dropColumn('no_po_external');
        });
    }
}
