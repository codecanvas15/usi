<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalToOutgoingPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outgoing_payments', function (Blueprint $table) {
            $table->string('to_name')->after('exchange_rate');
            $table->decimal('total', 18, 3)->after('to_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('outgoing_payments', function (Blueprint $table) {
            $table->dropColumn('to_name');
            $table->dropColumn('total');
        });
    }
}
