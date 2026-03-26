<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSpecificTimeWorkAgreementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('specific_time_work_agreements', function (Blueprint $table) {
            $table->dropForeign('stwa_second_employee');
            $table->dropColumn('second_employee_id');
            $table->enum('second_employee_type', ['new','existing'])->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_model')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('specific_time_work_agreements', function (Blueprint $table) {
            $table->unsignedBigInteger('second_employee_id');
            $table->foreign('second_employee_id', 'stwa_second_employee')->references('id')->on('employees');
            $table->dropColumn(['second_employee_type','reference_id','reference_model']);
        });
    }
}
