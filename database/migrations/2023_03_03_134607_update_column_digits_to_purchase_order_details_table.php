<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnDigitsToPurchaseOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->dropColumn('harga');
            $table->dropColumn('discount_per_liter');
        });
        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->double('harga', 18, 3)->default(0);
            $table->double('discount_per_liter', 18, 3)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_details', function (Blueprint $table) {
            //
        });
    }
}
