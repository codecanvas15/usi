<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxFileToSupplierInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('po_reference_id');
            $table->string('po_reference_model');
            $table->string('po_reference_kode');
            $table->text('tax_file')->after('file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
            //
        });
    }
}
