<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInvoiceGeneral extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_general_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('delivery_order_general_id');
            $table->unsignedBigInteger('delivery_order_general_detail_id')->nullable();

            $table->dropColumn([
                'quantity_received',
                'quantity_returned',
                'quantity_lost',
                'quantity_damage',
            ]);

            $table->foreign('delivery_order_general_detail_id', '')->references('id')->on('delivery_order_general_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
