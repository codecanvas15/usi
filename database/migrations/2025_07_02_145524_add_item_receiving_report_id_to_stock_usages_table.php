<?php

use App\Models\ItemReceivingReport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemReceivingReportIdToStockUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_usages', function (Blueprint $table) {
            $table->foreignIdFor(ItemReceivingReport::class)->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_usages', function (Blueprint $table) {
            $table->dropForeign('stock_usages_item_receiving_report_id_foreign');
            $table->dropColumn('item_receiving_report_id');
        });
    }
}
