<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDebitForeignToFundSubmissionSupplierOthersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fund_submission_supplier_others', function (Blueprint $table) {
            $table->decimal('debit_foreign', 18, 3)->nullable()->after('debit');
            $table->decimal('credit_foreign', 18, 3)->nullable()->after('credit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fund_submission_supplier_others', function (Blueprint $table) {
            $table->dropColumn('debit_foreign');
            $table->dropColumn('credit_foreign');
        });
    }
}
