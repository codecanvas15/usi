<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPercentageToAllowanceSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allowance_salaries', function (Blueprint $table) {
            $table->decimal('percentage', 18, 2)->after('name')->nullable();
        });

        Schema::table('deduction_salaries', function (Blueprint $table) {
            $table->decimal('percentage', 18, 2)->after('name')->nullable();
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
            $table->dropColumn('percentage');
        });

        Schema::table('deduction_salaries', function (Blueprint $table) {
            $table->dropColumn('percentage');
        });
    }
}
