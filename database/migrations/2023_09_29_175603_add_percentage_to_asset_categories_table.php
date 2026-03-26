<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPercentageToAssetCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->decimal('percentage', 5, 2)->after('name')->nullable()->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->dropColumn('percentage');
        });
    }
}
