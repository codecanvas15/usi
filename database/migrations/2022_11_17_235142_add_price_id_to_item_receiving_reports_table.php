<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceIdToItemReceivingReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_receiving_reports', function (Blueprint $table) {
            $table->foreignId('price_id')->after('branch_id')->nullable()->constrained('prices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_receiving_reports', function (Blueprint $table) {
            //
        });
    }
}
