<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFromNameToIncomingPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incoming_payments', function (Blueprint $table) {
            $table->string('from_name')->after('exchange_rate');
            $table->decimal('total', 18, 3)->after('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incoming_payments', function (Blueprint $table) {
            $table->dropColumn('from_name');
            $table->dropColumn('total');
        });
    }
}
