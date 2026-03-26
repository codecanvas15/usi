<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeEmergencyContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Employee::class)->constrained();
            $table->string('nama');
            $table->string('hubungan');
            $table->string('nomor_telepon');
            $table->string('alamat');
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
        Schema::dropIfExists('employee_emergency_contacts');
    }
}
