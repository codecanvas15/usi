<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsedAmountToTaxReconciliationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tax_reconciliation_details', function (Blueprint $table) {
            $table->decimal('used_amount', 18, 3)->after('in');
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
            $table->dropColumn('used_amount');
        });
    }
}
