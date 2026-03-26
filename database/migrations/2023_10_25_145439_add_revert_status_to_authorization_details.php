<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRevertStatusToAuthorizationDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('authorization_details', function (Blueprint $table) {
            $table->string('revert_status')->nullable();
            $table->string('void_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('authorization_details', function (Blueprint $table) {
            $table->dropColumn('revert_status');
            $table->dropColumn('void_status');
        });
    }
}
