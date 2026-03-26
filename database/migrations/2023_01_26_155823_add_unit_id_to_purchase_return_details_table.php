<?php

use App\Models\Unit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitIdToPurchaseReturnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_return_details', function (Blueprint $table) {
            $table->foreignIdFor(Unit::class)->after('item_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_return_details', function (Blueprint $table) {
            $table->dropColumn('unit_id');
        });
    }
}
