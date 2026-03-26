<?php

use App\Models\Tax;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableItemReceivingReportTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_report_taxes', function (Blueprint $table) {
            $table->bigInteger('reference_id')->nullable()->change();
            $table->bigInteger('reference_parent_id')->nullable()->change();
            $table->foreignIdFor(Tax::class)->after('item_receiving_report_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_receiving_report_taxes', function (Blueprint $table) {
            $table->dropForeign('item_receiving_report_taxes_tax_id_foreign');
            $table->dropColumn('tax_id');
        });
    }
}
