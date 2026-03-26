<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePoReferenceIdToSupplierInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
            $table->bigInteger('po_reference_id')->nullable()->change();
            $table->string('po_reference_model')->nullable()->change();
            $table->string('po_reference_kode')->nullable()->change();
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
            $table->bigInteger('po_reference_id')->nullable(false)->change();
            $table->string('po_reference_model')->nullable(false)->change();
            $table->string('po_reference_kode')->nullable(false)->change();
        });
    }
}
