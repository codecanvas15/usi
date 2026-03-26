<?php

use App\Models\InvoiceTrading;
use App\Models\Item;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvTradingAddOnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inv_trading_add_ons', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(InvoiceTrading::class)->constrained('invoice_tradings');
            $table->foreignIdFor(Item::class)->constrained('items');
            $table->decimal('quantity', 18, 2);
            $table->decimal('price', 18, 3);
            $table->decimal('sub_total', 18, 3);
            $table->decimal('total', 18, 3);
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
        Schema::dropIfExists('inv_trading_add_ons');
    }
}
