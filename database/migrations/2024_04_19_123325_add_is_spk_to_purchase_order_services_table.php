<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSpkToPurchaseOrderServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->integer('is_spk')->after('amount_discount')->default(0);
            $table->string('spk_number')->after('is_spk')->nullable();
            $table->string('pic')->after('spk_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->dropColumn(['is_spk', 'spk_number', 'pic']);
        });
    }
}
