<?php

use App\Models\Employee;
use App\Models\Leave;
use App\Models\MassLeave;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMassLeaveDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mass_leave_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(MassLeave::class);
            $table->foreignIdFor(Leave::class);
            $table->foreignIdFor(Employee::class);
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
        Schema::dropIfExists('mass_leave_details');
    }
}
