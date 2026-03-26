<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTaxIdToNullableInPurchaseOrderTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_taxes', function (Blueprint $table) {
            $table->unsignedBigInteger('tax_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_taxes', function (Blueprint $table) {
            $table->unsignedBigInteger('tax_id')->change();
        });
    }
}
