<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClosingDeliveryOrderShipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('closing_delivery_order_ships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('delivery_order_id');
            $table->unsignedBigInteger('losses_coa_id');
            $table->unsignedBigInteger('item_id');
            $table->date('date');
            $table->string('code', 30);
            $table->string('status')->default('approve');
            $table->string('note')->nullable();
            $table->decimal('losses_quantity', 18, 2)->nullable();
            $table->decimal('amount_sent', 18, 2)->nullable();
            $table->decimal('amount_losses', 18, 2)->nullable();
            $table->timestamps();

            // $table->foreign('delivery_order_id', 'closing_do_to_do_table')->references('id')->on('delivery_orders');
            // $table->foreign('losses_coa_id', 'closing_do_to_losses_coa_table')->references('id')->on('coas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('closing_delivery_order_ships');
    }
}
