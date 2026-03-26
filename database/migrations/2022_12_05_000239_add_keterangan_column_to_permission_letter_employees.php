<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeteranganColumnToPermissionLetterEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permission_letter_employees', function (Blueprint $table) {
            $table->text('keterangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permission_letter_employees', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
}
