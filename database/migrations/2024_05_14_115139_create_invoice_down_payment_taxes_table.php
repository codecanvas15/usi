<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceDownPaymentTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_down_payment_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\InvoiceDownPayment::class);
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
        Schema::dropIfExists('invoice_down_payment_taxes');
    }
}
