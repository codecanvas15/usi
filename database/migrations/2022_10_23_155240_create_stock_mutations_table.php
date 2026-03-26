<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockMutationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ware_house_id')->nullable()->constrained('ware_houses');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->foreignId('item_id')->nullable()->constrained('items');
            $table->foreignId('price_id')->nullable()->constrained('prices');
            $table->string('type')->nullable();
            $table->decimal('in', 18)->nullable();
            $table->decimal('out', 18)->nullable();
            $table->decimal('booking_in', 18)->nullable();
            $table->decimal('booking_out', 18)->nullable();
            $table->string('note')->nullable();
            $table->decimal('price_unit', 18)->nullable();
            $table->decimal('subtotal', 18)->nullable();
            $table->decimal('total', 18)->nullable();
            $table->decimal('value', 18)->nullable();
            $table->integer('is_return')->nullable();
            $table->integer('tax')->nullable();
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
        Schema::dropIfExists('stock_mutations');
    }
}
