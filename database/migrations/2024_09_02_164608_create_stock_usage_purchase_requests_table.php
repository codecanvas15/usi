<?php

use App\Models\PurchaseRequest;
use App\Models\StockUsage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockUsagePurchaseRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_usage_purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(StockUsage::class);
            $table->foreignIdFor(PurchaseRequest::class);
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
        Schema::dropIfExists('stock_usage_purchase_requests');
    }
}
