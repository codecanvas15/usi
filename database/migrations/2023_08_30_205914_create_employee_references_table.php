<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_references', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Employee::class);
            $table->string('name', 100);
            $table->string('address', 100);
            $table->string('phone', 100);
            $table->string('company', 100);
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
        Schema::dropIfExists('employee_references');
    }
}
