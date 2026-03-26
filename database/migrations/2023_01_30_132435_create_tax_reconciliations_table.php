<?php

use App\Models\Branch;
use App\Models\Coa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxReconciliationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->constrained();
            $table->foreignIdFor(Coa::class)->nullable()->constrained();
            $table->string('code');
            $table->date('date');
            $table->date('from_date');
            $table->date('to_date');
            $table->decimal('total_in', 18, 3);
            $table->decimal('total_out', 18, 3);
            $table->decimal('gap', 18, 3);
            $table->string('status')->default('pending');
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
        Schema::dropIfExists('tax_reconciliations');
    }
}
