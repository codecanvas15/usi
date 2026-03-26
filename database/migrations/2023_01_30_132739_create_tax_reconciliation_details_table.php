<?php

use App\Models\JournalDetail;
use App\Models\TaxReconciliation;
use App\Models\TaxReconciliationBalance;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxReconciliationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_reconciliation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TaxReconciliation::class)->constrained();
            $table->foreignIdFor(JournalDetail::class)->nullable()->constrained();
            $table->foreignIdFor(TaxReconciliationBalance::class)->nullable()->constrained();
            $table->string('type');
            $table->decimal('out', 18, 3);
            $table->decimal('in', 18, 3);
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
        Schema::dropIfExists('tax_reconciliation_details');
    }
}
