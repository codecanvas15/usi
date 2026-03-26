<?php

use App\Models\VechicleFleet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateItemReceivingReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_po_tradings', function (Blueprint $table) {
            $table->string('loading_order', 60)->nullable()->after('ware_house_id');
            $table->decimal('kapasitas', 18, 4)->nullable()->after('loading_order');
            $table->foreignIdFor(VechicleFleet::class)->nullable()->constrained('vehicle_fleets')->after('kapasitas');
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
