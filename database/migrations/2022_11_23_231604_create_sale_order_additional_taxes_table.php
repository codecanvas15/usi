<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrderAdditionalTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_order_additional_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\SaleOrderAdditional::class)->constrained();
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
        Schema::dropIfExists('sale_order_additional_taxes');
    }
}
