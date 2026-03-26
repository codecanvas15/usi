<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCalculationDetailInPurchaseGeneralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_general_details', function (Blueprint $table) {
            $table->decimal('sub_total', 20, 2)->default(0)->after('harga');
            $table->decimal('total', 20, 2)->default(0)->after('sub_total');
        });

        Schema::table('purchase_order_general_detail_taxes', function (Blueprint $table) {
            $table->decimal('total', 20, 2)->default(0)->after('value');
        });

        Schema::table('purchase_order_service_details', function (Blueprint $table) {
            $table->decimal('sub_total', 20, 2)->default(0)->after('harga');
            $table->decimal('total', 20, 2)->default(0)->after('sub_total');
        });

        Schema::table('purchase_order_service_detail_taxes', function (Blueprint $table) {
            $table->decimal('total', 20, 2)->default(0)->after('value');
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
            //
        });
    }
}
