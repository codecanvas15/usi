<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDegreeAndEducationInEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('education_id')->nullable()->after('employment_status_id');
            $table->unsignedBigInteger('degree_id')->nullable()->after('education_id');

            $table->foreign('education_id')->references('id')->on('educations');
            $table->foreign('degree_id')->references('id')->on('degrees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('education_id');
            $table->dropConstrainedForeignId('degree_id');
        });
    }
}
