<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGpEvaluationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gp_evaluation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gp_evaluation_id')->constrained('gp_evaluations');
            $table->foreignId('master_gp_evaluation_id')->constrained('master_gp_evaluations');
            $table->integer('score')->default(1);
            $table->mediumText('notes')->nullable();
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
        Schema::dropIfExists('gp_evaluation_details');
    }
}
