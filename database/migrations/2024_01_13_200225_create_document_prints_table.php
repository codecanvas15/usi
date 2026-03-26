<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentPrintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_prints', function (Blueprint $table) {
            $table->id();
            $table->string('model');
            $table->bigInteger('model_id');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->string('type');
            $table->string('title');
            $table->string('subtitle');
            $table->string('status')->default('pending');
            $table->text('reason');
            $table->text('link');
            $table->text('export_link');
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
        Schema::dropIfExists('document_prints');
    }
}
