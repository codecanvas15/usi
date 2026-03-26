<?php

use App\Models\Coa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClearingToFundSubmissionSupplierDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fund_submission_supplier_details', function (Blueprint $table) {
            $table->foreignIdFor(Coa::class)->after('supplier_invoice_parent_id')->nullable()->constrained();
            $table->decimal('amount_gap_foreign', 18, 3)->after('amount_foreign');
            $table->integer('is_clearing')->after('amount_gap_foreign');
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
        Schema::table('fund_submission_supplier_details', function (Blueprint $table) {
            $table->dropColumn('coa_id');
            $table->dropColumn('amount_gap_foreign');
            $table->dropColumn('is_clearing');
            $table->dropColumn('total_foreign');
        });
    }
}
