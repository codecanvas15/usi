<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentGapNoteToAccountPayableDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_payable_details', function (Blueprint $table) {
            $table->text('note')->change();
            $table->text('clearing_note')->nullable()->after('is_clearing');
            $table->text('exchange_rate_gap_note')->nullable()->after('exchange_rate_gap');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_payable_details', function (Blueprint $table) {
            $table->dropColumn('clearing_note');
            $table->dropColumn('exchange_rate_gap_note');
        });
    }
}
