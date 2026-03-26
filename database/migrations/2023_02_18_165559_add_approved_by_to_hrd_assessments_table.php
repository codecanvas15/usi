<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovedByToHrdAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hrd_assessments', function (Blueprint $table) {
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
        Schema::table('hrd_assessments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
        });
    }
}
