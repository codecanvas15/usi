<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOutstandingInCashAdvanceReturnInvoiceDetalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_advance_return_invoice_details', function (Blueprint $table) {
            $table->decimal('outstanding', 18, 2)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_advance_return_invoice_details', function (Blueprint $table) {
            $table->dropColumn('outstanding');
        });
    }
}
