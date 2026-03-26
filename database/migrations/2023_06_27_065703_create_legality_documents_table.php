<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLegalityDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('legality_documents', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['company', 'finance']);
            $table->string('name');
            $table->date('transaction_date');
            $table->date('effective_date');
            $table->date('end_date');
            $table->integer('due_date');
            $table->text('description');
            $table->string('file');
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
        Schema::dropIfExists('legality_documents');
    }
}
