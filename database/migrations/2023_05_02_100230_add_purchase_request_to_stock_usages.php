<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseRequestToStockUsages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_usages', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\PurchaseRequest::class)->nullable()->after('fleet_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_usages', function (Blueprint $table) {
            $table->dropForeign(['purchase_request_id']);
            $table->dropColumn('purchase_request_id');
        });
    }
}
