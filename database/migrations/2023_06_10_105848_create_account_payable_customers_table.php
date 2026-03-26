<?php

use App\Models\AccountPayable;
use App\Models\Coa;
use App\Models\FundSubmissionCustomer;
use App\Models\InvoiceParent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountPayableCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_payable_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(AccountPayable::class)->constrained();
            $table->foreignIdFor(FundSubmissionCustomer::class)->nullable()->constrained();
            $table->foreignIdFor(InvoiceParent::class)->nullable()->constrained();
            $table->foreignIdFor(Coa::class)->nullable()->constrained();
            $table->decimal('exchange_rate', 18, 2);
            $table->decimal('outstanding_amount', 18, 2);
            $table->decimal('receive_amount', 18, 2);
            $table->decimal('receive_amount_gap', 18, 2);
            $table->decimal('total', 18, 2);
            $table->decimal('receive_amount_foreign', 18, 2);
            $table->decimal('receive_amount_gap_foreign', 18, 2);
            $table->integer('is_clearing')->nullable();
            $table->text('clearing_note');
            $table->decimal('total_foreign', 18, 2);
            $table->decimal('exchange_rate_gap', 18, 2);
            $table->text('exchange_rate_gap_note');
            $table->decimal('exchange_rate_gap_idr', 18, 2);
            $table->decimal('exchange_rate_gap_foreign', 18, 2);
            $table->text('note');
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
        Schema::dropIfExists('account_payable_customers');
    }
}
