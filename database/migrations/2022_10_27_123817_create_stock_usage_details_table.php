<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockUsageDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_usage_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_usage_id')->nullable()->constrained('stock_usages');
            $table->foreignId('item_id')->nullable()->constrained('items');
            $table->foreignId('price_id')->nullable()->constrained('prices');
            $table->string('code')->nullable();
            $table->decimal('stock', 18)->nullable();
            $table->decimal('qty', 18)->nullable();
            $table->decimal('total', 18)->nullable();
            $table->string('necessity')->nullable();
            $table->string('employee_id')->nullable();
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
        Schema::dropIfExists('stock_usage_details');
    }
}
