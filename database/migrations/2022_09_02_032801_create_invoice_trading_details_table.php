<?php

use App\Models\DeliveryOrder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceTradingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_trading_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(DeliveryOrder::class)->constrained();
            $table->decimal('jumlah_diterima', 18, 2);
            $table->decimal('sub_total', 18, 2);
            $table->decimal('total', 18, 2);
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
        Schema::dropIfExists('invoice_trading_details');
    }
}
