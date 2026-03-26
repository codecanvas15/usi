<?php

use App\Models\Purchase;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseIdToCashAdvancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_advance_payments', function (Blueprint $table) {
            $table->foreignIdFor(Purchase::class)->after('fund_submission_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_advance_payments', function (Blueprint $table) {
            $table->dropForeign(['purchase_id']);
            $table->dropColumn('purchase_id');
        });
    }
}
