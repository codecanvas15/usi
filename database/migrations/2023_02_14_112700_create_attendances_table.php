<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Employee::class)->constrained();
            $table->foreignIdFor(\App\Models\Branch::class)->nullable()->constrained();
            $table->date('date');
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->time('late')->nullable();
            $table->time('go_home_early')->nullable();
            $table->time('overtime')->nullable();
            $table->time('work_hours')->nullable();
            $table->time('attendance_hours')->nullable();
            $table->tinyText('description')->nullable();
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
        Schema::dropIfExists('attendances');
    }
}
