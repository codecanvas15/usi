<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountInPurchaseOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->decimal('discount_per_liter', 18, 3)->nullable()->after('jumlah_lpbs');
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
            $table->dropColumn('discount_per_liter');
        });
    }
}
