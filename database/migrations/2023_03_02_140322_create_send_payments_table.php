<?php

use App\Models\Branch;
use App\Models\FundSubmission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSendPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('send_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->nullable()->constrained();
            $table->foreignIdFor(FundSubmission::class)->nullable()->constrained();
            $table->string('code')->nullable();
            $table->date('date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('realization_date')->nullable();
            $table->string('cheque_no');
            $table->string('from_bank');
            $table->string('realization_bank');
            $table->string('status', 24);
            $table->string('reject_reason')->nullable();
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
        Schema::dropIfExists('send_payments');
    }
}
