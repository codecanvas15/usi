<?php

use App\Models\Branch;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivablesPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receivables_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->constrained();
            $table->foreignIdFor(Project::class)->nullable()->constrained();
            $table->foreignIdFor(Customer::class)->constrained();
            $table->foreignIdFor(Currency::class)->constrained();
            $table->decimal('exchange_rate', 18, 3);
            $table->date('date');
            $table->string('reference')->nullable();
            $table->string('status');
            $table->string('reject_reason');
            $table->bigInteger('created_by');
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
        Schema::dropIfExists('receivables_payments');
    }
}
