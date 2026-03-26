<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateVehicleFleetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicle_fleets', function (Blueprint $table) {
            $table->string('nomor_bpkb')->nullable();
            $table->date('tanggal_stnk')->nullable();
            $table->string('nama_pemilik')->nullable();
            $table->mediumText('note')->nullable();
            $table->string('warna')->nullable();
            $table->string('document')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicle_fleets', function (Blueprint $table) {
            $table->dropColumn('nomor_bpkb');
            $table->dropColumn('tanggal_stnk');
            $table->dropColumn('nama_pemilik');
            $table->dropColumn('note');
            $table->dropColumn('warna');
            $table->dropColumn('document');
        });
    }
}
