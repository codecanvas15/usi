<?php

use App\Models\Item;
use App\Models\Price;
use App\Models\PurchaseOrderGeneral;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchseOrderGeneralDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_general_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PurchaseOrderGeneral::class)->constrained();
            $table->foreignIdFor(Item::class)->constrained();
            $table->foreignIdFor(Price::class)->constrained();
            $table->decimal('jumlah', 18, 2);
            $table->decimal('harga', 18, 2);
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
        Schema::dropIfExists('purchse_order_general_details');
    }
}
