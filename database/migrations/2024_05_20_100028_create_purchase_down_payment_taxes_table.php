<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseDownPaymentTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_down_payment_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\PurchaseDownPayment::class);
            $table->foreignIdFor(\App\Models\Tax::class);
            $table->decimal('value', 18, 2);
            $table->decimal('amount', 18, 2);
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
        Schema::dropIfExists('purchase_down_payment_taxes');
    }
}
