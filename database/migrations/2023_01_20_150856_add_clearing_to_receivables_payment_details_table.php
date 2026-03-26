<?php

use App\Models\Coa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClearingToReceivablesPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receivables_payment_details', function (Blueprint $table) {
            $table->foreignIdFor(Coa::class)->after('invoice_parent_id')->nullable()->constrained();
            $table->decimal('receive_amount_gap_foreign', 18, 3)->after('receive_amount_foreign');
            $table->integer('is_clearing')->after('receive_amount_gap_foreign');
            $table->decimal('total_foreign', 18, 3)->after('is_clearing');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receivables_payment_details', function (Blueprint $table) {
            $table->dropColumn('coa_id');
            $table->dropColumn('receive_amount_gap_foreign');
            $table->dropColumn('is_clearing');
            $table->dropColumn('total_foreign');
        });
    }
}
