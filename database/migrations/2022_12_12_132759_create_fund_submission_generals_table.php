<?php

use App\Models\Coa;
use App\Models\FundSubmission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundSubmissionGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_submission_generals', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(FundSubmission::class)->constrained();
            $table->foreignIdFor(Coa::class);
            $table->decimal('amount', 18, 3);
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
        Schema::dropIfExists('fund_submission_generals');
    }
}
