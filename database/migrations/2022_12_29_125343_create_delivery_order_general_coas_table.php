<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryOrderGeneralCoasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_order_general_coas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\DeliveryOrder::class);
            $table->foreignIdFor(\App\Models\Coa::class);
            $table->unsignedBigInteger('reference_id');
            $table->string('reference_model');
            $table->decimal('type');
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
        Schema::dropIfExists('delivery_order_general_coas');
    }
}
