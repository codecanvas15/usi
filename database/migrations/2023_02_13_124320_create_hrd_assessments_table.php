<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrdAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hrd_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interviewer')->constrained('employees');
            $table->foreignId('candidate')->constrained('labor_applications');
            $table->foreignId('position')->constrained('positions');
            $table->string('reference');
            $table->date('assessment_date');
            $table->enum('assessment_status', ['y','r','n'])->default('y');
            $table->mediumText('notes')->nullable();
            $table->enum('approval_status', ['pending','approve','reject'])->default('pending');
            $table->foreignId('approved_by')->constrained('employees');
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
        Schema::dropIfExists('hrd_assessments');
    }
}
