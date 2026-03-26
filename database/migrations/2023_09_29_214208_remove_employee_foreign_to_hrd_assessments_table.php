<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveEmployeeForeignToHrdAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hrd_assessments', function (Blueprint $table) {
            $table->dropForeign('hrd_assessments_approved_by_foreign');
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
            //
        });
    }
}
