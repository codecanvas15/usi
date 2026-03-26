<?php

use App\Models\Coa;
use App\Models\InvoiceTrading;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceTradingCoasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_trading_coas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(InvoiceTrading::class)->constrained();
            $table->foreignIdFor(Coa::class)->constrained();
            $table->string('type', 60);
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
        Schema::dropIfExists('invoice_trading_coas');
    }
}
