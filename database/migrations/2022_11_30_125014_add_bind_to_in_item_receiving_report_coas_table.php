<?php

use App\Models\ItemReceivingReportDetail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBindToInItemReceivingReportCoasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_report_coas', function (Blueprint $table) {
            $table->string('bind_to', 25)->nullable()->after('reference_id');
            $table->foreignIdFor(ItemReceivingReportDetail::class, 'item_receiving_report_detail_id')->nullable()->after('bind_to');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_item_receiving_report_coas', function (Blueprint $table) {
            //
        });
    }
}
