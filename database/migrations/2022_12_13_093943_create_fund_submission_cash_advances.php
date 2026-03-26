<?php

use App\Models\Coa;
use App\Models\FundSubmission;
use App\Models\Purchase;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundSubmissionCashAdvances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_submission_cash_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(FundSubmission::class)->constrained();
            $table->foreignIdFor(Coa::class)->constrained();
            $table->foreignIdFor(Purchase::class)->nullable()->constrained();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fund_submission_cash_advances');
    }
}
