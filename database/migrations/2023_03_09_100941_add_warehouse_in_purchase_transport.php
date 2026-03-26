<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseInPurchaseTransport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_transports', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\WareHouse::class)->nullable()->after('vendor_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_transports', function (Blueprint $table) {
            //
        });
    }
}
