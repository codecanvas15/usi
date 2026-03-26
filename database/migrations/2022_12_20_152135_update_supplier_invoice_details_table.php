<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSupplierInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_invoice_details', function (Blueprint $table) {
            $table->foreignId('reference_id')->nullable()->after('item_receiving_report_id');
            $table->string('reference_model')->nullable()->after('reference_id');
            $table->decimal('sub_total', 15, 2)->nullable()->after('reference_model');
            $table->decimal('tax', 15, 2)->nullable()->after('sub_total');
            $table->decimal('total', 15, 2)->nullable()->after('tax');
            $table->string('tax_reference')->nullable()->after('total');
            $table->mediumText('notes')->nullable()->after('tax_reference');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_invoice_details', function (Blueprint $table) {
            //
        });
    }
}
