<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeAccountCodeNullableInCoasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coas', function (Blueprint $table) {
            $table->string('account_code', 24)->nullable()->change();
            $table->string('account_type', 60)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coas', function (Blueprint $table) {
            $table->string('account_code', 24)->change();
            $table->string('account_type', 60)->change();
        });
    }
}
