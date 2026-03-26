<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAuthorizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('authorizations', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('subject_id');
            $table->dropColumn('status');
            $table->bigInteger('model_id')->after('model');
            $table->text('link')->after('model_id')->nullable();
            $table->text('update_status_link')->after('link')->nullable();
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
            $table->dropColumn('model_id');
            $table->dropColumn('link');
            $table->dropColumn('update_status_link');
        });
    }
}
