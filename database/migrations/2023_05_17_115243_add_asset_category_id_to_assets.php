<?php

use App\Models\AssetCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssetCategoryIdToAssets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->foreignIdFor(AssetCategory::class, 'asset_category_id')->nullable()->after('division_id')->constrained('asset_categories');
            $table->dropColumn(['asset_type', 'group']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            //
        });
    }
}
