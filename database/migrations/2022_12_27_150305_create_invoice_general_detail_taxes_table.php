<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceGeneralDetailTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_general_detail_taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_general_detail_id');
            // $table->foreignIdFor(\App\Models\InvoiceGeneralDetail::class)->constrained();
            $table->foreignIdFor(\App\Models\Tax::class)->constrained();
            $table->decimal('value', 18, 3);
            $table->decimal('total', 18, 3);
            $table->timestamps();

            $table->foreign('invoice_general_detail_id', 'invg_detail_tax_foreign')->references('id')->on('invoice_general_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_general_detail_taxes');
    }
}
