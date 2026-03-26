<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUserIdForeignFromAllowanceSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allowance_salaries', function (Blueprint $table) {
            $table->dropForeign('allowance_salaries_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('employees');
        });

        Schema::table('deduction_salaries', function (Blueprint $table) {
            $table->dropForeign('deduction_salaries_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('employees');
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
            $table->dropForeign('allowance_salaries_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('deduction_salaries', function (Blueprint $table) {
            $table->dropForeign('deduction_salaries_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
}
