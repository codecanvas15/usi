<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEduRecruitmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edu_recruitments', function (Blueprint $table) {
            $table->id();
            $table->string('pendidikan');
            $table->integer('tahun_masuk')->nullable();
            $table->integer('tahun_lulus')->nullable();
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
        Schema::dropIfExists('edu_recruitments');
    }
}
