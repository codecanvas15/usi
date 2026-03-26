<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceReturnIdToFundSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fund_submissions', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\InvoiceReturn::class, 'invoice_return_id')
                ->after('currency_id')->nullable()->constrained();
        });

        Schema::table('fund_submission_generals', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\InvoiceReturn::class, 'invoice_return_id')
                ->after('fund_submission_id')->nullable()->constrained();
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
            $table->dropForeign('fund_submissions_invoice_return_id_foreign');
            $table->dropColumn('invoice_return_id');
        });

        Schema::table('fund_submission_generals', function (Blueprint $table) {
            $table->dropForeign('fund_submission_generals_invoice_return_id_foreign');
            $table->dropColumn('invoice_return_id');
        });
    }
}
