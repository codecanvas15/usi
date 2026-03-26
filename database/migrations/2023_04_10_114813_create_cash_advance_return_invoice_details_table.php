<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashAdvanceReturnInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_advance_return_invoice_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cash_advance_return_invoice_id');
            $table->unsignedBigInteger('item_receiving_report_id');
            $table->decimal('amount', 18, 2);
            $table->timestamps();

            // $table->foreign('cash_advance_return_invoice_id', 'car_invoice_id')->references('id')->on('cash_advance_return_invoices');
            // $table->foreign('item_receiving_report_id', 'ir_report_id')->references('id')->on('item_receiving_reports');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_advance_return_invoice_details');
    }
}
