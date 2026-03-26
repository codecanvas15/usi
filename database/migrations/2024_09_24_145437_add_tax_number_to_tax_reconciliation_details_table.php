<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxNumberToTaxReconciliationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tax_reconciliation_details', function (Blueprint $table) {
            $table->string('tax_number')->after('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tax_reconciliation_details', function (Blueprint $table) {
            $table->dropColumn('tax_number');
        });
    }
}
