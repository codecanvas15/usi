<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateItemTypesTablbe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_types', function (Blueprint $table) {
            $table->string('nama')->after('id');
            $table->dropColumn('purchase_item');
            $table->dropColumn('manufacture_item');
            $table->dropColumn('service');
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
