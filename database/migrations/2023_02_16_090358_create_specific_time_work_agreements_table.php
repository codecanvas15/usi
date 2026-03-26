<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecificTimeWorkAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specific_time_work_agreements', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('branch_id')->nullable();

            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('division_id')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();

            $table->unsignedBigInteger('second_employee_id');
            $table->unsignedBigInteger('second_division_id')->nullable();
            $table->unsignedBigInteger('second_position_id')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();

            $table->string('code', 30);
            $table->date('date');
            $table->string('title');
            $table->string('work_agreement_type');
            $table->string('status');
            $table->longText('description');
            $table->string('attachment')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('branch_id', 'stwa_branch')->references('id')->on('branches');

            $table->foreign('employee_id', 'stwa_employee')->references('id')->on('employees');
            $table->foreign('division_id', 'stwa_division')->references('id')->on('divisions');
            $table->foreign('position_id', 'stwa_position')->references('id')->on('positions');

            $table->foreign('second_employee_id', 'stwa_second_employee')->references('id')->on('employees');
            $table->foreign('second_division_id', 'stwa_second_division')->references('id')->on('divisions');
            $table->foreign('second_position_id', 'stwa_second_position')->references('id')->on('positions');

            $table->foreign('created_by', 'stwa_created')->references('id')->on('users');
            $table->foreign('approved_by', 'stwa_approved')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('specific_time_work_agreements');
    }
}
