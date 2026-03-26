<?php

use App\Models\DeliveryOrderDetail;
use App\Models\ShNumber;
use App\Models\WareHouse;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryOrderTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_order_types', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(DeliveryOrderDetail::class)->constrained();
            $table->foreignIdFor(ShNumber::class)->constrained();
            $table->foreignIdFor(WareHouse::class)->nullable()->constrained();
            $table->enum('tipe', ['drop', 'supplier']);
            $table->decimal('kuantitas', 18, 2);
            $table->decimal('realisasi', 18, 2);
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
        Schema::dropIfExists('delivery_order_types');
    }
}
