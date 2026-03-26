<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockOpnameDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_opname_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->nullable()->constrained('stock_opnames');
            $table->foreignId('item_id')->nullable()->constrained('items');
            $table->foreignId('price_id')->nullable()->constrained('prices');
            $table->decimal('stock', 18)->nullable();
            $table->decimal('real_stock', 18)->nullable();
            $table->decimal('difference', 18)->nullable();
            $table->text('note')->nullable();
            $table->decimal('value', 18)->nullable();
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
        Schema::dropIfExists('stock_opname_details');
    }
}
