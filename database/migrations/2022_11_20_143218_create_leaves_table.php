<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Employee::class)->constrained();
            $table->foreignIdFor(\App\Models\Branch::class)->nullable()->constrained();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->integer('data')->nullable();
            $table->longText('note')->nullable();
            $table->string('status');

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
        Schema::dropIfExists('leaves');
    }
}
