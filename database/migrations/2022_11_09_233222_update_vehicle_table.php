<?php

use App\Models\Fleet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateVehicleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicle_fleets', function (Blueprint $table) {
            $table->foreignIdFor(Fleet::class)->nullable()->after('id')->constrained();
            $table->dropColumn(['nomor_lambung', 'kapasitas', 'tahun_pembuatan']);
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
