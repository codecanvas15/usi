<?php

use App\Models\Currency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_model');
            $table->bigInteger('invoice_id');
            $table->foreignIdFor(Currency::class)->constrained();
            $table->decimal('exchange_rate', 18, 3)->nullable();
            $table->string('model');
            $table->bigInteger('reference_id');
            $table->date('date');
            $table->decimal('amount_to_receive', 18, 3)->nullable();
            $table->decimal('receive_amount', 18, 3)->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('invoice_payments');
    }
}
