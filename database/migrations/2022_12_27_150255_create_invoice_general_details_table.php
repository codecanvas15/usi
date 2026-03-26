<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceGeneralDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_general_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_general_id');
            $table->unsignedBigInteger('sale_order_general_detail_id');
            $table->unsignedBigInteger('delivery_order_general_detail_id');
            $table->foreignIdFor(\App\Models\Item::class)->constrained();
            $table->foreignIdFor(\App\Models\Unit::class)->constrained();
            $table->decimal('quantity', 18, 3);
            $table->decimal('quantity_received', 18, 3)->default(0);
            $table->decimal('quantity_returned', 18, 3)->default(0);
            $table->decimal('quantity_lost', 18, 3)->default(0);
            $table->decimal('quantity_damage', 18, 3)->default(0);
            $table->decimal('price', 18, 3);
            $table->enum('calculation_type', ['sended', 'received']);
            $table->decimal('sub_total', 18, 3)->default(0);
            $table->decimal('total_tax', 18, 3)->default(0);
            $table->decimal('total', 18, 3)->default(0);
            $table->timestamps();

            $table->foreign('invoice_general_id', 'invg_detail_foreign')->references('id')->on('invoice_generals');
            $table->foreign('sale_order_general_detail_id', 'sog_detail_foreign')->references('id')->on('sale_order_general_details');
            $table->foreign('delivery_order_general_detail_id', 'dog_detail_foreign')->references('id')->on('delivery_order_general_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_general_details');
    }
}
