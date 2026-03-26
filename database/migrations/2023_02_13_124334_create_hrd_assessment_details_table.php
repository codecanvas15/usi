<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrdAssessmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hrd_assessment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_hrd_assessment_id')->constrained('master_hrd_assessments');
            $table->mediumText('notes')->nullable();
            $table->integer('rating')->default(1);
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
        Schema::dropIfExists('hrd_assessment_details');
    }
}
