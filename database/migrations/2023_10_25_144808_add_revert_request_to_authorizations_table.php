<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRevertRequestToAuthorizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('authorizations', function (Blueprint $table) {
            $table->string('revert_status')->nullable();
            $table->string('void_status')->nullable();
            $table->text('revert_or_void_necessary')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('authorizations', function (Blueprint $table) {
            $table->dropColumn('revert_status');
            $table->dropColumn('void_status');
            $table->dropColumn('revert_or_void_necessary');
        });
    }
}
