<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderWareHousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_ware_houses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\PoTrading::class)->unique()->constrained('purchase_orders');
            $table->foreignIdFor(\App\Models\WareHouse::class)->constrained();
            $table->decimal('jumlah', 18, 2);
            $table->decimal('sudah_dikirim', 18, 2);
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
        Schema::dropIfExists('purchase_order_ware_houses');
    }
}
