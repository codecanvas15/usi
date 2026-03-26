<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVechicleFleetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vechicle_fleets', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('type', ['sepeda', 'sepeda motor', 'mobil', 'pick up', 'truck', 'elf',  'bus', 'lainnya']);
            $table->integer('kapasitas');
            $table->year('tahun_pembuatan');
            $table->string('nomor_lambung', 50);
            $table->string('nomor_stnk', 50);
            $table->string('nomor_rangka', 50);
            $table->string('nomor_mesin', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vechicle_fleets');
    }
}
