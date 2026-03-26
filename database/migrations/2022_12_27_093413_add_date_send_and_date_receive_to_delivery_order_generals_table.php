<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateSendAndDateReceiveToDeliveryOrderGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_order_generals', function (Blueprint $table) {
            $table->date('date_send')->nullable()->after('date');
            $table->date('date_receive')->nullable()->after('date_send');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_order_generals', function (Blueprint $table) {
            $table->dropColumn([
                'date_send',
                'date_receive',
            ]);
        });
    }
}
