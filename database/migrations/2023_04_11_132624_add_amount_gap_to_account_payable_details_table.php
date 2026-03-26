<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountGapToAccountPayableDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_payable_details', function (Blueprint $table) {
            $table->decimal('amount_gap', 18, 3)->nullable()->after('amount');
            $table->decimal('total', 18, 3)->nullable()->after('amount_gap');
            $table->decimal('exchange_rate_gap_foreign', 18, 3)->after('exchange_rate_gap');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_payable_details', function (Blueprint $table) {
            $table->dropColumn('amount_gap');
            $table->dropColumn('total');
            $table->dropColumn('exchange_rate_gap_foreign');
        });
    }
}
