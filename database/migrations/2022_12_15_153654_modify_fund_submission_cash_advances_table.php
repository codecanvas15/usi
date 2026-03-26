<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyFundSubmissionCashAdvancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fund_submission_cash_advances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('purchase_id');
            $table->string('type')->after('coa_id');
            $table->string('note')->after('type');
            $table->decimal('debit', 18, 3)->after('note');
            $table->decimal('credit', 18, 3)->after('debit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fund_submission_cash_advances', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('note');
            $table->dropColumn('debit');
            $table->dropColumn('credit');
        });
    }
}
