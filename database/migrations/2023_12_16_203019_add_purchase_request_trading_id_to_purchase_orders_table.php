<?php

use App\Models\PurchaseRequestTrading;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseRequestTradingIdToPurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreignIdFor(PurchaseRequestTrading::class)->after('branch_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign('purchase_orders_purchase_request_trading_id_foreign');
            $table->dropColumn('purchase_request_trading_id');
        });
    }
}
