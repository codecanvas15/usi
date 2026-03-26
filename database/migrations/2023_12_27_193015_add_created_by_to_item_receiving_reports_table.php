<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCreatedByToItemReceivingReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_reports', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users');
        });

        $item_receiving_reports = DB::table('item_receiving_reports')->get();
        $activity_logs = DB::table('activity_log')
            ->whereIn('subject_type', $item_receiving_reports->pluck('reference_model')->toArray())
            ->whereIn('subject_id', $item_receiving_reports->pluck('reference_id')->toArray())
            ->get();

        foreach ($item_receiving_reports as $key => $item_receiving_report) {
            DB::table('item_receiving_reports')
                ->where('id', $item_receiving_report->id)
                ->update(
                    ['created_by' => $activity_logs
                        ->where('subject_id', $item_receiving_report->reference_id)
                        ->where('subject_type', $item_receiving_report->reference_model)
                        ->first()->causer_id]
                );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_receiving_reports', function (Blueprint $table) {
            $table->dropForeign('item_receiving_reports_created_by_foreign');
            $table->dropColumn('created_by');
        });
    }
}
