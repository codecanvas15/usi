<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('cabang')->nullable()->after('foto_sim');

            $table->string('divisi')->nullable()->after('cabang');
            $table->string('posisi')->nullable()->after('divisi');
            $table->string('employement_status')->nullable()->after('posisi');
            $table->string('jatah_cuti')->nullable()->after('employement_status');
            $table->date('join_date')->nullable()->after('jatah_cuti');
            $table->date('end_date')->nullable()->after('join_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('cabang');
            $table->dropColumn('divisi');
            $table->dropColumn('posisi');
            $table->dropColumn('employement_status');
            $table->dropColumn('jatah_cuti');
            $table->dropColumn('join_date');
            $table->dropColumn('end_date');
        });
    }
}
