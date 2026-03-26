<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityCompleteInLaborDemandDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('labor_demand_details', function (Blueprint $table) {
            $table->integer('quantity_complete')->default(0)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('labor_demand_details', function (Blueprint $table) {
            $table->dropColumn('quantity_complete');
        });
    }
}
