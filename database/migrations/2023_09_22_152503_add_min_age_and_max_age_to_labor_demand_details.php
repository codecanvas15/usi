<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinAgeAndMaxAgeToLaborDemandDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('labor_demand_details', function (Blueprint $table) {
            $table->renameColumn('age', 'min_age');
            $table->integer('max_age')->after('age')->nullable();
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
            //
        });
    }
}
