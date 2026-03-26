<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateRangeInDepreciations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('depreciations', function (Blueprint $table) {
            $table->date('from_date')->nullable()->after('date');
            $table->date('to_date')->nullable()->after('from_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('depreciations', function (Blueprint $table) {
            //
        });
    }
}
