<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExternalCodeInDeliveryOrderGeneralsTabe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_order_generals', function (Blueprint $table) {
            $table->string('external_code')->nullable()->after('code');
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
            $table->dropColumn('external_code');
        });
    }
}
