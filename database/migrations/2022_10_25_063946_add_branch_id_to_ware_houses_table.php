<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToWareHousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ware_houses', function (Blueprint $table) {
            $table->foreignId('branch_id')->after('id')->constrained('branches');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ware_houses', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
    }
}
