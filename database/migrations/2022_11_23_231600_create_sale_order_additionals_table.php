<?php

use App\Models\Item;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrderAdditionalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_order_additionals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_order_id')->constrained('sale_orders');
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
        Schema::dropIfExists('sale_order_additionals');
    }
}
