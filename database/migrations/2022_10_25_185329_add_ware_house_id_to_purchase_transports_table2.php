<?php

use App\Models\WareHouse;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWareHouseIdToPurchaseTransportsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_transports', function (Blueprint $table) {
            $table->foreignIdFor(WareHouse::class)->nullable()->after('total')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_transports', function (Blueprint $table) {
            //
        });
    }
}
