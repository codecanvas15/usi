<?php

use App\Models\Item;
use App\Models\Price;
use App\Models\PurchaseOrderService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderServiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_service_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PurchaseOrderService::class)->constrained();
            $table->foreignIdFor(Item::class)->constrained();
            $table->foreignIdFor(Price::class)->constrained();
            $table->decimal('harga', 18, 2);
            $table->decimal('jumlah', 18, 2);
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
        Schema::dropIfExists('purchase_order_service_details');
    }
}
