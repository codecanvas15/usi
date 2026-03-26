<?php

use App\Models\InvoiceDownPayment;
use App\Models\InvoiceParent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDownPaymentInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('down_payment_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(InvoiceDownPayment::class);
            $table->foreignIdFor(InvoiceParent::class);
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
        Schema::dropIfExists('down_payment_invoices');
    }
}
