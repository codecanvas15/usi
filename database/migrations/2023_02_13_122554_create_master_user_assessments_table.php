<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterUserAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_user_assessments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('weight')->default(0);
            $table->enum('type', ['key behavioral competencies', 'key skill competencies'])->default('key behavioral competencies');
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
        Schema::dropIfExists('master_user_assessments');
    }
}
