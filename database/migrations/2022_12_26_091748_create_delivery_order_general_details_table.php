<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryOrderGeneralDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_order_general_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\DeliveryOrderGeneral::class)->constrained();
            $table->unsignedBigInteger('sale_order_general_detail_id');
            $table->unsignedBigInteger('item_receiving_report_id')->nullable();
            $table->unsignedBigInteger('item_receiving_report_detail_id')->nullable();
            $table->foreignIdFor(\App\Models\Item::class)->constrained();
            $table->foreignIdFor(\App\Models\Unit::class)->constrained();
            $table->foreignIdFor(\App\Models\WareHouse::class)->nullable()->constrained();
            $table->date('load_date')->nullable();
            $table->date('unload_date')->nullable();
            $table->decimal('quantity', 18, 3);
            $table->decimal('quantity_received', 18, 3)->default(0);
            $table->decimal('quantity_returned', 18, 3)->default(0);
            $table->decimal('quantity_lost', 18, 3)->default(0);
            $table->decimal('quantity_remaining', 18, 3)->default(0);
            $table->decimal('quantity_damage', 18, 3)->default(0);
            $table->decimal('quantity_rejected', 18, 3)->default(0);
            $table->decimal('quantity_accepted', 18, 3)->default(0);
            $table->text('description')->nullable();
            $table->string('status', 60);
            $table->boolean('is_invoice_created')->default(false);
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('sale_order_general_detail_id', 'sale_order_detail_foreign')->references('id')->on('sale_order_general_details');
            $table->foreign('item_receiving_report_id', 'item_receiving_report_foreign')->references('id')->on('item_receiving_reports');
            $table->foreign('item_receiving_report_detail_id', 'item_receiving_report_detail_foreign')->references('id')->on('item_receiving_report_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_order_general_details');
    }
}
