<?php

use App\Models\DeliveryOrder;
use App\Models\VechicleFleet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(DeliveryOrder::class)->constrained();
            $table->foreignIdFor(VechicleFleet::class)->constrained();
            $table->string('segel_atas');
            $table->string('segel_bawah');
            $table->string('temperatur');
            $table->string('meter_awal');
            $table->string('meter_akhir');
            $table->string('sg_meter');
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
        Schema::dropIfExists('delivery_order_details');
    }
}
