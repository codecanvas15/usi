<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdOldDataInDeliveryOrderGenerals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_order_generals', function (Blueprint $table) {
            $table->boolean('is_old_data')->after('is_invoice_created')->default(true);
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
            $table->dropColumn(['is_old_data']);
        });
    }
}
