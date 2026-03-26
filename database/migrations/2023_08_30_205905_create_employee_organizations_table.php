<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_organizations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Employee::class);
            $table->string('name', 100);
            $table->string('place', 100);
            $table->string('position', 100);
            $table->date('from');
            $table->date('to')->nullable();
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
        Schema::dropIfExists('employee_organizations');
    }
}
