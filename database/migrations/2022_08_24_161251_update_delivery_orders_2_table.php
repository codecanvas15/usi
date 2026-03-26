<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDeliveryOrders2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('item_id');
            $table->dropColumn('dikirim_dengan');

            $table->string('kuantitas_diterima')->nullable()->change();
            $table->string('kuantitas_kirim')->nullable()->change();
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
