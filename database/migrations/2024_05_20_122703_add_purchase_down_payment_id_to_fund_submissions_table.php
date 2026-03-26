<?php

use App\Models\PurchaseDownPayment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseDownPaymentIdToFundSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fund_submissions', function (Blueprint $table) {
            $table->foreignIdFor(PurchaseDownPayment::class)->after('purchase_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fund_submissions', function (Blueprint $table) {
            $table->dropForeignIdFor(PurchaseDownPayment::class);
            $table->dropColumn('purchase_down_payment_id');
        });
    }
}
