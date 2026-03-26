<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGiroNumberToFundSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fund_submissions', function (Blueprint $table) {
            $table->integer('is_giro')->after('currency_id')->nullable();
            $table->string('giro_number')->after('is_giro')->nullable();
            $table->date('giro_liquid_date')->after('giro_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fund_submissions', function (Blueprint $table) {
            $table->dropColumn('is_giro');
            $table->dropColumn('giro_number');
            $table->dropColumn('giro_liquid_date');
        });
    }
}
