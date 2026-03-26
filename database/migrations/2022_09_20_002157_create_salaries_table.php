<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('payroll_period_id')->constrained('payroll_periods');
            $table->double('work_days');
            $table->double('work_days_total');
            $table->double('absences_days')->nullable();
            $table->double('base_salary', );
            $table->double('brutto_salary');
            $table->double('netto_salary');
            $table->double('allowance_total')->nullable();
            $table->double('deduction_total')->nullable();
            $table->enum('status', ['pending','approved'])->default('pending');
            $table->softDeletes();
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
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
