<?php

use App\Models\Coa;
use App\Models\FundSubmission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundSubmissionSupplierOthersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_submission_supplier_others', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(FundSubmission::class);
            $table->foreignIdFor(Coa::class)->constrained();
            $table->text('note');
            $table->decimal('debit', 18, 3);
            $table->decimal('credit', 18, 3);
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
        Schema::dropIfExists('fund_submission_supplier_others');
    }
}
