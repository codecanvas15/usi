<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnToItemReceivingReportCoasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_report_coas', function (Blueprint $table) {
            $table->string('reference_model')->after('type');
            $table->unsignedBigInteger('reference_id')->after('reference_model');

            $table->dropColumn('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_receiving_report_coas', function (Blueprint $table) {
            //
        });
    }
}
