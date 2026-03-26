<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApplicantStatusToOfferingLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offering_letters', function (Blueprint $table) {
            $table->string('applicant_status')->default('pending')->after('offering_letter');
            $table->text('applicant_status_reason')->nullable()->after('applicant_status');
            $table->dateTime('applicant_status_at')->nullable()->after('applicant_status_reason');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offering_letters', function (Blueprint $table) {
            $table->dropColumn('applicant_status');
            $table->dropColumn('applicant_status_reason');
            $table->dropColumn('applicant_status_at');
        });
    }
}
