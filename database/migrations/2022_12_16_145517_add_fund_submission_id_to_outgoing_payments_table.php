<?php

use App\Models\FundSubmission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFundSubmissionIdToOutgoingPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outgoing_payments', function (Blueprint $table) {
            $table->foreignIdFor(FundSubmission::class)->after('project_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('outgoing_payments', function (Blueprint $table) {
            $table->dropColumn('fund_submission_id');
        });
    }
}
