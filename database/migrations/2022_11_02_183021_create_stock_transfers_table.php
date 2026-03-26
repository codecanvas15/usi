<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->nullable()->constrained('items');
            $table->foreignId('price_id')->nullable()->constrained('prices');
            $table->string('code')->nullable();
            $table->date('date')->nullable();
            $table->foreignId('from')->constrained('ware_houses')->nullable();
            $table->foreignId('to')->constrained('ware_houses')->nullable();
            $table->decimal('qty', 18)->nullable();
            $table->string('note')->nullable();
            $table->string('status')->nullable();
            $table->string('created_by')->nullable();
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
        Schema::dropIfExists('stock_transfers');
    }
}
