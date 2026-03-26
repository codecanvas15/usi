<?php

use App\Models\Coa;
use App\Models\ReceivablesPayment;
use App\Models\SupplierInvoiceParent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivablesPaymentVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receivables_payment_vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ReceivablesPayment::class)->nullable()->constrained();
            $table->foreignIdFor(SupplierInvoiceParent::class)->nullable()->constrained();
            $table->foreignIdFor(Coa::class)->nullable()->constrained();
            $table->decimal('exchange_rate', 18, 2);
            $table->decimal('outstanding_amount', 18, 2);
            $table->decimal('amount', 18, 2);
            $table->decimal('amount_gap', 18, 2);
            $table->decimal('total', 18, 2);
            $table->decimal('amount_foreign', 18, 2);
            $table->decimal('amount_gap_foreign', 18, 2);
            $table->integer('is_clearing')->nullable();
            $table->text('clearing_note');
            $table->decimal('total_foreign', 18, 2);
            $table->decimal('exchange_rate_gap', 18, 2);
            $table->decimal('exchange_rate_gap_idr', 18, 2);
            $table->decimal('exchange_rate_gap_foreign', 18, 2);
            $table->text('exchange_rate_gap_note');
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
        Schema::dropIfExists('receivables_payment_vendors');
    }
}
