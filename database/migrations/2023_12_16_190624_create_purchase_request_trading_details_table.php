<?php

use App\Models\Item;
use App\Models\PurchaseRequestTrading;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequestTradingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_request_trading_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PurchaseRequestTrading::class);
            $table->foreignIdFor(Item::class);
            $table->decimal('qty', 18, 3);
            $table->decimal('ordered_qty', 18, 3);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_request_trading_details');
    }
}
