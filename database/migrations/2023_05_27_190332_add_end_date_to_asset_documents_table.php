<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEndDateToAssetDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asset_documents', function (Blueprint $table) {
            $table->date('end_date')->after('effective_date')->nullable()->comment('Tanggal berakhirnya dokumen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asset_documents', function (Blueprint $table) {
            $table->dropColumn('end_date');
        });
    }
}
