<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeFormalEducationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_formal_education', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Employee::class);
            $table->string('level', 30);
            $table->string('name', 100);
            $table->string('city', 100);
            $table->string('faculty', 100)->nullable();
            $table->string('major', 100)->nullable();
            $table->date('from');
            $table->date('to')->nullable();
            $table->string('gpa', 10)->nullable();
            $table->string('graduate', 30)->nullable();
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
        Schema::dropIfExists('employee_formal_education');
    }
}
