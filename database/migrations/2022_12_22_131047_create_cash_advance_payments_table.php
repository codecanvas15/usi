<?php

use App\Models\Branch;
use App\Models\Currency;
use App\Models\FundSubmission;
use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashAdvancePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_advance_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->constrained();
            $table->foreignIdFor(Project::class)->nullable()->constrained();
            $table->foreignIdFor(FundSubmission::class)->constrained();
            $table->string('code');
            $table->string('to_model');
            $table->bigInteger('to_id')->nullable();
            $table->string('to_name');
            $table->date('date');
            $table->string('reference')->nullable();
            $table->foreignIdFor(Currency::class)->constrained();
            $table->double('exchange_rate', 18, 2);
            $table->text('keterangan')->nullable();
            $table->string('status');
            $table->text('reject_reason')->nullable();
            $table->bigInteger('created_by')->nullable();
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
        Schema::dropIfExists('cash_advance_payments');
    }
}
