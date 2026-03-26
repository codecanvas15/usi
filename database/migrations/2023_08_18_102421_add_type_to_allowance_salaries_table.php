<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToAllowanceSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allowance_salaries', function (Blueprint $table) {
            $table->string('type')->after('name')->nullable();
        });

        Schema::table('deduction_salaries', function (Blueprint $table) {
            $table->string('type')->after('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('allowance_salaries', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('deduction_salaries', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
