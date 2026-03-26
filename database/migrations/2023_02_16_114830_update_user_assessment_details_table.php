<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserAssessmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_assessment_details', function (Blueprint $table) {
            $table->foreignId('user_assessment_id')->after('id')->constrained('user_assessments');
            $table->foreignId('master_user_assessment_id')->constrained('master_user_assessments');
            $table->integer('rating')->default(1);
            $table->double('weight')->default(0);
            $table->enum('type', ['kbc', 'ksc'])->nullable()->default('kbc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_assessment_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_assessment_id');
            $table->dropConstrainedForeignId('master_user_assessment_id');
            $table->dropColumn('rating');
            $table->dropColumn('weight');
            $table->dropColumn('type');
        });
    }
}
