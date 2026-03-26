<?php

use App\Models\Purchase;
use App\Models\PurchaseRequest;
use App\Models\SoTrading;
use App\Models\Supplier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_transports', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Purchase::class)->nullable()->constrained();
            $table->foreignIdFor(PurchaseRequest::class)->nullable()->constrained();
            $table->foreignIdFor(SoTrading::class)->nullable()->constrained('sale_orders');
            $table->foreignIdFor(Supplier::class)->constrained();
            $table->string('kode', 24);
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
        Schema::dropIfExists('purchase_transports');
    }
}
