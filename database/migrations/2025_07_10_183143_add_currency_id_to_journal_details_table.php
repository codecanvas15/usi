<?php

use App\Models\Currency;
use App\Models\JournalDetail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyIdToJournalDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('journal_details', function (Blueprint $table) {
            $table->foreignIdFor(Currency::class)->after('journal_id')->nullable()->constrained();
            $table->double('exchange_rate')->after('currency_id')->nullable();
        });

        $journal_details = JournalDetail::with('journal.currency')
            ->whereHas('journal')
            ->get();

        foreach ($journal_details as $journal_detail) {
            $journal_detail->currency_id = $journal_detail->journal->currency->id;
            $journal_detail->exchange_rate = $journal_detail->journal->exchange_rate;
            $journal_detail->save();
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
            $table->dropForeign('journal_details_currency_id_foreign');
            $table->dropColumn('currency_id');
            $table->dropColumn('exchange_rate');
        });
    }
}
