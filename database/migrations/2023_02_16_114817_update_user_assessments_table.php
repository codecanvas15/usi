<?php

use App\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_assessments', function (Blueprint $table) {
            $table->foreignIdFor(Branch::class)->nullable()->constrained();
            $table->foreignId('candidate')->constrained('employees');
            $table->string('reference');
            $table->date('assessment_date');
            $table->string('hiring_manager');
            $table->double('behavioral_rating');
            $table->double('skill_rating');
            $table->double('total_rating');
            $table->enum('recommend_status', ['y','r','x']);
            $table->mediumText('first_note')->nullable();
            $table->mediumText('second_note')->nullable();
            $table->mediumText('third_note')->nullable();
            $table->enum('approval_status', ['pending','approve','reject'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('employees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_assessments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
            $table->dropConstrainedForeignId('candidate');
            $table->dropColumn('reference');
            $table->dropColumn('assessment_date');
            $table->dropColumn('hiring_manager');
            $table->dropColumn('behavioral_rating');
            $table->dropColumn('skill_rating');
            $table->dropColumn('total_rating');
            $table->dropColumn('recommend_status');
            $table->dropColumn('first_note');
            $table->dropColumn('second_note');
            $table->dropColumn('third_note');
            $table->dropColumn('approval_status');
            $table->dropConstrainedForeignId('approved_by');
        });
    }
}
