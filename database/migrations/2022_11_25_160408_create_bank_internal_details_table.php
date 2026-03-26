<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankInternalDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_internal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_internal_id')->constrained('bank_internals');
            $table->string('name', 50)->nullable();
            $table->mediumText('description')->nullable();
            $table->double('credit_limit')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
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
        Schema::dropIfExists('bank_internal_details');
    }
}
