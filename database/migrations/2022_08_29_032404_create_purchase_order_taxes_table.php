<?php

use App\Models\PoTrading;
use App\Models\Tax;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Tax::class)->constrained();
            $table->foreignIdFor(PoTrading::class)->constrained('purchase_orders');
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
        Schema::dropIfExists('purchase_order_taxes');
    }
}
