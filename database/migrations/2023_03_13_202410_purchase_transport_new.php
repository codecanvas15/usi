<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PurchaseTransportNew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_transports', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\PurchaseTransport::class)->nullable()->after('currency_id')->constrained();
        });

        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->string('type', 30)->nullable()->default('delivery-order')->after('is_double_handling');
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
