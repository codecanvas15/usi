<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferenceInJournalDetais extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('journal_details', function (Blueprint $table) {
            $table->string('reference_model')->nullable()->after('item_receiving_report_coa_id');
            $table->unsignedBigInteger('reference_id')->nullable()->after('reference_model');
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
