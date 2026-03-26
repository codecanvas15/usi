<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderDetailTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_detail_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\PoTradingDetail::class, 'purchase_order_general_detail_id');
            $table->foreignIdFor(\App\Models\Tax::class, 'tax_id');
            $table->decimal('value', 18, 4);
            $table->boolean('calculate_after_discount', false);
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
        Schema::dropIfExists('purchase_order_detail_taxes');
    }
}
