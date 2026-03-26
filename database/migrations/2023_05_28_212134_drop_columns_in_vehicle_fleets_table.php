<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsInVehicleFleetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicle_fleets', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_stnk',
                'nomor_rangka',
                'nomor_mesin',
                'plat_nomor',
            ]);
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
            //
        });
    }
}
