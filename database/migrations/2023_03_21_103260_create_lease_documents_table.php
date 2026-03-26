<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaseDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lease_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Lease::class)->constrained();
            $table->string('name');
            $table->date('transaction_date')->nullable();
            $table->date('effective_date')->nullable();
            $table->integer('due_date')->nullable();
            $table->string('audit_result')->nullable();
            $table->text('description')->nullable();
            $table->string('file')->nullable();
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
        Schema::dropIfExists('lease_documents');
    }
}
