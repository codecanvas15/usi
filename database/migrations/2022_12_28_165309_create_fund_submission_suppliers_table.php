<?php

use App\Models\Coa;
use App\Models\FundSubmission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundSubmissionSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_submission_suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(FundSubmission::class)->constrained();
            $table->foreignIdFor(Coa::class)->constrained();
            $table->string('note');
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
        Schema::dropIfExists('fund_submission_suppliers');
    }
}
