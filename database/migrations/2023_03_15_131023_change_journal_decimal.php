<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeJournalDecimal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->decimal('credit_total_exchanged', 18, 2)->change();
            $table->decimal('debit_total_exchanged', 18, 2)->change();
        });

        Schema::table('journal_details', function (Blueprint $table) {
            $table->decimal('credit', 18, 2)->change();
            $table->decimal('debit', 18, 2)->change();
            $table->decimal('credit_exchanged', 18, 2)->change();
            $table->decimal('debit_exchanged', 18, 2)->change();
        });
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
