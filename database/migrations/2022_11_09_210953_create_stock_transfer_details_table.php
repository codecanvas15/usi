<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTransferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transfer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->nullable()->constrained('items');
            $table->foreignId('price_id')->nullable()->constrained('prices');
            $table->decimal('qty', 18)->nullable();
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
        Schema::dropIfExists('stock_transfer_details');
    }
}
