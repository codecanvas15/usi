<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeVechicleTypeToStringinVechicleFleetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vechicle_fleets', function (Blueprint $table) {
            $table->dropColumn('kapasitas');

            $table->integer('kapasitas_penumpang')->after('type')->nullable();
            $table->integer('kapasitas_angkut')->after('kapasitas_penumpang')->nullable();
            $table->string('type')->change();
            $table->string('plat_nomor', 24)->unique()->nullable()->after('nomor_mesin');
            $table->softDeletes()->after('plat_nomor');
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
            $table->enum('type', ['sepeda', 'sepeda motor', 'mobil', 'pick up', 'truck', 'elf',  'bus', 'lainnya'])->change();
            $table->dropSoftDeletes();
        });
    }
}
