<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePurchaseTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('lpbs');
        });

        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->decimal('jumlah_lpbs', 18, 2)->nullable()->after('jumlah');
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
            $table->dropColumn('jumlah_lpbs');
        });
    }
}
