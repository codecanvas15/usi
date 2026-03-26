<?php

use App\Models\SoTrading;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_tradings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SoTrading::class)->constrained('sale_orders');
            $table->string('status');
            $table->decimal('jumlah', 18, 2);
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
        Schema::dropIfExists('invoice_tradings');
    }
}
