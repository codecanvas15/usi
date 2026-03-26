<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeRoleHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_role_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('causer_id')->constrained('employees');
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('from_role_id')->constrained('roles');
            $table->foreignId('to_role_id')->constrained('roles');
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
        Schema::dropIfExists('employee_role_histories');
    }
}
