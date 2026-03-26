<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaborApplicationEmergencyContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labor_application_emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('labor_application_id');
            $table->foreign('labor_application_id', 'la_parent_to_la_emergency')->references('id')->on('labor_applications');
            $table->string('name');
            $table->string('relationship');
            $table->string('phone');
            $table->string('address');
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
        Schema::dropIfExists('labor_application_emergency_contacts');
    }
}
