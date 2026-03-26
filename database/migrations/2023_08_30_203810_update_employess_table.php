<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmployessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_ktp',
                'foto_id',
                'nomor_bpjs',
                'nomor_bpjs_tk',
                'bpjs_dues',
                'deposit_asset_employee',
                'deposit_asset_company',
                'exit_interview',
            ]);

            $table->string('religion', 100)->after('staff_type')->nullable();
            $table->string('weight', 24)->after('religion')->nullable();
            $table->string('height', 24)->after('weight')->nullable();
            $table->string('blood_type', 24)->after('height')->nullable();
            $table->string('hobby', 100)->after('blood_type')->nullable();
            $table->date('marriage_date')->after('hobby')->nullable();
            $table->string('vehicle', 100)->after('marriage_date')->nullable();
            $table->string('parents_phone_number', 24)->after('vehicle')->nullable();
            $table->string('file', 100)->after('parents_phone_number')->nullable();
            $table->text('reason_for_choosing_the_major')->after('file')->nullable();
            $table->text('thesis_topic')->after('reason_for_choosing_the_major')->nullable();
            $table->text('reason_for_not_passing')->after('thesis_topic')->nullable();
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
