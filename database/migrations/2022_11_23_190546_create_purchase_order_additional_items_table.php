<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Item;
use App\Models\PoTrading;

class CreatePurchaseOrderAdditionalItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_additional_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Item::class)->nullable()->constrained();
            $table->foreignIdFor(PoTrading::class)->constrained('purchase_orders');
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
        Schema::dropIfExists('purchase_order_additional_items');
    }
}
