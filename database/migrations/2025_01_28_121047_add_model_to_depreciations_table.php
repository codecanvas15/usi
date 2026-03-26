<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModelToDepreciationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('depreciations', function (Blueprint $table) {
            $table->string('model')->nullable()->after('asset_id');
            $table->bigInteger('model_id')->nullable()->after('model');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('depreciations', function (Blueprint $table) {
            $table->dropColumn('model');
            $table->dropColumn('model_id');
        });
    }
}
