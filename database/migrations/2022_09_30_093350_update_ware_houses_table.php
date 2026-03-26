<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateWareHousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ware_houses', function (Blueprint $table) {
            $table->string('deskripsi')->nullable()->change();
            $table->string('jalan')->nullable()->change();
            $table->string('kota')->nullable()->change();
            $table->string('provinsi')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
