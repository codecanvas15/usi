<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropKapasitasPenumpangFromVechicleFleetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vechicle_fleets', function (Blueprint $table) {
            $table->dropColumn('kapasitas_penumpang');
            $table->renameColumn('kapasitas_angkut', 'kapasitas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vechicle_fleets', function (Blueprint $table) {
            $table->renameColumn('kapasitas', 'kapasitas_angkut');
            $table->integer('kapasitas_penumpang')->nullable();
        });
    }
}
