<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountForeignToFundSubmissionSupplierLpbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fund_submisison_supplier_lpbs', function (Blueprint $table) {
            $table->decimal('amount_foreign', 20, 2)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fund_submisison_supplier_lpbs', function (Blueprint $table) {
            $table->dropColumn('amount_foreign');
        });
    }
}
