<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentPrintApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_print_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_print_id')->references('id')->on('document_prints');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->string('status');
            $table->integer('level');
            $table->text('note')->nullable();
            $table->dateTime('status_at')->nullable();
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
        Schema::dropIfExists('document_print_approvals');
    }
}
