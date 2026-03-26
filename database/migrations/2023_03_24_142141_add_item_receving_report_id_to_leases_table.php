<?php

use App\Models\Item;
use App\Models\ItemReceivingReportDetail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemRecevingReportIdToLeasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->foreignIdFor(ItemReceivingReportDetail::class)->after('id')->nullable();
            $table->foreignIdFor(Item::class)->nullable()->after('item_receiving_report_detail_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->dropForeign('leases_item_receiving_report_detail_id_foreign');
            $table->dropForeign('leases_item_id_foreign');
            $table->dropColumn('item_receiving_report_detail_id');
            $table->dropColumn('item_id');
        });
    }
}
