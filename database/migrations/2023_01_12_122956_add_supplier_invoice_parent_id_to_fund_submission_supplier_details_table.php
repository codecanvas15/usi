<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupplierInvoiceParentIdToFundSubmissionSupplierDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fund_submission_supplier_details', function (Blueprint $table) {
            $table->dropColumn('model');
            $table->dropColumn('reference_id');

            $table->bigInteger('supplier_invoice_parent_id')->after('fund_submission_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fund_submission_supplier_details', function (Blueprint $table) {
            $table->dropColumn('supplier_invoice_parent_id');
        });
    }
}
