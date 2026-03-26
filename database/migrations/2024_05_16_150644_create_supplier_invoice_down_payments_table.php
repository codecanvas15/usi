<?php

use App\Models\CashAdvancePayment;
use App\Models\SupplierInvoice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierInvoiceDownPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_invoice_down_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SupplierInvoice::class);
            $table->foreignIdFor(CashAdvancePayment::class);
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
        Schema::dropIfExists('supplier_invoice_down_payments');
    }
}
