<?php

use App\Models\Currency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyToFundSubmissionSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fund_submission_suppliers', function (Blueprint $table) {
            $table->foreignIdFor(Currency::class)->after('coa_id')->constrained()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fund_submission_suppliers', function (Blueprint $table) {
            $table->dropColumn('currency_id');
        });
    }
}
