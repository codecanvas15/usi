<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Employee::class);
            $table->string('language', 100);
            $table->string('speak', 30);
            $table->string('listening', 30);
            $table->string('write', 30);
            $table->string('read', 30);
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
        Schema::dropIfExists('employee_languages');
    }
}
