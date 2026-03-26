<?php

use App\Models\Branch;
use App\Models\LaborApplication;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferingLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offering_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->constrained();
            $table->foreignIdFor(LaborApplication::class)->constrained();
            $table->foreignId('created_by')->constrained('employees');
            $table->string('reference');
            $table->date('start_work_date');
            $table->string('work_location');
            $table->string('employment_status');
            $table->string('compensation');
            $table->double('salary');
            $table->double('allowance_salary'); // tunjangan
            $table->integer('leave_day'); // jatah cuti (hari)
            $table->double('holiday_allowance'); // thr
            $table->string('to_email');
            $table->date('due_date');
            $table->longText('offering_letter');
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
        Schema::dropIfExists('offering_letters');
    }
}
