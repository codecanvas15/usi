<?php

use App\Models\Branch;
use App\Models\Coa;
use App\Models\Currency;
use App\Models\FundSubmission;
use App\Models\Project;
use App\Models\Vendor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountPayablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_payables', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->nullable()->constrained();
            $table->foreignIdFor(FundSubmission::class)->nullable()->constrained();
            $table->foreignIdFor(Project::class)->nullable()->constrained();
            $table->foreignIdFor(Vendor::class)->nullable()->constrained();
            $table->foreignIdFor(Coa::class)->nullable()->constrained();
            $table->foreignIdFor(Currency::class)->nullable()->constrained();
            $table->unsignedBigInteger('supplier_invoice_currency_id');
            $table->foreign('supplier_invoice_currency_id')->references('id')->on('currencies');
            $table->decimal('exchange_rate', 18, 3);
            $table->string('code');
            $table->date('date');
            $table->decimal('total', 18, 3);
            $table->decimal('exchange_rate_gap_total', 18, 3);
            $table->string('status')->default('pending');
            $table->string('reject_reason')->nullable();
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
        Schema::dropIfExists('account_payables');
    }
}
