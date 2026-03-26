<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReturnedAmountInCashReturn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_advance_payments', function (Blueprint $table) {
            $table->decimal('returned_amount', 18, 3)->default(0)->after('created_by');
        });

        Schema::table('cash_advance_receives', function (Blueprint $table) {
            $table->decimal('returned_amount', 18, 3)->default(0)->after('created_by');
        });

        Schema::table('cash_bonds', function (Blueprint $table) {
            $table->decimal('returned_amount', 18, 3)->default(0)->after('reject_reason');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_advance_payment_details', function (Blueprint $table) {
            $table->dropColumn('returned_amount');
        });

        Schema::table('cash_advance_receive_details', function (Blueprint $table) {
            $table->dropColumn('returned_amount');
        });

        Schema::table('cash_bond_details', function (Blueprint $table) {
            $table->dropColumn('returned_amount');
        });
    }
}
