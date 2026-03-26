<?php

use App\Models\SoTrading;
use App\Models\Tax;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrderTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_order_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Tax::class)->constrained();
            $table->foreignIdFor(SoTrading::class)->constrained('sale_orders');
            $table->double('value');
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
        Schema::dropIfExists('sale_order_taxes');
    }
}
