<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeWorkExperiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_work_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Employee::class);
            $table->date('from');
            $table->date('to');
            $table->string('name', 100);
            $table->string('phone', 30);
            $table->unsignedInteger('employee_count');
            $table->string('type', 30);
            $table->string('position', 100);
            $table->string('beginning_position', 100);
            $table->string('end_position', 100);
            $table->string('supervisor', 100);
            $table->string('reason_for_leaving', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_work_experiences');
    }
}
