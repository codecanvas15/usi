<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleToModelAuthorizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('model_authorizations', function (Blueprint $table) {
            $table->string('role')->after('minimum_value')->default('menyetujui');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('model_authorizations', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
}
