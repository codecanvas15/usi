<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeInsidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_insiders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Employee::class);
            $table->string('name', 100);
            $table->string('position', 100);
            $table->string('relation', 100);
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
        Schema::dropIfExists('employee_insiders');
    }
}
