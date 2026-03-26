<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeLengthTax extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_taxes', function (Blueprint $table) {
            $table->decimal('value', 18, 4)->change();
        });
        Schema::table('purchase_order_general_taxes', function (Blueprint $table) {
            $table->decimal('value', 18, 4)->change();
        });
        Schema::table('purchase_order_service_taxes', function (Blueprint $table) {
            $table->decimal('value', 18, 4)->change();
        });
        Schema::table('purchase_transport_taxes', function (Blueprint $table) {
            $table->decimal('value', 18, 4)->change();
        });
        Schema::table('sale_order_taxes', function (Blueprint $table) {
            $table->decimal('value', 18, 4)->change();
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
