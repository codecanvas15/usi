<?php

use App\Models\TaxTrading;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxTradingIdInPurchaseOrderTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_taxes', function (Blueprint $table) {
            $table->foreignIdFor(TaxTrading::class)->nullable()->constrained()->after('tax_id');
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
            $table->dropForeign('tax_trading_id');
        });
    }
}
