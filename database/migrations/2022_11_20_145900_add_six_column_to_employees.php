<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSixColumnToEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->decimal('bpjs_dues', 18, 2)->nullable();
            $table->text('deposit_asset_employee')->nullable();
            $table->text('deposit_asset_company')->nullable();
            $table->string('exit_interview')->nullable();
            $table->string('employee_status')->nullable();
            $table->integer('leave')->nullable();
            $table->dropColumn('jatah_cuti');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('bpjs_dues');
            $table->dropColumn('deposit_asset_employee');
            $table->dropColumn('deposit_asset_company');
            $table->dropColumn('exit_interview');
            $table->dropColumn('employee_status');
            $table->dropColumn('leave');
        });
    }
}
