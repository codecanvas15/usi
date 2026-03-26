<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveStartDateFromClosingPeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('closing_periods', function (Blueprint $table) {
            $table->dropColumn('from_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('closing_periods', function (Blueprint $table) {
            $table->date('from_date')->nullable();
        });
    }
}
