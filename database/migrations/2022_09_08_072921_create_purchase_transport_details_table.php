<?php

use App\Models\PurchaseTransport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseTransportDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_transport_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PurchaseTransport::class)->constrained();
            $table->integer('jumlah_do');
            $table->integer('jumlah');
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
        Schema::dropIfExists('purchase_transport_details');
    }
}
