<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddOrderingToJournalDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('journal_details', function (Blueprint $table) {
            $table->string('ordering')->after('remark')->nullable();
            $table->timestamp('timestamp')->after('ordering')->nullable();
        });

        $journal_details = DB::table('journal_details')->get();
        foreach ($journal_details as $key => $journal_detail) {
            $journal = DB::table('journals')->where('id', $journal_detail->journal_id)->first();
            DB::table('journal_details')->where('id', $journal_detail->id)
                ->update([
                    'timestamp' => Carbon::parse($journal->date . ' ' . Carbon::parse($journal_detail->created_at)->format('H:i:s')),
                ]);
        }

        $journal_details = DB::table('journal_details')
            ->orderBy('timestamp')
            ->get();

        foreach ($journal_details as $key => $journal_detail) {
            $journal = DB::table('journals')->where('id', $journal_detail->journal_id)->first();

            $max_ordering = DB::table('journal_details')
                ->join('journals', 'journals.id', 'journal_details.journal_id')
                ->whereDate('journal_details.timestamp', Carbon::parse($journal->date))
                ->max('ordering');

            if (!$max_ordering) {
                $new_ordering = Carbon::parse($journal->date)->format('ynd') . "-" . sprintf("%05s", 1);
            } else {
                $explode_ordering = explode("-", $max_ordering)[1];
                $new_ordering = Carbon::parse($journal->date)->format('ynd') . "-" . sprintf("%05s", $explode_ordering + 1);
            }

            DB::table('journal_details')->where('id', $journal_detail->id)->update(
                [
                    'ordering' => $new_ordering,
                ]
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
        Schema::table('journal_details', function (Blueprint $table) {
            $table->dropColumn('ordering');
            $table->dropColumn('timestamp');
        });
    }
}
