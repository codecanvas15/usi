<?php

use App\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateReceiveInItemReceivingReportTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_reports', function (Blueprint $table) {
            $table->foreignIdFor(Branch::class)->nullable()->after('id')->constrained();
            $table->date('date_receive')->nullable()->after('branch_id');
            $table->time('date_receive_time')->nullable()->after('date_receive');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_receiving_reports', function (Blueprint $table) {
            $table->dropColumn('date_receive');
        });
    }
}
