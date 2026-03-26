<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrderGeneralDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_order_general_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\SaleOrderGeneral::class)->constrained();
            $table->foreignIdFor(\App\Models\Item::class)->constrained();
            $table->decimal('price', 18, 3);
            $table->decimal('amount', 18, 3);
            $table->decimal('sended', 18, 3);
            $table->decimal('sub_total', 18, 3);
            $table->decimal('total', 18, 3);
            $table->string('status', 32);
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
        Schema::dropIfExists('sale_order_general_details');
    }
}
