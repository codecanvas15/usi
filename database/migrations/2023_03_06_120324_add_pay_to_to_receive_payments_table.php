<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPayToToReceivePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receive_payments', function (Blueprint $table) {
            $table->string('pay_from')->nullable()->after('branch_id');
            $table->string('from_name')->nullable()->after('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receive_payments', function (Blueprint $table) {
            $table->dropColumn('pay_from');
            $table->dropColumn('from_name');
        });
    }
}
