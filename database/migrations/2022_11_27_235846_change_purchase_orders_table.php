<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // * add
            $table->decimal('sub_total_after_tax', 20, 3)->nullable()->after('sub_total');

            // * update
            $table->decimal('sub_total', 18, 3)->change();
            $table->decimal('total', 18, 3)->change();
            $table->decimal('other_cost', 18, 3)->change();
        });

        Schema::table('purchase_order_taxes', function (Blueprint $table) {
            $table->decimal('total', 18, 3)->nullable()->after('value');
        });

        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->decimal('sub_total', 18, 3)->after('status');
            $table->decimal('total', 18, 3)->after('sub_total');
        });

        Schema::table('purchase_order_additional_items', function (Blueprint $table) {
            $table->decimal('sub_total', 18, 3)->after('jumlah');
            $table->decimal('total', 18, 3)->after('sub_total');
        });

        Schema::table('purchase_order_additional_taxs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('po_trading_id');

            $table->foreignId('po_additional_id')->constrained('purchase_order_additional_items')->onDelete('cascade');
            $table->decimal('total', 18, 3)->after('value');
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
