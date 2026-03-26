<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropTaxFromPurchaseOrderGeneral extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_general_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tax_id');
            $table->dropColumn('value_tax');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_general_details', function (Blueprint $table) {
            //
        });
    }
}
