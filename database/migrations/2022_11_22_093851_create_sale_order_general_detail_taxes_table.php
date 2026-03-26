<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrderGeneralDetailTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_order_general_detail_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('so_general_detail_id')->references('id')->on('sale_order_general_details');
            $table->foreignIdFor(\App\Models\Tax::class)->constrained();
            $table->decimal('value', 18, 4);
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
        Schema::dropIfExists('sale_order_general_detail_taxes');
    }
}
