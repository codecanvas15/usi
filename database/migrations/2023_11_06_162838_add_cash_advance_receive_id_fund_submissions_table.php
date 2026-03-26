<?php

use App\Models\CashAdvanceReceive;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCashAdvanceReceiveIdFundSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fund_submissions', function (Blueprint $table) {
            $table->foreignIdFor(CashAdvanceReceive::class)
                ->after('invoice_return_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fund_submissions', function (Blueprint $table) {
            $table->dropForeign('fund_submissions_cash_advance_receive_id_foreign');
            $table->dropColumn('cash_advance_receive_id');
        });
    }
}
