<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceGeneralAdditionalTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_general_additional_taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_general_additional_id');
            $table->unsignedBigInteger('tax_id');
            $table->decimal('value', 18, 3);
            $table->decimal('total', 18, 3);
            $table->timestamps();

            $table->foreign('invoice_general_additional_id', 'invg_additional_item_foreign')->references('id')->on('invoice_general_additionals');
            $table->foreign('tax_id', 'invg_add_tax_foreign')->references('id')->on('taxes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_general_additional_taxes');
    }
}
