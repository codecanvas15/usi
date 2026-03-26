<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExchangeValueInJouralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->decimal('credit_total_exchanged', 18, 3)->default(0)->after('credit_total');
            $table->decimal('debit_total_exchanged', 18, 3)->default(0)->after('debit_total');
        });

        $journals = \App\Models\Journal::all();
        foreach ($journals as $journal) {
            $journal->credit_total_exchanged = $journal->credit_total * $journal->exchange_rate;
            $journal->debit_total_exchanged = $journal->debit_total * $journal->exchange_rate;
            $journal->save();
        }

        Schema::table('journal_details', function (Blueprint $table) {
            $table->decimal('credit_exchanged', 18, 3)->default(0)->after('credit');
            $table->decimal('debit_exchanged', 18, 3)->default(0)->after('debit');
        });

        $journalDetails = \App\Models\JournalDetail::all();
        foreach ($journalDetails as $journalDetail) {
            $journalDetail->credit_exchanged = $journalDetail->credit * $journalDetail->journal->exchange_rate;
            $journalDetail->debit_exchanged = $journalDetail->debit * $journalDetail->journal->exchange_rate;
            $journalDetail->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('journals', function (Blueprint $table) {
            //
        });
    }
}
