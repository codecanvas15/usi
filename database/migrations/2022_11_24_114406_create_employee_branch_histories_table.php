<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeBranchHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_branch_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('causer_id')->constrained('employees');
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('from_branch_id')->constrained('branches');
            $table->foreignId('to_branch_id')->constrained('branches');
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
        Schema::dropIfExists('employee_branch_histories');
    }
}
