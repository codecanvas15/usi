<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCompleteToItemCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_categories', function (Blueprint $table) {
            $table->boolean('is_complete')->default(true)->after('item_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_categories', function (Blueprint $table) {
            $table->dropColumn('is_complete');
        });
    }
}
