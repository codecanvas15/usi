<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryOrderShipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_order_ships', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Branch::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\SoTrading::class)->nullable()->constrained('sale_orders');
            $table->foreignIdFor(\App\Models\WareHouse::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\PurchaseTransport::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\PurchaseTransportDetail::class)->nullable()->constrained();
            $table->date("target_delivery")->nullable();
            $table->date("load_date")->nullable();
            $table->date("unload_date")->nullable();
            $table->string("code")->nullable();
            $table->decimal('load_quantity_realization', 18, 2)->default(0);
            $table->decimal('load_quantity', 18, 2)->default(0);
            $table->decimal('unload_quantity', 18, 2)->default(0);
            $table->decimal('unload_quantity_realization', 18, 2)->default(0);
            $table->decimal('quantity_used', 18, 2)->default(0);
            $table->string('hpp')->nullable();
            $table->string('file')->nullable();
            $table->string('description')->nullable();
            $table->string('top_seal')->nullable();
            $table->string('bottom_seal')->nullable();
            $table->string('temperature')->nullable();
            $table->string('initial_meter')->nullable();
            $table->string('initial_final')->nullable();
            $table->string('sg_meter')->nullable();
            $table->string('status')->nullable();
            $table->string('status_print')->nullable();
            $table->text('fleet_information')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->softDeletes();
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
        Schema::dropIfExists('delivery_order_ships');
    }
}
