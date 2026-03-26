<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLpbCoaIdToJournalDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('journal_details', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\ItemReceivingReportCoa::class)->nullable()->after('coa_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('journal_details', function (Blueprint $table) {
            //
        });
    }
}
