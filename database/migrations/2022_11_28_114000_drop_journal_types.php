<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropJournalTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('journal_type_id');

            $table->string('journal_type')->nullable()->after('exchange_rate');
        });
        Schema::drop('journal_types');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
