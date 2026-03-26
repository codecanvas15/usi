<?php

use App\Models\ReceivablesPayment;
use App\Models\InvoiceReturn;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivablesPaymentInvoiceReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receivables_payment_invoice_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ReceivablesPayment::class);
            $table->foreignIdFor(InvoiceReturn::class);
            $table->decimal('exchange_rate', 18, 2);
            $table->decimal('outstanding_amount', 18, 2);
            $table->decimal('amount', 18, 2);
            $table->decimal('amount_foreign', 18, 2);
            $table->decimal('exchange_rate_gap', 18, 2);
            $table->decimal('exchange_rate_gap_idr', 18, 2);
            $table->decimal('exchange_rate_gap_foreign', 18, 2);
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
        Schema::dropIfExists('receivables_payment_invoice_returns');
    }
}
