<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVehicleTypeToPurchaseTransportDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_transport_details', function (Blueprint $table) {
            $table->string('vehicle_type')->after('jumlah')->nullable();
            $table->string('vehicle_info')->after('vehicle_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_transport_details', function (Blueprint $table) {
            $table->dropColumn('vehicle_type');
            $table->dropColumn('vehicle_info');
        });
    }
}
