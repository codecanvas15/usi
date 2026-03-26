<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropCalculateAfterDiscount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('purchase_order_general_detail_taxes', function (Blueprint $table) {
        //     $table->dropColumn('calculate_after_discount');
        // });

        // Schema::table('purchase_order_service_detail_taxes', function (Blueprint $table) {
        //     $table->dropColumn('calculate_after_discount');
        // });

        Schema::table('purchase_order_detail_taxes', function (Blueprint $table) {
            $table->dropColumn('calculate_after_discount');
        });

        Schema::table('purchase_order_transport_detail_taxes', function (Blueprint $table) {
            $table->dropColumn('calculate_after_discount');
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
