<?php

use App\Models\AccountPayable;
use App\Models\SupplierInvoiceParent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountPayableDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_payable_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(AccountPayable::class)->nullable()->constrained();
            $table->foreignIdFor(SupplierInvoiceParent::class)->nullable()->constrained();
            $table->decimal('exchange_rate', 18, 3);
            $table->decimal('outstanding_amount', 18, 3);
            $table->decimal('receive_amount', 18, 3);
            $table->decimal('receive_amount_foreign', 18, 3);
            $table->decimal('exchange_rate_gap', 18, 3);
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
        Schema::dropIfExists('account_payable_details');
    }
}
