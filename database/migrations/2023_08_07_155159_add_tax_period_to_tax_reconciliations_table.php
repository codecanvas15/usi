<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxPeriodToTaxReconciliationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tax_reconciliations', function (Blueprint $table) {
            $table->date('tax_period')->after('date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tax_reconciliations', function (Blueprint $table) {
            $table->dropColumn('tax_period');
        });
    }
}
