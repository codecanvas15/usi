<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendorToStockMutationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_mutations', function (Blueprint $table) {
            $table->string('document_model')->after('price_id')->nullable();
            $table->string('document_id')->after('document_model')->nullable();
            $table->string('vendor_model')->after('document_id')->nullable();
            $table->string('vendor_id')->after('vendor_model')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_mutations', function (Blueprint $table) {
            //
        });
    }
}
