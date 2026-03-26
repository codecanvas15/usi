<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropCandidateInUserAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_assessments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('candidate');
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
            $table->foreignId('candidate')->constrained('employees');
        });
    }
}
