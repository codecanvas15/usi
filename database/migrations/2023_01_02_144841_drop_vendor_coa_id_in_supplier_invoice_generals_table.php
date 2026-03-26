<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropVendorCoaIdInSupplierInvoiceGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_invoice_generals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vendor_coa_id');
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
            //
        });
    }
}
