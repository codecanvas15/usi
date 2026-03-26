<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOfferingLetter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offering_letters', function (Blueprint $table) {
            $table->dropColumn([
                'start_work_date',
                'work_location',
                'employment_status',
                'compensation',
                'allowance_salary',
                'leave_day',
                'holiday_allowance',
                'to_email',
                'due_date',
            ]);

            $table->string('nik')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
