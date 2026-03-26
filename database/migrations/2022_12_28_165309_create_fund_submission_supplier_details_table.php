<?php

use App\Models\FundSubmission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundSubmissionSupplierDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_submission_supplier_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(FundSubmission::class)->constrained();
            $table->string('model');
            $table->bigInteger('reference_id');
            $table->decimal('exchange_rate', 18, 3);
            $table->decimal('outstanding_amount', 18, 3);
            $table->decimal('amount', 18, 3);
            $table->string('note', 18, 3);
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
        Schema::dropIfExists('fund_submission_supplier_details');
    }
}
