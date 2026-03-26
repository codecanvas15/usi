<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_transports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_id');
        });

        Schema::table('purchase_order_generals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_id');
        });

        // Schema::table('purchase_orders', function (Blueprint $table) {
        //     $table->dropColumn('supplier_id');
        // });

        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_id');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_id');
        });

        Schema::dropIfExists('suppliers');
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
