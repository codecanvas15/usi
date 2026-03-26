<?php

use App\Models\TaxReconciliation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxReconciliationBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_reconciliation_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TaxReconciliation::class);
            $table->decimal('amount', 18, 3);
            $table->string('status');
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
        Schema::dropIfExists('tax_reconciliation_balances');
    }
}
