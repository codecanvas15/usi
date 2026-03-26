<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitIdToPuchaseRequestDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_request_details', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Unit::class)->nullable()->after('purchase_request_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_request_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('unit_id');
        });
    }
}
