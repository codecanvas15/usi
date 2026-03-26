<?php

use App\Models\WareHouse;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWareHouseToItemReceivingPoTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_po_tradings', function (Blueprint $table) {
            $table->foreignIdFor(WareHouse::class)->nullable()->after('liter_obs')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_receiving_po_tradings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ware_house_id');
        });
    }
}
