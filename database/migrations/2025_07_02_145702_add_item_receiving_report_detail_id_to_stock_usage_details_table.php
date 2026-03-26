<?php

use App\Models\ItemReceivingReport;
use App\Models\ItemReceivingReportDetail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemReceivingReportDetailIdToStockUsageDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_usage_details', function (Blueprint $table) {
            $table->foreignIdFor(ItemReceivingReportDetail::class)->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_usage_details', function (Blueprint $table) {
            $table->dropForeign('stock_usage_details_item_receiving_report_detail_id_foreign');
            $table->dropColumn('item_receiving_report_detail_id');
        });
    }
}
