<?php

use App\Models\ItemReceivingReport;
use App\Models\PurchaseOrderAdditionalItems;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemReceivingPoTradingAdditionalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_receiving_po_trading_additionals', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ItemReceivingReport::class);
            $table->foreignIdFor(PurchaseOrderAdditionalItems::class);
            $table->decimal('outstanding_qty', 18, 2)->nullable();
            $table->decimal('receive_qty', 18, 2);
            $table->decimal('subtotal', 18, 2);
            $table->decimal('tax_total', 18, 2);
            $table->decimal('total', 18, 2);
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
        Schema::dropIfExists('item_receiving_po_trading_additionals');
    }
}
