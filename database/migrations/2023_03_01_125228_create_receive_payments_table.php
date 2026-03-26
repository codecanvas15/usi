<?php

use App\Models\Branch;
use App\Models\Currency;
use App\Models\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receive_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->nullable()->constrained();
            $table->foreignIdFor(Customer::class)->nullable()->constrained();
            $table->foreignIdFor(Currency::class)->nullable()->constrained();
            $table->decimal('exchange_rate', 18, 3)->nullable();
            $table->string('code')->nullable();
            $table->date('date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('realization_date')->nullable();
            $table->string('cheque_no');
            $table->string('from_bank');
            $table->string('realization_bank');
            $table->double('amount');
            $table->string('status', 24);
            $table->string('reject_reason')->nullable();
            $table->foreignId('created_by')->constrained('users');
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
        Schema::dropIfExists('receive_payments');
    }
}
