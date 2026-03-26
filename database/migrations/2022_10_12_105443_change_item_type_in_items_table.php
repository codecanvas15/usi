<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeItemTypeInItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE items MODIFY COLUMN type ENUM('trading', 'general', 'service', 'transport')");

        // Schema::table('items', function (Blueprint $table) {
        //     //
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('items', function (Blueprint $table) {
        //     //
        // });
    }
}
