<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyOutgoingPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outgoing_payment_details', function (Blueprint $table) {
            $table->dropColumn('amount');
            $table->decimal('debit', 18, 3)->after('note');
            $table->decimal('credit', 18, 3)->after('debit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('outgoing_payment_details', function (Blueprint $table) {
            $table->dropColumn('debit');
            $table->dropColumn('credit');
        });
    }
}
