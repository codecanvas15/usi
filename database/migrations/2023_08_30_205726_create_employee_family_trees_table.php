<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeFamilyTreesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_family_trees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Employee::class);
            $table->string('type', 30);
            $table->string('relation', 100);
            $table->string('name', 100);
            $table->string('gender', 30);
            $table->string('birth_place', 100);
            $table->date('birth_date');
            $table->string('education', 30)->nullable();
            $table->string('last_position', 100)->nullable();
            $table->string('last_company', 100)->nullable();
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
        Schema::dropIfExists('employee_family_trees');
    }
}
