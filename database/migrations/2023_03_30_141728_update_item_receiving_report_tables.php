<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateItemReceivingReportTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_reports', function (Blueprint $table) {
            $table->decimal('sub_total', 18, 2)->nullable()->after('status');
            $table->decimal('tax_total', 18, 2)->nullable()->after('sub_total');
            $table->decimal('total', 18, 2)->nullable()->after('tax_total');
            $table->decimal('exchange_rate', 18, 2)->nullable()->after('total');
        });

        Schema::table('item_receiving_report_details', function (Blueprint $table) {
            $table->decimal('sub_total', 18, 2)->nullable()->after('reference_model');
            $table->decimal('tax_total', 18, 2)->nullable()->after('sub_total');
            $table->decimal('total', 18, 2)->nullable()->after('tax_total');
        });

        Schema::table('item_receiving_po_tradings', function (Blueprint $table) {
            $table->decimal('sub_total', 18, 2)->nullable()->after('loading_order');
            $table->decimal('tax_total', 18, 2)->nullable()->after('sub_total');
            $table->decimal('total', 18, 2)->nullable()->after('tax_total');
        });

        Schema::table('item_receiving_report_purchase_transports', function (Blueprint $table) {
            $table->decimal('sub_total', 18, 2)->nullable()->after('price');
            $table->decimal('tax_total', 18, 2)->nullable()->after('sub_total');
            $table->decimal('total', 18, 2)->nullable()->after('tax_total');
        });

        Schema::table('item_receiving_report_purchase_transport_details', function (Blueprint $table) {
            $table->decimal('sub_total', 18, 2)->nullable()->after('received');
            $table->decimal('tax_total', 18, 2)->nullable()->after('sub_total');
            $table->decimal('total', 18, 2)->nullable()->after('tax_total');
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
