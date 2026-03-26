<?php

use App\Models\TaxTrading;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxTradingIdToPurchaseReturnTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_return_taxes', function (Blueprint $table) {
            $table->foreignIdFor(TaxTrading::class)->nullable()->after('tax_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_return_taxes', function (Blueprint $table) {
            $table->dropForeign(['tax_trading_id']);
        });
    }
}
