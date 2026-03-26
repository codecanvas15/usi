<?php

use App\Models\ItemReceivingReport;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDueDateToItemReceivingReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_reports', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('date_receive');
        });

        $item_receiving_reports =  ItemReceivingReport::all();
        foreach ($item_receiving_reports as $key => $item_receiving_report) {
            $top_days = $item_receiving_report->reference->term_of_payment_days ?? $item_receiving_report->reference->top_days ?? $item_receiving_report->vendor->top_days;
            $item_receiving_report->due_date = Carbon::parse($item_receiving_report->date_receive)->addDays($top_days);
            $item_receiving_report->save();
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
            $table->dropColumn('due_date');
        });
    }
}
