<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLongLatToFleetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fleets', function (Blueprint $table) {
            $table->string('longitude', 24)->nullable()->after('year');
            $table->string('latitude', 24)->nullable()->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fleets', function (Blueprint $table) {
            //
        });
    }
}
