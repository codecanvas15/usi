<?php

use App\Models\PurchaseTransport;
use App\Models\Tax;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseTransportTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_transport_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PurchaseTransport::class)->constrained();
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
        Schema::dropIfExists('purchase_transport_taxes');
    }
}
