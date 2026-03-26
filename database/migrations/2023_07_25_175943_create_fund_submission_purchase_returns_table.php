<?php

use App\Models\FundSubmission;
use App\Models\PurchaseReturn;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundSubmissionPurchaseReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_submission_purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(FundSubmission::class);
            $table->foreignIdFor(PurchaseReturn::class);
            $table->decimal('exchange_rate', 18, 2);
            $table->decimal('outstanding_amount', 18, 2);
            $table->decimal('amount', 18, 2);
            $table->decimal('amount_foreign', 18, 2);
            $table->decimal('exchange_rate_gap', 18, 2);
            $table->decimal('exchange_rate_gap_idr', 18, 2);
            $table->decimal('exchange_rate_gap_foreign', 18, 2);
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
        Schema::dropIfExists('fund_submission_purchase_returns');
    }
}
