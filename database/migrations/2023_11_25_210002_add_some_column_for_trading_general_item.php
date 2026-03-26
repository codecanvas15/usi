<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnForTradingGeneralItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_generals', function (Blueprint $table) {
            $table->string('type', 30)->default('purchase-request')->after('approved_by');
        });

        Schema::table('purchase_order_general_details', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_request_id')->nullable()->change();
            $table->unsignedBigInteger('sales_order_general_id')->nullable()->after('purchase_request_id');
        });

        Schema::table('purchase_order_general_detail_items', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_request_detail_id')->nullable()->change();
            $table->unsignedBigInteger('sale_order_general_detail_id')->nullable()->after('purchase_request_detail_id');
            $table->decimal('quantity_paired', 18, 2)->default(0)->after('quantity');
            $table->string('status_pairing', 30)->default('unpaired')->after('status');
        });

        Schema::table('sale_order_generals', function (Blueprint $table) {
            $table->string('type', 30)->default('sale-order')->after('approved_by');
        });

        Schema::table('sale_order_general_details', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_order_general_id')->nullable()->after('unit_id');
            $table->decimal('amount_paired', 18, 3);
            $table->string('status_pairing', 30)->default('unpaired')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_generals', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('purchase_order_general_details', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_request_id')->nullable(false)->change();
            $table->dropColumn('sales_order_general_id');
        });

        Schema::table('purchase_order_general_detail_items', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_request_detail_id')->nullable(false)->change();
            $table->dropColumn('sale_order_general_detail_id');
            $table->dropColumn('quantity_paired');
            $table->dropColumn('status_pairing');
        });

        Schema::table('sale_order_generals', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('sale_order_general_details', function (Blueprint $table) {
            $table->dropColumn('purchase_order_general_id');
            $table->dropColumn('amount_paired');
            $table->dropColumn('status_pairing');
        });
    }
}
