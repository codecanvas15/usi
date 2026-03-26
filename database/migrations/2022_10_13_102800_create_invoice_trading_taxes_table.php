<?php

use App\Models\InvoiceTrading;
use App\Models\Tax;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceTradingTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_trading_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(InvoiceTrading::class)->constrained();
            $table->foreignIdFor(Tax::class)->constrained();
            $table->decimal('value', 18, 4);
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
        Schema::dropIfExists('invoice_trading_taxes');
    }
}
