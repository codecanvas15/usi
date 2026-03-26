<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionLetterEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission_letter_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Employee::class)->constrained();
            $table->foreignIdFor(\App\Models\Branch::class)->constrained();
            $table->string('letter_number', 32);
            $table->string('letter_type', 60);
            $table->string('letter_reason');
            $table->dateTime('letter_date_start')->nullable();
            $table->dateTime('letter_date_end')->nullable();
            $table->string('letter_status', 32);
            $table->string('letter_note')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('permission_letter_employees');
    }
}
