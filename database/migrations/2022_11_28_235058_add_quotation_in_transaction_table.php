<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuotationInTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_generals', function (Blueprint $table) {
            $table->string('quotation')->nullable()->after('keterangan');
        });

        Schema::table('purchase_order_services', function (Blueprint $table) {
            $table->string('quotation')->nullable()->after('vendor_id');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('quotation')->nullable()->after('vendor_id');
        });

        Schema::table('sale_orders', function (Blueprint $table) {
            $table->string('quotation')->nullable()->after('created_by');
        });

        Schema::table('sale_order_generals', function (Blueprint $table) {
            $table->string('quotation')->nullable()->after('status');
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
            //
        });
    }
}
