<?php

use App\Models\VechicleFleet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeVehicleIdNullableInDeliveryOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_order_details', function (Blueprint $table) {
            $table->foreignIdFor(VechicleFleet::class)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_order_details', function (Blueprint $table) {
            //
        });
    }
}
