<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveReferenceUniqueFromSupplierInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
            $table->dropUnique('supplier_invoices_reference_unique');
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
            $table->string('reference')->unique()->nullable()->change();
        });
    }
}
