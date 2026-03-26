<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToOutgoingPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outgoing_payment_details', function (Blueprint $table) {
            $table->string('type')->after('credit')->nullable();
        });

        Schema::table('fund_submission_generals', function (Blueprint $table) {
            $table->string('type')->after('credit')->nullable();
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
            $table->dropColumn('type');
        });

        Schema::table('fund_submission_generals', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
