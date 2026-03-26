<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePurchaseTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_transports', function (Blueprint $table) {
            $table->dropForeign('purchase_transports_po_trading_id_foreign');
            $table->dropForeign('purchase_transports_ware_house_id_foreign');
            $table->dropColumn('po_trading_id');
            $table->dropColumn('ware_house_id');

            $table->decimal('sub_total', 18, 2)->nullable()->after('harga');
            $table->decimal('ppn')->nullable()->after('sub_total');
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
