<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStatusToSupplierInvoiceGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_invoice_generals', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('supplier_invoice_generals', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approve', 'rejected', 'revert'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_invoice_generals', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approve', 'rejected', 'revert'])->default('pending');
        });
    }
}
