<?php

use App\Models\Vendor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableTaxReconciliationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tax_reconciliation_details', function (Blueprint $table) {
            $table->foreignIdFor(Vendor::class)->nullable()->change();
            $table->text('note')->after('used_amount')->nullable();
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
            $table->dropForeign('tax_reconciliation_details_vendor_id_foreign');
            $table->dropColumn('vendor_id');
            $table->dropColumn('note');
        });
    }
}
