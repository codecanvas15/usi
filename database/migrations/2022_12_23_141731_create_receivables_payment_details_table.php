<?php

use App\Models\InvoiceTrading;
use App\Models\ReceivablesPayment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivablesPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receivables_payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ReceivablesPayment::class)->constrained();
            $table->foreignIdFor(InvoiceTrading::class)->constrained();
            $table->decimal('outstanding_amount', 18, 3);
            $table->decimal('receive_amount', 18, 3);
            $table->decimal('exchange_rate_gap', 18, 3);
            $table->text('note');
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
        Schema::dropIfExists('receivables_payment_details');
    }
}
