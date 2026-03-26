<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateHrdAssessmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hrd_assessment_details', function (Blueprint $table) {
            $table->foreignId('hrd_assessment_id')->after('id')->constrained('hrd_assessments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hrd_assessment_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hrd_assessment_id');
        });
    }
}
