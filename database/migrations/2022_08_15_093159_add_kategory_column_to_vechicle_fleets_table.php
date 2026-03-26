<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKategoryColumnToVechicleFleetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vechicle_fleets', function (Blueprint $table) {
            $table->string('kategori', 50)->default('vendor')->after('type');
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
            //
        });
    }
}
