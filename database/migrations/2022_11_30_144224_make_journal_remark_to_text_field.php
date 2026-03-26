<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeJournalRemarkToTextField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('journal_details', function (Blueprint $table) {
            $table->text('remark')->nullable()->change();
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
            $table->string('remark', 255)->nullable()->change();
        });
    }
}
