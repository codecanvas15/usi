<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssetDocumentTypeIdToAssets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->unsignedBigInteger('asset_document_type_id')->nullable()->after('asset_category_id');
            $table->foreign('asset_document_type_id', 'asset_id_to_asset_document')->references('id')->on('asset_document_types');
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
