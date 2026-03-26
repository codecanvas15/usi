<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExchangeRateGapIdrToFundSubmissionSupplierDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fund_submission_supplier_details', function (Blueprint $table) {
            $table->decimal('exchange_rate_gap_idr', 18, 3)->nullable()->after('exchange_rate_gap');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fund_submission_supplier_details', function (Blueprint $table) {
            $table->dropColumn('exchange_rate_gap_idr');
        });
    }
}
